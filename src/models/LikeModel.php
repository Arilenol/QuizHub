<?php
class LikeModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

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

    public function sendLike(int|string $quizId, string $userId): bool
    {

        $stmt = $this->db->prepare("
        INSERT INTO likes (quiz_id,user_id) VALUES (?, ?) 
        ");
        return $stmt->execute([$quizId, $userId]);
    }

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

    public function sendDislike(int|string $quizId, string $userId): bool
    {
        $stmt = $this->db->prepare("
        INSERT INTO dislikes (quiz_id,user_id) VALUES (?, ?) 
        ");
        return $stmt->execute([$quizId, $userId]);
    }

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
}
