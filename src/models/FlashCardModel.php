<?php
class FlashCardModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Récupère tous les IDs des questions d'une flashcard par l'ID du quiz
     *
     * @param int $quizId Identifiant du quiz
     * @return array Tableau des IDs des questions (vide si aucune)
     */
    public function getFlashCardById(int $quizId): array
    {
        $stmt = $this->db->prepare("SELECT id FROM carte WHERE quiz_id = ? ORDER BY numeroCarte ASC");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // retourne un tableau d'IDs
    }

    /**
     * Récupère les informations complètes d'une question par son ID
     *
     * @param int $id Identifiant de la question
     * @return array|null Tableau associatif des infos de la question ou null si non trouvé
     */
    public function getInfoFlashCardById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM carte WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
