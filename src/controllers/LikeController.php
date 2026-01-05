<?php
require_once ROOT . '/src/models/LikeModel.php';
require_once ROOT . '/config/config.php';

class LikeController
{

    private LikeModel $model;

    public function __construct()
    {
        $db = getDbConnection();
        $this->model = new LikeModel($db);
    }


    public function sendReaction($reactions)
    {
        // Démarrage de session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if ($this->model->hasLiked($reactions['quizId'], $_SESSION['id'])){
            return;
        }
        if ($this->model->hasDisliked($reactions['quizId'], $_SESSION['id'])){
            return;
        }
        if ($reactions['type'] === 'like') {
            $this->model->sendLike($reactions['quizId'], $_SESSION['id']);
        } else {
            $this->model->sendDislike($reactions['quizId'], $_SESSION['id']);
        }
    }
}
