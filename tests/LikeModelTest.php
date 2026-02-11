<?php

use PHPUnit\Framework\TestCase;

require_once 'src\models\LikeModel.php';

class LikeModelTest extends TestCase
{
    private PDO $db;
    private LikeModel $model;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Crée tables
        $this->db->exec("
            CREATE TABLE likes (
                quiz_id INTEGER,
                user_id INTEGER
            );
            CREATE TABLE dislikes (
                quiz_id INTEGER,
                user_id INTEGER
            );
        ");

        $this->model = new LikeModel($this->db);

        // Données de test
        $this->db->exec("
            INSERT INTO likes (quiz_id, user_id) VALUES (1, 1);
            INSERT INTO dislikes (quiz_id, user_id) VALUES (2, 1);
        ");
    }

    /* =========================
       HAS LIKED / DISLIKED
    ========================= */

    public function testHasLiked()
    {
        $this->assertTrue($this->model->hasLiked(1, 1));
        $this->assertFalse($this->model->hasLiked(2, 1));
    }

    public function testHasDisliked()
    {
        $this->assertTrue($this->model->hasDisliked(2, 1));
        $this->assertFalse($this->model->hasDisliked(1, 1));
    }

    /* =========================
       SEND LIKE / DISLIKE
    ========================= */

    public function testSendLike()
    {
        $this->assertTrue($this->model->sendLike(3, 2));
        $this->assertTrue($this->model->hasLiked(3, 2));
    }

    public function testSendDislike()
    {
        $this->assertTrue($this->model->sendDislike(4, 2));
        $this->assertTrue($this->model->hasDisliked(4, 2));
    }

    /* =========================
       REMOVE LIKE / DISLIKE
    ========================= */

    public function testRemoveLike()
    {
        $this->assertTrue($this->model->removeLike(1, 1));
        $this->assertFalse($this->model->hasLiked(1, 1));
    }

    public function testRemoveDislike()
    {
        $this->assertTrue($this->model->removeDislike(2, 1));
        $this->assertFalse($this->model->hasDisliked(2, 1));
    }

    /* =========================
       GET REACTIONS
    ========================= */

    public function testGetReactions()
    {
        // Setup : 2 likes sur quiz 1, 1 dislike sur quiz 2
        $this->model->sendLike(1, 2);
        $this->model->sendDislike(2, 2);

        $reactions1 = $this->model->getReactions(1);
        $this->assertEquals(2, $reactions1['nbjaime']);
        $this->assertEquals(0, $reactions1['nbjaimepas']);

        $reactions2 = $this->model->getReactions(2);
        $this->assertEquals(0, $reactions2['nbjaime']);
        $this->assertEquals(2, $reactions2['nbjaimepas']);
    }
}
