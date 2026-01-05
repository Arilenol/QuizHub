<?php
class HistoricModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Sauvegarde l'historique de jeu d'un utilisateur pour un quiz donné.
     * 
     * Cette méthode vérifie si un résultat existe déjà pour l'utilisateur et le quiz,
     * le supprime si nécessaire, puis crée un nouvel enregistrement avec score et temps
     * initialisés à NULL.
     *
     * @param int|string $quizId L'identifiant du quiz à sauvegarder.
     * @param int $userId L'identifiant de l'utilisateur qui a joué au quiz.
     *
     * @return bool Retourne true si l'insertion dans la table `resultat` a réussi, false sinon.
     */
    public function saveHistoric(string|int $quizId, int $userId): bool
    {
        // Vérifie si un résultat existe déjà pour ce quiz et cet utilisateur
        $stmt = $this->db->prepare("SELECT * FROM resultat WHERE quiz_id = ? and user_id = ?");
        if ($stmt->execute([$quizId, $userId])) {
            // Supprime l'ancien résultat s'il existe
            $stmt = $this->db->prepare("DELETE FROM resultat WHERE quiz_id = ? and user_id = ?");
            $stmt->execute([$quizId, $userId]);
        }
        $stmt = $this->db->prepare("INSERT INTO resultat(quiz_id, user_id, score, tempsPris) VALUES (?,?,null,null)");
        return $stmt->execute([$quizId, $userId]);
    }
}
