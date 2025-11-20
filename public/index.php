<?php

define('ROOT', dirname(__DIR__));

// par défaut page d’accueil
$page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'home';

switch ($page) {
    case 'home':
        require_once ROOT . '/src/controllers/HomeController.php';
        $controller = new HomeController();
        // va charger views/home.php
        $controller->index();
        break;


    case 'catalogue':
        require_once ROOT . '/src/controllers/CatalogueController.php';
        $controller = new CatalogueController();
        // va charger views/catalogue.php
        $controller->index();
        //header('Location: catalogue.php');
        break;


    case 'lesson':
        $categorie = $_GET['categorie'] ?? null;
        switch ($categorie) {

            //créé une leçon
            case 'create':
                require_once ROOT . '/src/controllers/LessonController.php';
                $controller = new LessonController();
                // va charger views/lesson/createLesson.php
                $controller->createLesson();
                break;

            //voir une leçon
            case 'view':
                require_once ROOT . '/src/controllers/LessonController.php';
                isset($_GET['id']) ? $id = $_GET['id'] : exit;
                $controller = new LessonController();
                // va charger views/lesson/show.php
                $controller->index($id);
                break;

            default:
                echo "Erreur : catégorie de leçon invalide";
            break;
        }
        break;
    case 'log':
        $logtype = $_GET['typelog'] ?? null;
        switch ($logtype) {
            //formulaire d'inscription
            case 'register':
                require_once ROOT . '/src/controllers/LogController.php';
                $controller = new LogController();
                // va charger views/log/connection.php
                $controller->showRegister();
                break;

            //page de connexion
            case 'connection':
                require_once ROOT . '/src/controllers/LogController.php';
                $controller = new LogController();
                // va charger views/log/connection.php
                $controller->showConnection();
                break;

            default:
                echo "Erreur : catégorie de leçon invalide";
            break;
        }
        break;

    default:
        echo "404 - Page non trouvée";
        break;
}
