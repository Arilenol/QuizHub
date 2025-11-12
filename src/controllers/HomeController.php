<?php
require_once ROOT . '/src/models/HomeModel.php';
require_once ROOT . '/config/config.php';

class HomeController {
    public function index() {
        $db = getDbConnection();
        $model = new HomeModel($db);
         // récupère les données
        $quiz = $model->getAll();
        // afficher la vue
        require ROOT . '/src/views/home.php';
    }
}
