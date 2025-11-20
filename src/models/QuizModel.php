<?php
class QuizModel {
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
     * Récupère les ID des questions de la flashcard par l'id Flashcard 
     *
     * @param int $id Identifiant de la flashcard
     * @return array|false Tableau de tous les ID des questions/réponses de la flashcard
     */
    public function getFlashCardById($id) : array|false {
        $stmt = $this->db->prepare("SELECT id FROM carte WHERE quiz_id = ? ORDER BY id");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_COLUMN,0);
    }
    
}