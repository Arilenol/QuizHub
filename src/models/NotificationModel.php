<?php

require_once ROOT . '/src/models/LogModel.php';
require_once ROOT . '/config/config.php';

class NotificationModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère toutes les demandes d'ami reçues par un utilisateur.
     *
     * @param int|string $receveurId  Identifiant de l'utilisateur recevant les demandes.
     *
     * @return array  Un tableau de tableaux associatifs contenant les informations des demandeurs.
     *                Chaque élément contient au moins `demandeur_id`, `receveur_id`, et éventuellement d'autres colonnes.
     *                Tableau vide si aucune demande.
     */
    public function getFriendRequestsReceived(int|string $receveurId): array
    {

        $stmt = $this->db->prepare("
        SELECT da.*, u.username, u.email, u.id
        FROM demandeAmi da
        JOIN users u ON da.demandeur_id = u.id
        WHERE da.receveur_id = ?
        ");
        $stmt->execute([$receveurId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    /**
     * Crée une demande d'ami d'un utilisateur connecté vers un autre utilisateur.
     *
     * Cette méthode insère une ligne dans la table `demandeAmi` avec l'identifiant
     * du demandeur (l'utilisateur connecté via $_SESSION['id']) et celui du receveur.
     * Elle vérifie d'abord qu'aucune demande identique n'existe déjà pour éviter les doublons.
     *
     * @param string|int $receveurId  Identifiant de l'utilisateur à qui envoyer la demande.
     *
     * @return bool  Retourne true si la demande a été créée, false si elle existe déjà ou en cas d'erreur.
     */
    public function createFriendRequest(int|string $receveurEmail): bool
    {
        $fetchId = new LogModel($this->db);
        $receveur = $fetchId->getUserByEmail($receveurEmail);

        if ($receveur === false) {
            return false;
        }

        // Empêcher l'auto-demande
        if ($_SESSION['id'] === $receveur['id']) {
            return false;
        }

        // Vérifie si une demande existe déjà (dans les deux sens)
        $checkStmt = $this->db->prepare("
        SELECT COUNT(*) 
        FROM demandeAmi
        WHERE 
            (demandeur_id = :me AND receveur_id = :other)
            OR
            (demandeur_id = :other AND receveur_id = :me)
    ");

        $checkStmt->execute([
            ':me'    => $_SESSION['id'],
            ':other' => $receveur['id']
        ]);

        if ($checkStmt->fetchColumn() > 0) {
            return false;
        }

        // Vérifie s’ils sont déjà amis
        $friendCheck = $this->db->prepare("
        SELECT COUNT(*) 
        FROM amis
        WHERE 
            (user1_id = :me AND user2_id = :other)
            OR
            (user1_id = :other AND user2_id = :me)
    ");

        $friendCheck->execute([
            ':me'    => $_SESSION['id'],
            ':other' => $receveur['id']
        ]);

        if ($friendCheck->fetchColumn() > 0) {
            return false;
        }

        // Insérer la demande
        $stmt = $this->db->prepare("
        INSERT INTO demandeAmi (demandeur_id, receveur_id)
        VALUES (:me, :other)
    ");

        return $stmt->execute([
            ':me'    => $_SESSION['id'],
            ':other' => $receveur['id']
        ]);
    }

    // Vérifie s’ils sont déjà amis
    public function hasFriend(int|string $receveur): bool
    {
        $friendCheck = $this->db->prepare("
            SELECT COUNT(*) 
            FROM amis
            WHERE 
                (user1_id = :me AND user2_id = :other)
                OR
                (user1_id = :other AND user2_id = :me)
        ");

        $friendCheck->execute([
            ':me'    => $_SESSION['id'],
            ':other' => $receveur
        ]);

        if ($friendCheck->fetchColumn() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Ajoute un utilisateur en ami.
     *
     * @param int|string $id Identifiant de l'utilisateur à ajouter en ami.
     * @return bool True si l'amitié a été ajoutée et la demande supprimée, false sinon.
     */
    public function addFriend(string|int $id): bool
    {
        // Normaliser l'ordre pour éviter doublons
        $user1 = min($_SESSION['id'], $id);
        $user2 = max($_SESSION['id'], $id);

        $stmt = $this->db->prepare("
        INSERT INTO amis(user1_id, user2_id)
        VALUES (?, ?)
    ");

        if ($stmt->execute([$user1, $user2])) {
            return $this->deleteFriendRequest($id);
        }

        return false;
    }

    /**
     * Supprime une demande d'ami reçue.
     *
     * @param int|string $id Identifiant de l'utilisateur ayant fait la demande.
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function deleteFriendRequest(string|int $id): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM demandeAmi
        WHERE demandeur_id = ? AND receveur_id = ?
    ");

        return $stmt->execute([$id, $_SESSION['id']]);
    }

    /**
     * Crée une notification pour un utilisateur.
     *
     * @param int|string $user_id      ID de l'utilisateur qui reçoit la notification
     * @param string $type             Type de notification (ex: 'disponibilite_change')
     * @param string $message          Message de la notification
     * @param int|string $contenu_id   ID du contenu (quiz ou leçon)
     * @param string $contenu_type     Type du contenu ('quiz' ou 'lesson')
     *
     * @return bool  True si la notification a été créée, false sinon
     */
    public function createNotification(int|string $user_id, string $type, string $message, int|string $contenu_id = null, string $contenu_type = null): bool
    {
        $stmt = $this->db->prepare("
        INSERT INTO notifications (user_id, type, message, contenu_id, contenu_type)
        VALUES (:user_id, :type, :message, :contenu_id, :contenu_type)
    ");

        return $stmt->execute([
            ':user_id'      => $user_id,
            ':type'         => $type,
            ':message'      => $message,
            ':contenu_id'   => $contenu_id,
            ':contenu_type' => $contenu_type
        ]);
    }

    /**
     * Récupère toutes les notifications d'un utilisateur.
     *
     * @param int|string $user_id  ID de l'utilisateur
     *
     * @return array  Tableau des notifications non lues
     */
    public function getNotifications(int|string $user_id): array
    {
        $stmt = $this->db->prepare("
        SELECT * FROM notifications
        WHERE user_id = ? AND is_read = 0
        ORDER BY date_creation DESC
    ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Supprime une notification.
     *
     * @param int|string $notification_id  ID de la notification à supprimer
     * @param int|string $user_id          ID de l'utilisateur (pour vérifier les droits)
     *
     * @return bool  True si la notification a été supprimée, false sinon
     */
    public function deleteNotification(int|string $notification_id, int|string $user_id): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM notifications
        WHERE id = ? AND user_id = ?
    ");

        return $stmt->execute([$notification_id, $user_id]);
    }
}
