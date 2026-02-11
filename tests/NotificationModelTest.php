<?php
use PHPUnit\Framework\TestCase;

define('ROOT', dirname(__DIR__));
require_once 'src/models/LogModel.php';
require_once 'src/models/NotificationModel.php';

class NotificationModelTest extends TestCase
{
    private $db;
    private $model;

    protected function setUp(): void
    {
        // Base en mémoire
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Création des tables nécessaires
        $this->db->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT,
                email TEXT
            );
        ");
        $this->db->exec("
            CREATE TABLE demandeAmi (
                demandeur_id INTEGER,
                receveur_id INTEGER
            );
        ");
        $this->db->exec("
            CREATE TABLE amis (
                user1_id INTEGER,
                user2_id INTEGER
            );
        ");
        $this->db->exec("
            CREATE TABLE notifications (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                type TEXT,
                message TEXT,
                contenu_id INTEGER,
                contenu_type TEXT,
                is_read INTEGER DEFAULT 0,
                date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Préparer un utilisateur connecté
        $_SESSION['id'] = 1;
        $this->db->exec("INSERT INTO users (id, username, email) VALUES (1, 'Alice', 'alice@test.com')");
        $this->db->exec("INSERT INTO users (id, username, email) VALUES (2, 'Bob', 'bob@test.com')");

        $this->model = new NotificationModel($this->db);
    }

    public function testGetFriendRequestsReceived(): void
    {
        $this->db->exec("INSERT INTO demandeAmi (demandeur_id, receveur_id) VALUES (2,1)");
        $requests = $this->model->getFriendRequestsReceived(1);
        $this->assertCount(1, $requests);
        $this->assertEquals(2, $requests[0]['demandeur_id']);
    }

    public function testAddFriend(): void
    {
        // Simuler demande existante
        $this->db->exec("INSERT INTO demandeAmi (demandeur_id, receveur_id) VALUES (2,1)");

        $result = $this->model->addFriend(2);
        $this->assertTrue($result);

        // Vérifier que la relation d'amitié existe
        $stmt = $this->db->query("SELECT * FROM amis WHERE user1_id = 1 AND user2_id = 2");
        $friend = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($friend);

        // Vérifier que la demande a été supprimée
        $stmt = $this->db->query("SELECT * FROM demandeAmi WHERE demandeur_id = 2 AND receveur_id = 1");
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($request);
    }

    public function testDeleteFriendRequest(): void
    {
        $this->db->exec("INSERT INTO demandeAmi (demandeur_id, receveur_id) VALUES (2,1)");
        $result = $this->model->deleteFriendRequest(2);
        $this->assertTrue($result);

        $stmt = $this->db->query("SELECT * FROM demandeAmi WHERE demandeur_id = 2 AND receveur_id = 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($row);
    }

    public function testCreateNotification(): void
    {
        $result = $this->model->createNotification(2, 'info', 'Hello Bob', 10, 'quiz');
        $this->assertTrue($result);

        $stmt = $this->db->query("SELECT * FROM notifications WHERE user_id = 2");
        $notif = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals('info', $notif['type']);
        $this->assertEquals('Hello Bob', $notif['message']);
    }

    public function testGetNotifications(): void
    {
        $this->db->exec("INSERT INTO notifications (user_id, type, message, is_read) VALUES (2, 'info', 'Test', 0)");
        $notifs = $this->model->getNotifications(2);
        $this->assertCount(1, $notifs);
        $this->assertEquals('Test', $notifs[0]['message']);
    }

    public function testDeleteNotification(): void
    {
        $this->db->exec("INSERT INTO notifications (id, user_id, type, message) VALUES (1, 2, 'info', 'Delete me')");
        $result = $this->model->deleteNotification(1, 2);
        $this->assertTrue($result);

        $stmt = $this->db->query("SELECT * FROM notifications WHERE id = 1");
        $notif = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($notif);
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        $this->db = null;
        $this->model = null;
    }
}
