<?php
class LikeModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function sendLike(int|string $quizId, string $userId): bool
    {

        $stmt = $this->db->prepare("
        INSERT INTO likes (dislike_id, quiz_id,user_id) VALUES (?, 1) ON DUPLICATE KEY UPDATE nbjaime = nbjaime + 1
        ");
        return $stmt->execute([$quizId, $userId]);
    }

    public function sendDislike(int|string $quizId): bool
    {
        $stmt = $this->db->prepare("
        UPDATE dislikes SET nbjaimepas = nbjaimepas + 1 where id = ?
        ");
        return $stmt->execute([$quizId]);
    }

    public function getReactions(int|string $quizId): array
    {
        $stmt = $this->db->prepare("
        SELECT nbjaime, nbjaimepas FROM quiz where id = ?
        ");
        $stmt->execute([$quizId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
