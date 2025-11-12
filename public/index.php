<?php

define('ROOT', dirname(__DIR__));

// par défaut page d’accueil
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        require_once ROOT . '/src/controllers/HomeController.php';
        $controller = new HomeController();
        // va charger views/home.php
        $controller->index();
        break;

    case 'lesson':
        //EN DEVELOPPEMENT
        // require_once ROOT . '/src/controllers/HomeController.php';
        // $controller = new HomeController();
        // // va charger views/home.php
        // $controller->index();
        header("Location: ../src/views/lesson.php");
        break;
    default:
        echo "404 - Page non trouvée";
}
