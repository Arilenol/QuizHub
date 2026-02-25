<?php
class LikeModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Vérifie si un utilisateur a déjà liké un quiz.
     *
     * @param int|string $quizId Identifiant du quiz.
     * @param int|string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si l’utilisateur a liké le quiz, false sinon.
     */
    public function hasLiked(int|string $quizId, int|string $userId): bool
    {
        $stmt = $this->db->prepare("
        SELECT 1 
        FROM likes 
        WHERE quiz_id = :quiz_id AND user_id = :user_id
        LIMIT 1
    ");

        $stmt->execute([
            'quiz_id' => $quizId,
            'user_id' => $userId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Vérifie si un utilisateur a déjà disliké un quiz.
     *
     * @param int|string $quizId Identifiant du quiz.
     * @param int|string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si l’utilisateur a disliké le quiz, false sinon.
     */
    public function hasDisliked(int|string $quizId, int|string $userId): bool
    {
        $stmt = $this->db->prepare("
        SELECT 1 
        FROM dislikes 
        WHERE quiz_id = :quiz_id AND user_id = :user_id
        LIMIT 1
    ");

        $stmt->execute([
            'quiz_id' => $quizId,
            'user_id' => $userId
        ]);

        return (bool) $stmt->fetchColumn();
    }


    /**
     * Ajoute un "like" par l'utilisateur pour un quiz donné.
     *
     * @param int|string $quizId Identifiant du quiz.
     * @param string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si l’insertion a réussi, false sinon.
     */
    public function sendLike(int|string $quizId, string $userId): bool
    {

        $stmt = $this->db->prepare("
        INSERT INTO likes (quiz_id,user_id) VALUES (?, ?) 
        ");
        return $stmt->execute([$quizId, $userId]);
    }

    /**
     * Supprime le "like" d’un utilisateur pour un quiz donné.
     *
     * @param int|string $quizId Identifiant du quiz.
     * @param string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function removeLike(int|string $quizId, string $userId): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM likes 
        WHERE quiz_id = :quiz_id 
        AND user_id = :user_id
    ");

        return $stmt->execute([
            'quiz_id' => $quizId,
            'user_id' => $userId
        ]);
    }

    /**
     * Ajoute un "dislike" par l'utilisateur pour un quiz donné.
     *
     * @param int|string $quizId Identifiant du quiz.
     * @param string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si l’insertion a réussi, false sinon.
     */
    public function sendDislike(int|string $quizId, string $userId): bool
    {
        $stmt = $this->db->prepare("
        INSERT INTO dislikes (quiz_id,user_id) VALUES (?, ?) 
        ");
        return $stmt->execute([$quizId, $userId]);
    }

    /**
     * Supprime le "dislike" d’un utilisateur pour un quiz donné.
     *
     * @param int|string $quizId Identifiant du quiz.
     * @param string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function removeDislike(int|string $quizId, string $userId): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM dislikes 
        WHERE quiz_id = :quiz_id 
        AND user_id = :user_id
    ");

        return $stmt->execute([
            'quiz_id' => $quizId,
            'user_id' => $userId
        ]);
    }

    /**
     * Récupère le nombre total de réactions d’un quiz.
     *
     * @param int|string $quizId Identifiant du quiz.
     *
     * @return array Tableau associatif contenant :
     *               - 'nbjaime'    : Nombre total de likes
     *               - 'nbjaimepas' : Nombre total de dislikes
     */
    public function getReactions(int|string $quizId): array
    {
        $stmt = $this->db->prepare("
        SELECT
            (SELECT COUNT(*) FROM likes WHERE quiz_id = :id) AS nbjaime,
            (SELECT COUNT(*) FROM dislikes WHERE quiz_id = :id) AS nbjaimepas
        ");

        $stmt->execute(['id' => $quizId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }




    /**
     * Vérifie si un utilisateur a déjà liké une leçon.
     *
     * @param int|string $leconId Identifiant du quiz.
     * @param int|string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si l’utilisateur a liké le quiz, false sinon.
     */
    public function hasLikedLecon(int|string $leconId, int|string $userId): bool
    {
        $stmt = $this->db->prepare("
        SELECT 1 
        FROM likesLecon 
        WHERE lecon_id = :lecon_id AND user_id = :user_id
        LIMIT 1
    ");

        $stmt->execute([
            'lecon_id' => $leconId,
            'user_id' => $userId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Vérifie si un utilisateur a déjà disliké une leçon.
     *
     * @param int|string $leconId Identifiant du quiz.
     * @param int|string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si l’utilisateur a disliké le quiz, false sinon.
     */
    public function hasDislikedLecon(int|string $leconId, int|string $userId): bool
    {
        $stmt = $this->db->prepare("
        SELECT 1 
        FROM dislikesLecon 
        WHERE lecon_id = :lecon_id AND user_id = :user_id
        LIMIT 1
    ");

        $stmt->execute([
            'lecon_id' => $leconId,
            'user_id' => $userId
        ]);

        return (bool) $stmt->fetchColumn();
    }


    /**
     * Ajoute un "like" par l'utilisateur pour une leçon donnée.
     *
     * @param int|string $leconId Identifiant du quiz.
     * @param string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si l’insertion a réussi, false sinon.
     */
    public function sendLikeLecon(int|string $leconId, string $userId): bool
    {

        $stmt = $this->db->prepare("
        INSERT INTO likesLecon (lecon_id , user_id) VALUES (?, ?) 
        ");
        return $stmt->execute([$leconId, $userId]);
    }

    /**
     * Supprime le "like" d’un utilisateur pour une leçon donnée.
     *
     * @param int|string $leconId Identifiant du quiz.
     * @param string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function removeLikeLecon(int|string $leconId, string $userId): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM likesLecon 
        WHERE lecon_id = :leconId
        AND user_id = :user_id
    ");

        return $stmt->execute([
            'leconId' => $leconId,
            'user_id' => $userId
        ]);
    }

    /**
     * Ajoute un "dislike" par l'utilisateur pour une leçon donnée.
     *
     * @param int|string $leconId Identifiant du quiz.
     * @param string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si l’insertion a réussi, false sinon.
     */
    public function sendDislikeLecon(int|string $leconId, string $userId): bool
    {
        $stmt = $this->db->prepare("
        INSERT INTO dislikesLecon (lecon_id,user_id) VALUES (?, ?) 
        ");
        return $stmt->execute([$leconId, $userId]);
    }

    /**
     * Supprime le "dislike" d’un utilisateur pour une leçon donnée.
     *
     * @param int|string $leconId Identifiant du quiz.
     * @param string $userId Identifiant de l’utilisateur.
     *
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function removeDislikeLecon(int|string $leconId, string $userId): bool
    {
        $stmt = $this->db->prepare("
        DELETE FROM dislikesLecon 
        WHERE lecon_id = :lecon_id 
        AND user_id = :user_id
    ");

        return $stmt->execute([
            'lecon_id' => $leconId,
            'user_id' => $userId
        ]);
    }

    /**
     * Récupère le nombre total de réactions d’une leçon.
     *
     * @param int|string $leconId Identifiant d'une leçon.
     *
     * @return array Tableau associatif contenant :
     *               - 'nbjaime'    : Nombre total de likes
     *               - 'nbjaimepas' : Nombre total de dislikes
     */
    public function getReactionsLecon(int|string $leconId): array
    {
        $stmt = $this->db->prepare("
        SELECT
            (SELECT COUNT(*) FROM likesLecon WHERE lecon_id = :id) AS nbjaime,
            (SELECT COUNT(*) FROM dislikesLecon WHERE lecon_id = :id) AS nbjaimepas
        ");

        $stmt->execute(['id' => $leconId]);



        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
