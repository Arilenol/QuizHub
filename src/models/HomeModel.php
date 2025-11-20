<?php
class HomeModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    private function getAll() : array {
        $stmt = $this->db->query("SELECT * FROM quiz");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un quiz par son ID
     *
     * @param int $id Identifiant du quiz
     * @return array|false Tableau associatif du quiz, ou false si non trouvé
     */
    public function getById($id) : array|false {
        $stmt = $this->db->prepare("SELECT * FROM quiz WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Récupère toutes les informations des quiz avec l'utilisateur associé
     *
     * Inclut : genre, date, nom d'utilisateur, titre, difficulté, description.
     *
     * @return array Tableau associatif de quiz enrichis avec info utilisateur
     */
    public function getAllInfo() : array {
        $stmt = $this->db->query("
            SELECT 
                q.id,
                q.genre, 
                q.date, 
                u.username AS user_name, 
                GROUP_CONCAT(c.categorieName) AS categories,
                q.title, 
                q.difficulty, 
                q.description,
                q.nbjaime,
                q.nbjaimepas 
            FROM quiz q
            JOIN users u ON u.id = q.user_id
            JOIN categorie_quiz cq ON cq.quiz_id = q.id
            JOIN categories c ON c.id = cq.category_id
            GROUP BY q.id
        ");

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // transformation des catégories en tableau
        foreach ($results as &$row) {
            $row['categories'] = explode(',', $row['categories']);
        }

        return $results;
    }
}
