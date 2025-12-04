<?php

define('ROOT', dirname(__DIR__));

// par défaut page d’accueil
$page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : 'home';

switch ($page) {
    case 'home':
        session_start();
        if (session_status() === PHP_SESSION_ACTIVE) {
            foreach ($_SESSION as $key => $value) {
                if ($key !== 'id') {
                    // on ne garde que "id", on supprime tous les résultats précédents
                    unset($_SESSION[$key]);
                }
            }
        }
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
        require_once ROOT . '/src/controllers/LogController.php';
        switch ($logtype) {
            //formulaire d'inscription
            case 'register':
                $controller = new LogController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->createUser();
                } else {
                    // va charger views/log/connection.php
                    $controller->showRegister();
                }
                break;

            //page de connexion
            case 'connection':
                $controller = new LogController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->loginUser();
                } else {
                    // va charger views/log/connection.php
                    $controller->showConnection();
                }
                break;
            case 'logout':
                // On démarre l'ancienne session
                session_start();
                // On la supprime du navigateur
                session_unset();
                session_destroy();
                header("Location: ?page=home");
                exit;
                break;

            default:
                echo "Erreur : type d'action de sesson non identifié";
                break;
        }
        break;
    case 'flashcard':
        $action = $_GET['action'] ?? null; // start, ongoing, end
        require_once ROOT . '/src/controllers/FlashCardController.php';
        $controller = new FlashCardController();
        if (isset($_GET['categorie'])) {
            //routine pour créer le controlleur
            $controller->createFlashcard();
            break;
        }
        switch ($action) {
            case 'start':
                $id = $_GET['id'] ?? null;
                $controller->preload($id);
                break;

            case 'ongoing':
                $questionId = $_GET['question'] ?? null;
                $controller->questionById($questionId);
                break;

            case 'end':
                echo "Fin du quiz";
                break;

            default:
                echo "Action flashcard invalide";
        }
        break;
    case 'profil':
        require_once ROOT . '/src/controllers/ProfileController.php';
        $controller = new ProfileController();
        if (isset($_GET['action'])) {
            if ($_GET['action'] === 'displayFriends') {
                $controller->showProfile("showFriends");
            }
        } else {
            $controller->showProfile();
        }
        break;
    case 'standard':
    case 'test':
        if (isset($_GET['categorie'])) {
            require_once ROOT . '/src/controllers/QuizController.php';
            $controller = new QuizController();
            $controller->createQuiz();
            exit;
        }
        $id = $_GET['id'] ?? null;
        $idQuestion = $_GET['idQuestion'] ?? 1;
        $showAnswer = isset($_GET['reponse']) && $_GET['reponse'] === 'visible';

        require_once ROOT . '/src/controllers/QuizController.php';
        $controller = new QuizController();

        $controller->showQuiz($id, $idQuestion, $showAnswer);
        break;
    case 'createContent':
        require_once ROOT . '/src/views/createContent.php';
        break;

    case 'CRUD':
        require_once ROOT . '/src/controllers/CRUDController.php';
        $controller = new CRUDController();
        $controller->index();
        break;
    case 'notification':
        require_once ROOT . '/src/controllers/NotificationController.php';
        $controller = new NotificationController();
        if (isset($_GET['email'])) {
            $controller->sendRequest($_GET['email']);
        } elseif (isset($_GET['action'])) {
            if ($_GET['action'] === 'add') {
                $controller->addFriendRequest($_GET['id']);
            } elseif ($_GET['action'] === 'delete') {
                $controller->deleteFriendRequest($_GET['id']);
            }
        } else {
            $controller->index();
        }
        if (isset($_GET['action']) && ($_GET['action']  === "fetch")) {
            $controller->fetch();
        }

        break;
    default:
        echo "404 - Page non trouvée";
        break;
}
