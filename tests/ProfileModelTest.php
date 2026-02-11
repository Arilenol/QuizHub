<?php

use PHPUnit\Framework\TestCase;

require_once 'src\models\ProfileModel.php';

class ProfileModelTest extends TestCase
{
    private PDO $db;
    private ProfileModel $model;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        /* ======================
           TABLES
        ====================== */

        $this->db->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT,
                email TEXT,
                password TEXT,
                description TEXT,
                picture_path TEXT
            );

            CREATE TABLE quiz (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                genre TEXT,
                date TEXT,
                title TEXT,
                difficulty TEXT,
                description TEXT
            );

            CREATE TABLE lecon (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER
            );

            CREATE TABLE resultat (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                quiz_id INTEGER,
                user_id INTEGER,
                score INTEGER,
                dateRealisation TEXT
            );

            CREATE TABLE categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                categorieName TEXT
            );

            CREATE TABLE categorie_quiz (
                quiz_id INTEGER,
                category_id INTEGER
            );

            CREATE TABLE amis (
                user1_id INTEGER,
                user2_id INTEGER
            );

            CREATE TABLE categorie_lecon (
                lesson_id INTEGER,
                category_id INTEGER
            );

            CREATE TABLE likes (
                quiz_id INTEGER
            );

            CREATE TABLE dislikes (
                quiz_id INTEGER
            );
        ");

        $this->model = new ProfileModel($this->db);

        /* ======================
           DONNÉES DE TEST
        ====================== */

        // Users
        $this->db->exec("
            INSERT INTO users (id, username, email, password)
            VALUES (1,'alice','alice@test.com','pass'),
                   (2,'bob','bob@test.com','pass');
        ");

        // Quiz
        $this->db->exec("
            INSERT INTO quiz (id,user_id,title,genre,date,difficulty,description)
            VALUES (1,1,'Quiz1','General','2024','Easy','desc');
        ");

        // Résultat
        $this->db->exec("
            INSERT INTO resultat (quiz_id,user_id,score,dateRealisation)
            VALUES (1,1,15,'2024-01-01');
        ");

        // Leçon
        $this->db->exec("
            INSERT INTO lecon (id,user_id) VALUES (1,1);
        ");
        $this->db->exec("
            INSERT INTO categorie_lecon (lesson_id,category_id) VALUES (1,1);
        ");

        // Catégories
        $this->db->exec("
            INSERT INTO categories (id,categorieName) VALUES (1,'Math');
            INSERT INTO categorie_quiz (quiz_id,category_id) VALUES (1,1);
        ");

        // Ami
        $this->db->exec("
            INSERT INTO amis (user1_id,user2_id) VALUES (1,2);
        ");
    }

    /* ======================
       GET CREDENTIALS
    ====================== */

    public function testGetCredentials()
    {
        $user = $this->model->getCredentials(1);
        $this->assertEquals('alice', $user['username']);
    }

    /* ======================
       COUNT METHODS
    ====================== */

    public function testGetQuizCreated()
    {
        $this->assertEquals(1, $this->model->getQuizCreated(1));
    }

    public function testGetLessonsCreated()
    {
        $this->assertEquals(1, $this->model->getLessonsCreated(1));
    }

    public function testGetGamesNumber()
    {
        $this->assertEquals(1, $this->model->getGamesNumber(1));
    }

    /* ======================
       QUIZ PLAYED
    ====================== */

    public function testGetQuizPlayed()
    {
        $quizzes = $this->model->getQuizPlayed(1);
        $this->assertIsArray($quizzes);
        $this->assertEquals('Quiz1', $quizzes[0]['title']);
        $this->assertEquals(['Math'], $quizzes[0]['categories']);
    }

    /* ======================
       FRIENDS
    ====================== */

    public function testGetFriends()
    {
        $friends = $this->model->getFriends(1);
        $this->assertEquals('bob', $friends[0]['friend_name']);
    }

    /* ======================
       UPDATE METHODS
    ====================== */

    public function testSaveUsername()
    {
        $this->model->saveUsername('newAlice', 1);
        $user = $this->model->getCredentials(1);
        $this->assertEquals('newAlice', $user['username']);
    }

    public function testSaveEmail()
    {
        $this->model->saveEmail('new@mail.com', 1);
        $user = $this->model->getCredentials(1);
        $this->assertEquals('new@mail.com', $user['email']);
    }

    public function testSavePassword()
    {
        $this->model->savePassword('newpass', 1);
        $user = $this->model->getCredentials(1);
        $this->assertTrue(password_verify('newpass', $user['password']));
    }

    public function testSavePicture()
    {
        $this->model->savePicture('img.png', 1);
        $user = $this->model->getCredentials(1);
        $this->assertEquals('img.png', $user['picture_path']);
    }

    /* ======================
       DELETE METHODS
    ====================== */

    public function testDeleteFriend()
    {
        $this->assertTrue($this->model->deleteFriend(2, 1));
        $this->assertFalse($this->model->getFriends(1));
    }

    public function testDeleteLesson()
    {
        $this->assertTrue($this->model->deleteLesson(1));
    }

    public function testDeleteQuiz()
    {
        $this->assertTrue($this->model->deleteQuiz(1));
    }
}
