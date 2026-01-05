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
     * Cette méthode interroge la base de données afin de retrouver
     * les informations d’un utilisateur à partir de son id.  
     * Si un utilisateur correspond, ses données sont retournées sous forme de tableau
     * associatif. Sinon, la méthode retourne false.
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
     * Cette méthode exécute une requête SQL afin de déterminer combien de
     * quiz ont été créés par un utilisateur spécifique en fonction de son ID.
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
     * Cette méthode exécute une requête SQL afin de déterminer combien de
     * leçons ont été créés par un utilisateur spécifique en fonction de son ID.
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
     * Cette méthode exécute une requête SQL afin de déterminer combien de
     * quiz ont été jouées par un utilisateur spécifique en fonction de son ID.
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
     * Compte le nombre de parties jouées (quiz)
     *
     * Cette méthode exécute une requête SQL afin de déterminer combien de
     * quiz ont été jouées par un utilisateur spécifique en fonction de son ID.
     *
     * @param int $id|string L'identifiant de l'utilisateur.
     *
     * @return int Le nombre total de quiz jouées par l'utilisateur.
     */
    public function getQuizPlayed(int|string $id): array|false
    {
        $stmt = $this->db->prepare("SELECT 
                q.id,
                q.genre, 
                q.date, 
                u.username AS user_name,
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
     * Cette méthode retourne un tableau contenant tous les enregistrements de la table `amis`
     * où l'utilisateur est soit `user1_id` soit `user2_id`.
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
            END AS friend_email
        FROM amis a
        JOIN users u1 ON u1.id = a.user1_id
        JOIN users u2 ON u2.id = a.user2_id
        WHERE a.user1_id = :id OR a.user2_id = :id
    ");
        $stmt->execute([":id" => $id]);
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
}
