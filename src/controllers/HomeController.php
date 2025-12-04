<?php
require_once ROOT . '/src/models/HomeModel.php';
require_once ROOT . '/config/config.php';

class HomeController
{
    public function index()
    {
        $db = getDbConnection();
        $model = new HomeModel($db);
        // récupère les données
        if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
            $quiz = $model->getAllInfo();
            $quizNextPart = $model->getAllCreationsByUser($_SESSION['id']);
        } else {
            $quiz = $model->getAllInfo();
            $quizNextPart = $model->getAllNewCreations();
        }
        // afficher la vue
        require ROOT . '/src/views/home.php';
    }
}
