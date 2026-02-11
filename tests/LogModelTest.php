<?php

use PHPUnit\Framework\TestCase;

require_once 'src\models\LogModel.php';

class LogModelTest extends TestCase
{
    private PDO $db;
    private LogModel $model;

    protected function setUp(): void
    {
        // Base SQLite temporaire en mémoire
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Création table users
        $this->db->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL
            )
        ");

        $this->model = new LogModel($this->db);
    }

    /* =========================
       CREATE USER
    ========================= */

    public function testCreateUser()
    {
        $result = $this->model->createUser(
            "alice",
            "alice@test.com",
            "password123"
        );

        $this->assertTrue($result);

        $users = $this->db->query("SELECT * FROM users")
            ->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $users);
        $this->assertEquals("alice", $users[0]['username']);
        $this->assertEquals("alice@test.com", $users[0]['email']);
        $this->assertTrue(
            password_verify("password123", $users[0]['password'])
        );
    }

    /* =========================
       GET USER BY EMAIL
    ========================= */

    public function testGetUserByEmail()
    {
        $this->model->createUser("bob", "bob@test.com", "secret");

        $user = $this->model->getUserByEmail("bob@test.com");

        $this->assertNotFalse($user);
        $this->assertEquals("bob", $user['username']);
    }

    public function testGetUserByEmailReturnsFalseIfNotFound()
    {
        $user = $this->model->getUserByEmail("unknown@test.com");

        $this->assertFalse($user);
    }

    /* =========================
       VERIFY PASSWORD
    ========================= */

    public function testVerifyPasswordSuccess()
    {
        $this->model->createUser("charlie", "charlie@test.com", "mypassword");

        $this->assertTrue(
            $this->model->verifyPassword("charlie@test.com", "mypassword")
        );
    }

    public function testVerifyPasswordFailsWithWrongPassword()
    {
        $this->model->createUser("david", "david@test.com", "mypassword");

        $this->assertFalse(
            $this->model->verifyPassword("david@test.com", "wrongpassword")
        );
    }

    public function testVerifyPasswordFailsIfUserDoesNotExist()
    {
        $this->assertFalse(
            $this->model->verifyPassword("ghost@test.com", "1234")
        );
    }
}
