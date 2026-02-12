<?php
class PageInterModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère les informations principales d’un quiz.
     *
     * @param int $quizId Identifiant du quiz à récupérer.
     *
     * @return array Tableau associatif contenant :
     *               - 'id' : ID du quiz
     *               - 'title' : Titre du quiz
     *               - 'description' : Description du quiz
     *               Retourne un tableau vide si aucun quiz ne correspond.
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
     * Récupère le classement (leaderboard) des amis d’un utilisateur pour un quiz donné.
     *
     * Le classement est trié :
     *  1. Par score décroissant (meilleur_score DESC)
     *  2. En cas d’égalité, par temps croissant (tempsPris ASC)
     *
     * @param int $quizId Identifiant du quiz concerné.
     * @param int $userId Identifiant de l’utilisateur dont on veut afficher le classement des amis.
     *
     * @return array Tableau de tableaux associatifs contenant pour chaque utilisateur :
     *               - 'id'              : ID de l'utilisateur
     *               - 'username'        : Nom d'utilisateur
     *               - 'email'           : Email de l'utilisateur
     *               - 'meilleur_score'  : Meilleur score en pourcentage
     *               - 'tempsPris'       : Meilleur temps enregistré
     *               - 'dateRealisation' : Date de la meilleure tentative
     *
     *               Retourne un tableau vide si aucun résultat n’est trouvé.
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
     * Récupère les réactions d’un quiz donné.
     *
     * @param int $quizId Identifiant du quiz pour récupérer les réactions associées.
     *
     * @return array Tableau associatif contenant :
     *               - 'nbjaime' : retourne le nbr de likes du quiz, 0 sinon
     *               - 'nbjaimepas' : retourne le nbr de dislikes du quiz, 0 sinon
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
     * Récupère la réaction d’un utilisateur pour un quiz donné.
     *
     * @param int $userId Identifiant de l’utilisateur.
     * @param int $quizId Identifiant du quiz.
     *
     * @return array Tableau associatif contenant :
     *               - 'hasLiked'    : retourne si l’utilisateur a liké le quiz
     *               - 'hasDisliked' : retourne si l’utilisateur a disliké le quiz
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
