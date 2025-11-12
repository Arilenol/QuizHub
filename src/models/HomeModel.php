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
    public function getAllInfo() : array{
        $stmt = $this->db->query("
            SELECT 
                q.id,
                q.genre, 
                q.date, 
                u.username AS user_name, 
                q.title, 
                q.difficulty, 
                q.description 
            FROM quiz q
            JOIN users u ON u.id = q.user_id
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
