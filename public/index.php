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


    case 'catalogue':
        require_once ROOT . '/src/controllers/CatalogueController.php';
        $controller = new CatalogueController();
        // va charger views/catalogue.php
        $controller->index();
        //header('Location: catalogue.php');
        break;

    default:
        echo "404 - Page non trouvée";
        break;
}
