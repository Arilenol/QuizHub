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

    case 'save-reactions':
        require_once ROOT . '/src/controllers/LikeController.php';
        $controller = new LikeController();
        $input = file_get_contents('php://input');
        $reactions = json_decode($input, true); // récupère le tableau JS
        var_dump($reactions);
        // $controller->sendReaction($reactions);
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
            case 'modify':
                require_once ROOT . '/src/controllers/LessonController.php';
                isset($_GET['id']) ? $id = $_GET['id'] : exit;
                $controller = new LessonController();
                // va charger views/lesson/createLesson.php
                $controller->modifyLesson($id);
                break;
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
        require_once ROOT . '/src/controllers/FlashcardController.php';
        $controller = new FlashcardController();
        if (isset($_GET['categorie']) && $_GET['categorie'] === 'create') {
            //routine pour créer le controlleur
            $controller->createFlashcard();
            break;
        } elseif (isset($_GET['categorie']) && $_GET['categorie'] === 'modify') {
            $id = $_GET['id'] ?? null;
            $controller->modifyFlashcard($id);
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
                $controller->endFlashcard();
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
            } else if ($_GET['action'] === 'showHistory') {
                $controller->showProfile("showHistory");
            } else if ($_GET['action'] === 'updateProfile') {
                $controller->saveNewInfo();
            }
        } else {
            $controller->showProfile();
        }
        break;
    case 'standard':
    case 'test':
        if (isset($_GET['categorie']) && $_GET['categorie'] === 'create') {
            require_once ROOT . '/src/controllers/QuizController.php';
            $controller = new QuizController();
            $controller->createQuiz();
            exit;
        } elseif (isset($_GET['categorie']) && $_GET['categorie'] === 'modify') {
            $id = $_GET['id'] ?? null;
            require_once ROOT . '/src/controllers/QuizController.php';
            $controller = new QuizController();
            $controller->modifyQuiz($id);
            exit;
        }

        $id = $_GET['id'] ?? null;
        $idQuestion = $_GET['idQuestion'] ?? 1;
        $showAnswer = isset($_GET['reponse']) && $_GET['reponse'] === 'visible';

        require_once ROOT . '/src/controllers/QuizController.php';
        $controller = new QuizController();

        $controller->saveHistoric($id);
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

    case 'signalement':
        require_once ROOT . '/src/controllers/SignalementController.php';
        $controller = new SignalementController();
        $controller->index();
        break;
    case 'CRUDquiz':
        require_once ROOT . '/src/controllers/CRUDQuizController.php';
        $controller = new CRUDQuizController();
        $controller->index();
        break;
    case 'CRUDauteur':
        require_once ROOT . '/src/controllers/CRUDAuteurController.php';
        $controller = new CRUDAuteurController();
        $controller->index();
        break;
    case 'Categorie':
        require_once ROOT . '/src/controllers/CategorieController.php';
        $controller = new CategorieController();
        $controller->index();
        break;
    default:
        echo "404 - Page non trouvée";
        break;
}
