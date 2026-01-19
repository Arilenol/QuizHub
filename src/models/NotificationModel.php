<?php

require_once ROOT . '/src/models/LogModel.php';

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
     * Cette méthode interroge la table `demandeAmi` pour obtenir toutes les demandes
     * d'ami dont l'utilisateur est le receveur. Elle retourne les informations
     * du demandeur pour chaque demande.
     *
     * @param int|string $receveurId  Identifiant de l'utilisateur recevant les demandes.
     *
     * @return array  Un tableau de tableaux associatifs contenant les informations des demandeurs.
     *                Chaque élément contient au moins `demandeur_id`, `receveur_id`, et éventuellement d'autres colonnes.
     *                Tableau vide si aucune demande.
     */
    public function getFriendRequestsReceived(int|string $receveurId): array
    {

        // $stmt2 = $this->db->prepare("
        // DELETE FROM amis
        // WHERE user1_id = ? AND user2_id = ?
        // ");
        // 
        // $stmt2->execute([12, 13]);

        // $stmt2 = $this->db->prepare("
        // INSERT INTO demandeAmi (demandeur_id,receveur_id)
        // VALUES (?,?)
        // ");

        // $stmt2->execute([1, 13]);

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

    public function deleteFriendRequest(string|int $id): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM demandeAmi
        WHERE demandeur_id = ? AND receveur_id = ?
    ");

        return $stmt->execute([$id, $_SESSION['id']]);
    }
}
