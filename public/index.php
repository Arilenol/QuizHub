<?php

define('ROOT', dirname(__DIR__));
// Build a BASE_URL from SERVER variables and ROOT so views can reference web URLs.
// Fallbacks are provided for CLI or unusual server configurations.


//--------------------------------------------------généré par Copilot------------------------------------------------------------
//pour transforler un chemin serveur en URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '';
$rootReal = realpath(ROOT) ? str_replace('\\','/', realpath(ROOT)) : str_replace('\\','/', ROOT);
$docRoot = $docRoot ? str_replace('\\','/',$docRoot) : '';
$basePath = '';
if ($docRoot !== '' && strpos($rootReal, $docRoot) === 0) {
    $basePath = substr($rootReal, strlen($docRoot));
}
// If we couldn't compute a path, fallback to project folder name
if ($basePath === '') {
    // try to infer from ROOT
    $basePath = '/' . trim(basename($rootReal), '/');
}
$basePath = rtrim($basePath, '/');
define('BASE_URL', $protocol . '://' . $host . $basePath . '/public');
//---------------------------------------------------généré par Copilot------------------------------------------------------------


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
