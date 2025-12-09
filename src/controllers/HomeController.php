<?php
require_once ROOT . '/src/models/HomeModel.php';
require_once ROOT . '/config/config.php';
require_once ROOT . '/src/models/LessonModel.php';

class HomeController
{
    public function index()
    {
        $db = getDbConnection();
        $model = new HomeModel($db);
        // récupère les données
        $quiz = $model->getAllInfo();

        // $lessons = 
        if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
            $quizNextPart = $model->getAllCreationsByUser($_SESSION['id']);
        } else {
            $quizNextPart = $model->getAllNewCreations();
        }
        // afficher la vue
        require ROOT . '/src/views/home.php';
    }
}
