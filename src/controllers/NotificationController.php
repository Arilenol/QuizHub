<?php
require_once ROOT . '/src/models/NotificationModel.php';
require_once ROOT . '/config/config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
class NotificationController
{
    private NotificationModel $model;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $db = getDbConnection();
        $this->model = new NotificationModel($db);
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $action = $_POST['action'] ?? null;

            switch ($action) {

                case 'add':
                    if (!empty($_POST['id'])) {
                        $this->addFriendRequest($_POST['id']);
                        return;
                    }
                    break;

                case 'delete':
                    if (!empty($_POST['id'])) {
                        $this->deleteFriendRequest($_POST['id']);
                        return;
                    }
                    break;

                case 'sendRequest':
                    header("Location: ?page=home");
                    if (!empty($_POST['email'])) {
                        $this->sendRequest($_POST['email']);
                        return;
                    }
                    break;

                case 'deleteNotif':
                    if (!empty($_POST['id'])) {
                        $this->deleteNotification($_POST['id']);
                        return;
                    }
                    break;
            }
        }

        $allFriendRequests = $this->model->getFriendRequestsReceived($_SESSION['id']);
        $notifications = $this->model->getNotifications($_SESSION['id']);

        require ROOT . '/src/views/notification.php';
    }

    public function sendRequest(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = "Format d’email invalide.";
        } else {
            $success = $this->model->createFriendRequest($email);
            $_SESSION['flash'] = $success
                ? "Demande envoyée avec succès."
                : "Échec de la demande.";
        }

        header('Location: ?page=notification');
        exit;
    }
    public function addFriendRequest(string|int $id): void
    {
        $success = $this->model->addFriend($id);
        if ($success) {
            $this->model->createNotification($id, "DemandeAmi", "Votre demande d'ami a été acceptée");
        }
        header('Location: ?page=notification');
        exit;
    }

    public function deleteFriendRequest(string|int $id): void
    {
        $this->model->deleteFriendRequest($id);
        header('Location: ?page=notification');
        exit;
    }

    public function deleteNotification(string|int $notif_id): void
    {
        $this->model->deleteNotification($notif_id, $_SESSION['id']);
        header('Location: ?page=notification');
        exit;
    }
    public function fetch()
    {
        $notifications = $this->model->getFriendRequestsReceived($_SESSION['id']);
        if (empty($notifications)) {
            $notifications = $this->model->getNotifications($_SESSION['id']);
        }
        // pour que le JS puisse récupérer
        echo json_encode($notifications);
    }
}
