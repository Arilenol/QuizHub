<?php

class SignalementModel {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Sauvegarde un signalement en base de données
     *
     * @param int|string $user_id  ID de l'utilisateur qui signale
     * @param string $type         Type de signalement
     * @param string $description  Description du signalement
     * @param int|string|null $quiz_id  ID du quiz signalé (optionnel)
     * @param int|string|null $lesson_id  ID de la leçon signalée (optionnel)
     *
     * @return int|false  ID du signalement créé ou false en cas d'erreur
     */
    public function createSignalement(int|string $user_id, string $type, string $description, int|string|null $quiz_id = null, int|string|null $lesson_id = null)
    {
        $stmt = $this->db->prepare("
        INSERT INTO signalements (user_id, quiz_id, lesson_id, type, description)
        VALUES (:user_id, :quiz_id, :lesson_id, :type, :description)
    ");

        if ($stmt->execute([
            ':user_id'     => $user_id,
            ':quiz_id'     => $quiz_id,
            ':lesson_id'   => $lesson_id,
            ':type'        => $type,
            ':description' => $description
        ])) {
            return $this->db->lastInsertId();
        }
        return false;
    }
}

?>