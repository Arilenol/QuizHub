<?php
require_once ROOT . '/src/models/NotificationModel.php';
require_once ROOT . '/config/config.php';
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
        $allFriendRequests = $this->model->getFriendRequestsReceived($_SESSION['id']);
        $notifications = $this->model->getNotifications($_SESSION['id']);
        require ROOT . '/src/views/notification.php';
    }

    public function sendRequest(string $email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "Format d’email invalide.";
            $this->index();
            return;
        }
        $message = $this->model->createFriendRequest($email);
        $allFriendRequests = $this->model->getFriendRequestsReceived($_SESSION['id']);        $notifications = $this->model->getNotifications($_SESSION['id']);        require ROOT . '/src/views/notification.php';
    }

    public function addFriendRequest(string|int $id): void
    {
        $success = $this->model->addFriend($id);

        header('Location: ?page=notification');
        exit;
    }

    public function deleteFriendRequest(string|int $id): void
    {
        $success = $this->model->deleteFriendRequest($id);

        header('Location: ?page=notification');
        exit;
    }

    public function deleteNotification(string|int $notif_id): void
    {
        $this->model->deleteNotification($notif_id, $_SESSION['id']);
        $this->index();
    }

    public function fetch()
    {
        $notifications = $this->model->getFriendRequestsReceived($_SESSION['id']);
        // pour que le JS puisse récupérer
        echo json_encode($notifications);
    }
}
