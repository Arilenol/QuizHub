<?php
class HomeModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM quiz");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un quiz par son ID
     *
     * @param int $id Identifiant du quiz
     * @return array|false Tableau associatif du quiz, ou false si non trouvé
     */
    public function getById($id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM quiz WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les informations des quiz avec l'utilisateur associé
     *
     * Inclut : genre, date, nom d'utilisateur, titre, difficulté, description.
     *
     * @return array Tableau associatif de quiz enrichis avec info utilisateur
     */
    public function getAllInfo(): array
    {
        $stmt = $this->db->query("
            SELECT 
                q.id,
                q.genre, 
                q.date, 
                u.username AS user_name,
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
            ORDER BY nbjaime DESC;
        ");

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // transformation des catégories en tableau
        foreach ($results as &$row) {
            $row['categories'] = explode(',', $row['categories']);
        }

        return $results;
    }


    /**
     * Récupère toutes les informations des quiz avec l'utilisateur associé
     *
     * Inclut : genre, date, nom d'utilisateur, titre, difficulté, description.
     *
     * @return array Tableau associatif de quiz enrichis avec info utilisateur
     */
    public function getAllCreationsByUser(string|int $id): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                q.id,
                q.genre, 
                q.date, 
                u.username AS user_name,
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
            WHERE u.id = ?
        ");

        $stmt->execute([$id]);
        $resultsBis = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // transformation des catégories en tableau
        // foreach ($resultsBis as &$row) {
        //     $row['categories'] = explode(',', $row['categories']);
        // }
        // var_dump($resultsBis);
        return $resultsBis;
    }

    /**
     * Récupère toutes les informations des quiz avec l'utilisateur associé
     *
     * Inclut : genre, date, nom d'utilisateur, titre, difficulté, description.
     *
     * @return array Tableau associatif de quiz enrichis avec info utilisateur
     */
    public function getAllNewCreations(): array
    {
        $stmt = $this->db->query("
            SELECT 
                q.id,
                q.genre, 
                q.date, 
                u.username AS user_name,
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
            ORDER BY q.date DESC;
        ");

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // transformation des catégories en tableau
        foreach ($results as &$row) {
            $row['categories'] = explode(',', $row['categories']);
        }

        return $results;
    }
}
