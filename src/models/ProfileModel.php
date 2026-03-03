<?php
class ProfileModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère toutes les informations liées à un utilisateur via son id.
     *
     * @param string $id L’id de l’utilisateur recherché.
     * 
     * @return array|false Un tableau associatif contenant les informations de l’utilisateur
     *                     (id, username, email, password, etc.) ou false si aucun
     *                     utilisateur ne correspond.
     */
    public function getCredentials(string $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Compte le nombre de créations (quiz) appartenant à un utilisateur.
     *
     * @param int $id L'identifiant de l'utilisateur.
     *
     * @return int Le nombre total de quiz créés par l'utilisateur.
     */
    public function getQuizCreated(int $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(id) AS total FROM quiz WHERE user_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    /**
     * Compte le nombre de créations (leçons) appartenant à un utilisateur.
     *
     * @param int $id L'identifiant de l'utilisateur.
     *
     * @return int Le nombre total de leçons créés par l'utilisateur.
     */
    public function getLessonsCreated(int $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(id) AS total FROM lecon WHERE user_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    /**
     * Compte le nombre de parties jouées (quiz)
     *
     * @param int $id|string L'identifiant de l'utilisateur.
     *
     * @return int Le nombre total de quiz jouées par l'utilisateur.
     */
    public function getGamesNumber(int|string $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(id) AS resultat FROM resultat WHERE user_id = ?");
        $stmt->execute([$id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['resultat'];
    }


    /**
     * Récupère la liste des quiz joués par un utilisateur avec leurs détails.
     *
     * @param int|string $id L'identifiant de l'utilisateur.
     *
     * @return array|false Un tableau de quiz joués avec leurs détails ou false si aucun.
     */
    public function getQuizPlayed(int|string $id): array|false
    {
        $stmt = $this->db->prepare("SELECT 
                q.id,
                q.genre, 
                q.date, 
                u.username AS user_name,
                r.score as note,
                r.dateRealisation AS dateRealisation,
                (
                    SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                    FROM categorie_quiz cq
                    JOIN categories c ON c.id = cq.category_id
                    WHERE cq.quiz_id = q.id
                ) AS categories,
                q.title, 
                q.difficulty, 
                q.description,
                (SELECT COUNT(*) FROM likes l WHERE l.quiz_id = q.id) AS nbjaime,
                (SELECT COUNT(*) FROM dislikes d WHERE d.quiz_id = q.id) AS nbjaimepas
            FROM quiz q
            JOIN users u ON u.id = q.user_id
            JOIN resultat r on r.quiz_id = q.id
            WHERE r.user_id = ?
            ORDER BY r.dateRealisation DESC");
        $stmt->execute([$id]);


        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($results as &$row) {
            $row['categories'] = explode(',', $row['categories']);
        }

        return $results;
    }

    /**
     * Récupère tous les amis d’un utilisateur.
     *
     * @param int|string $id L'identifiant de l'utilisateur.
     *
     * @return array|false Tableau de tous les amis ou false si aucun ami trouvé.
     */
    public function getFriends(int|string $id): array|false
    {
        $stmt = $this->db->prepare("
        SELECT
            CASE 
                WHEN a.user1_id = :id THEN u2.id
                ELSE u1.id
            END AS friend_id,
            CASE 
                WHEN a.user1_id = :id THEN u2.username
                ELSE u1.username
            END AS friend_name,
            CASE 
                WHEN a.user1_id = :id THEN u2.email
                ELSE u1.email
            END AS friend_email,
            CASE 
                WHEN a.user1_id = :id THEN u2.picture_path
                ELSE u1.picture_path
            END AS friend_picture

        FROM amis a
        JOIN users u1 ON u1.id = a.user1_id
        JOIN users u2 ON u2.id = a.user2_id
        WHERE a.user1_id = :id OR a.user2_id = :id
    ");

        $stmt->execute([':id' => $id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return !empty($results) ? $results : false;
    }


    /**
     * Met à jour le nom d'utilisateur d'un utilisateur.
     *
     * @param string     $username Nouveau nom d'utilisateur
     * @param int|string $id       Identifiant de l'utilisateur
     *
     * @return bool Retourne true si la mise à jour a réussi, false sinon
     */
    public function saveUsername(string $username, string|int $id): bool
    {
        if (empty($username)) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE users SET username = ? WHERE id = ?");
        return $stmt->execute([trim($username), $id]);
    }

    /**
     * Met à jour la description d'un utilisateur.
     *
     * @param string     $description Nouvelle description
     * @param int|string $id          Identifiant de l'utilisateur
     *
     * @return bool Retourne true si la mise à jour a réussi, false sinon
     */
    public function saveDescription(string $description, string|int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET description = ? WHERE id = ?");
        return $stmt->execute([trim($description), $id]);
    }
    /**
     * Met à jour le mot de passe d'un utilisateur.
     * Le mot de passe est automatiquement hashé avant l'enregistrement.
     *
     * @param string     $password Nouveau mot de passe en clair
     * @param int|string $id       Identifiant de l'utilisateur
     *
     * @return bool Retourne true si la mise à jour a réussi, false sinon
     */
    public function savePassword(string $password, string|int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([password_hash(trim($password), PASSWORD_DEFAULT), $id]);
    }

    /**
     * Met à jour le chemin de l'image de profil d'un utilisateur.
     *
     * @param string     $pathForDb Chemin relatif de l'image à enregistrer en base
     * @param int|string $id        Identifiant de l'utilisateur
     *
     * @return bool Retourne true si la mise à jour a réussi, false sinon
     */
    public function savePicture(string $pathForDb, string|int $id): bool
    {
        $stmt = $this->db->prepare("
                UPDATE users 
                SET picture_path = :path 
                WHERE id = :id
            ");

        return $stmt->execute([
            'path' => $pathForDb,
            'id'   => $id
        ]);
    }

    /**
     * Met à jour l'adresse email d'un utilisateur.
     *
     * @param string     $email Nouvelle adresse email
     * @param int|string $id    Identifiant de l'utilisateur
     *
     * @return bool Retourne true si la mise à jour a réussi, false sinon
     */
    public function saveEmail(string $email, string|int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE users SET email = ? WHERE id = ?");
        return $stmt->execute([trim($email), $id]);
    }

    /**
     * Supprime un ami à partir de son id
     *
     * @param int|string $idDeleteFriend    Identifiant de l'utilisateur ami à supprimer
     * @param int|string $idCurrentSession    Identifiant de l'utilisateur de la session
     *
     * @return bool Retourne true si la suppresion a réussi, false sinon
     */
    public function deleteFriend(int|string $friendId, int|string $userId): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM amis
        WHERE 
            (user1_id = :user AND user2_id = :friend)
            OR
            (user1_id = :friend AND user2_id = :user)
    ");

        $stmt->execute([
            ':user'   => $userId,
            ':friend' => $friendId
        ]);

        return $stmt->rowCount() > 0;
    }


    /**
     * Supprime une leçon par son id.
     *
     * @param int|string $idToDelete Identifiant de la leçon à supprimer
     *
     * @return bool Retourne true si la suppression a réussi, false sinon
     */
    public function deleteLesson(int|string $idToDelete): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM lecon
        WHERE id = ?
    ");

        $stmt->execute([$idToDelete]);

        $stmt = $this->db->prepare("
            DELETE FROM categorie_lecon
            WHERE lesson_id = ?
    ");

        $stmt->execute([$idToDelete]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Supprime un quiz appartenant à l'utilisateur.
     *
     * @param int|string $idToDelete Identifiant du quiz à supprimer
     *
     * @return bool Retourne true si la suppression a réussi, false sinon
     */
    public function deleteQuiz(int|string $idToDelete): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM categorie_quiz
            WHERE quiz_id = ?
        ");

        $stmt->execute([$idToDelete]);

        $stmt = $this->db->prepare("
            DELETE FROM quiz
            WHERE id = ?
    ");

        $stmt->execute([$idToDelete]);


        $stmt = $this->db->prepare("
            UPDATE lecon SET quiz_id = null
            WHERE quiz_id = ?
    ");

        $stmt->execute([$idToDelete]);

        return $stmt->rowCount() > 0;
    }
}
