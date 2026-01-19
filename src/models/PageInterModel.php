<?php
class PageInterModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Retourne les infos du quiz (nom, description)
     */
    public function getQuizInfo(int $quizId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, title, description
            FROM quiz
            WHERE id = ?
        ");
        $stmt->execute([$quizId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Retourne le classement des amis + l'utilisateur sur un quiz
     * Convertit le score "k/n" en pourcentage
     * Seuls les amis qui ont participé et l'utilisateur s'il a participé
     */
    public function getFriendsLeaderboard(int $quizId, int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.id,
                u.username,
                u.email,
                MAX(
                    CAST(SUBSTR(r.score, 1, INSTR(r.score, '/') - 1) AS FLOAT) / 
                    CAST(SUBSTR(r.score, INSTR(r.score, '/') + 1) AS FLOAT) * 100
                ) as meilleur_score,
                MAX(r.tempsPris) as tempsPris,
                MAX(r.dateRealisation) as dateRealisation
            FROM users u
            INNER JOIN amis a ON (a.user1_id = ? AND a.user2_id = u.id) 
                              OR (a.user2_id = ? AND a.user1_id = u.id)
            INNER JOIN resultat r ON r.user_id = u.id AND r.quiz_id = ?
            GROUP BY u.id
            
            UNION
            
            SELECT 
                u.id,
                u.username,
                u.email,
                MAX(
                    CAST(SUBSTR(r.score, 1, INSTR(r.score, '/') - 1) AS FLOAT) / 
                    CAST(SUBSTR(r.score, INSTR(r.score, '/') + 1) AS FLOAT) * 100
                ) as meilleur_score,
                MAX(r.tempsPris) as tempsPris,
                MAX(r.dateRealisation) as dateRealisation
            FROM users u
            INNER JOIN resultat r ON r.user_id = u.id AND r.quiz_id = ?
            WHERE u.id = ?
            GROUP BY u.id
            
            ORDER BY meilleur_score DESC, tempsPris ASC
        ");
        $stmt->execute([$userId, $userId, $quizId, $quizId, $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne les réactions (likes/dislikes) pour un quiz
     */
    public function getQuizReactions(int $quizId): array
    {
        $stmtLikes = $this->db->prepare("
            SELECT COUNT(*) as nbjaime
            FROM likes
            WHERE quiz_id = ?
        ");
        $stmtLikes->execute([$quizId]);
        $likes = $stmtLikes->fetch(PDO::FETCH_ASSOC);

        $stmtDislikes = $this->db->prepare("
            SELECT COUNT(*) as nbjaimepas
            FROM dislikes
            WHERE quiz_id = ?
        ");
        $stmtDislikes->execute([$quizId]);
        $dislikes = $stmtDislikes->fetch(PDO::FETCH_ASSOC);

        return [
            'nbjaime' => $likes['nbjaime'] ?? 0,
            'nbjaimepas' => $dislikes['nbjaimepas'] ?? 0
        ];
    }

    /**
     * Vérifie si l'utilisateur a déjà aimé ou non aimé le quiz
     */
    public function getUserReaction(int $userId, int $quizId): array
    {
        $stmtLike = $this->db->prepare("
            SELECT like_id
            FROM likes
            WHERE user_id = ? AND quiz_id = ?
            LIMIT 1
        ");
        $stmtLike->execute([$userId, $quizId]);
        $hasLiked = $stmtLike->fetch(PDO::FETCH_ASSOC) !== false;

        $stmtDislike = $this->db->prepare("
            SELECT dislike_id
            FROM dislikes
            WHERE user_id = ? AND quiz_id = ?
            LIMIT 1
        ");
        $stmtDislike->execute([$userId, $quizId]);
        $hasDisliked = $stmtDislike->fetch(PDO::FETCH_ASSOC) !== false;

        return [
            'hasLiked' => $hasLiked,
            'hasDisliked' => $hasDisliked
        ];
    }
}
?>
