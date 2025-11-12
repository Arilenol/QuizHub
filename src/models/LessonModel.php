<?php
class HomeModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère une leçon par son ID
     *
     * @param int $id Identifiant de la leçon selectionnée
     * @return array|false Tableau associatif de la leçon, ou false si non trouvé
     */
    public function getById($id) : array|false {
        $stmt = $this->db->prepare("SELECT * FROM quiz WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
