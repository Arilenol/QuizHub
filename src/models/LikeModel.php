<?php
class LikeModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function sendLike(int|string $quizId): bool
    {

        $stmt = $this->db->prepare("
        UPDATE quiz SET nbjaime = nbjaime + 1 where id = ?
        ");
        return $stmt->execute([$quizId]);
    }

    public function sendDislike(int|string $quizId): bool
    {
        $stmt = $this->db->prepare("
        UPDATE quiz SET nbjaimepas = nbjaime + 1 where id = ?
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
