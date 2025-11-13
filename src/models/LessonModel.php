<?php
class LessonModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère une leçon par son ID
     *
     * @param int $id Identifiant de la leçon
     * @return array|false Tableau associatif de la leçon, ou false si non trouvé
     */
    public function getLesson($id) : array|false {
        $stmt = $this->db->prepare("SELECT * FROM lecon WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Récupère les différentes parties de la leçon par son ID
     *
     * @param int $id Identifiant de la leçon
     * @return array|false Tableau associatif des parties de la leçon, ou false si non trouvé
     */
    public function getPart(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT title, content,id
            FROM partie
            WHERE lecon_id = ?
            ORDER BY numeroPartie ASC
        ");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les différentes parties de la leçon par son ID
     *
     * @param int $id Identifiant de la leçon
     * @return array|false Tableau associatif des parties de la leçon, ou false si non trouvé
     */
    public function getExemple(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT consigne, reponse, numeroExemple, partie_id
            FROM exemple
            WHERE partie_id = ?
            ORDER BY numeroExemple ASC
        ");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle leçon
     *
     * @param string $title Titre de la leçon
     * @param string $description Description de la leçon
     * @param int $user_id Identifiant de l'utilisateur créateur
     * @param int $quizId Id du quiz auquel il se réfère
     * @return int|false Retourne l'ID de la leçon créée, ou false en cas d’échec
     */
    public function createLesson(string $title, string $description, int $user_id, ?int $quizId = null): int|false {
        try {

            $stmt = $this->db->prepare("
                INSERT INTO lesson (quiz_id, title, description, user_id)
                VALUES (:quiz_id, :title, :description, :user_id)
            ");

            $success = $stmt->execute([
                ':quiz_id' => $quizId,
                ':title' => htmlspecialchars($title),
                ':description' => htmlspecialchars($description),
                ':user_id' => $user_id
            ]);

            if ($success) {
                return true;
            }

            return false;
        } catch (PDOException $e) {
            error_log("Erreur création leçon : " . $e->getMessage());
            return false;
        }
    }

}
?>