<?php
require_once ROOT . '/src/models/ProfileModel.php';
require_once ROOT . '/config/config.php';

class ProfileController
{

    private ProfileModel $model;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = getDbConnection();
        $this->model = new ProfileModel($this->db);
    }

    public function showProfile(?string $option = null, ?string $optionSuccess = null, ?string $optionError = null)
    {
        if (isset($_SESSION['id'])) {
            $infosUser = $this->model->getCredentials($_SESSION['id']);
            $creation = $this->model->getQuizCreated($_SESSION['id']);
            $played = $this->model->getGamesNumber($_SESSION['id']);
            $lessonCreated = $this->model->getLessonsCreated($_SESSION['id']);
            if ($option !== null && $option === "showFriends") {
                $friends = $this->model->getFriends($_SESSION['id']);
            }
            if ($option !== null && $option === "showHistory") {
                $hist = $this->model->getQuizPlayed($_SESSION['id']);
            }
            if ($option === null && $creation > 0) {
                require_once ROOT . '/src/models/HomeModel.php';
                require_once ROOT . '/src/models/LessonModel.php';
                $modelHome = new HomeModel($this->db);
                $modelLesson = new LessonModel($this->db);
                $quiz = $modelHome->getAllCreationsByUser($_SESSION['id']);
                $lessons = $modelLesson->getAllInfoLessonsByUser($_SESSION['id']);
                if (!empty($lessons) && $lessons[0] !== null) {
                    $quiz[] = $lessons[0];
                }
            }
            if ($option === 'showProfile') {
                $showProfileModal = true;
                $messageSuccess = $optionSuccess;
                $messageError = $optionError;
            }
            if (isset($_GET['actionType'])){
                $test = $this->model->deleteFriend($_SESSION['id'],$_POST['idToDelete']);
            }
            $activeTab = $_GET['action'] ?? 'creations';
            $activeTab = $activeTab === 'displayFriends' ? 'friends' : ($activeTab === 'showHistory' ? 'history' : 'creations');
            require ROOT . '/src/views/profil.php';
        } else {
            echo "Problème de chargement";
        }
    }

    public function saveNewInfo()
    {
        $infosUser = $this->model->getCredentials($_SESSION['id']);
        $saver = [];
        $error = [];
        $messageSuccess = '';
        $messageError = '';
        if ($infosUser['username'] !== $_POST['username']) {
            $success = $this->model->saveUsername($_POST['username'], $_SESSION['id']);
            if ($success) {
                $saver[] = "Nom d'utilisateur";
            } else {
                $error[] = "Nom d'utilisateur";
            }
        }
        if ($infosUser['description'] !== $_POST['description']) {
            $success = $this->model->saveDescription($_POST['description'], $_SESSION['id']);
            if ($success) {
                $saver[] = 'Description';
            } else {
                $error[] = "Description";
            }
        }
        if ($infosUser['email'] !== $_POST['email']) {
            $success = $this->model->saveEmail($_POST['email'], $_SESSION['id']);
            if ($success) {
                $saver[] = 'Email';
            } else {
                $error[] = "Email";
            }
        }
        if (!empty($_POST['password'])) {
            if ($_POST['passwordVerif'] === $_POST['password']) {
                $success = $this->model->savePassword($_POST['password'], $_SESSION['id']);
                if ($success) {
                    $saver[] = 'Mot de passe';
                } else {
                    $error[] = "Mot de passe";
                }
            } else {
                $error[] = "Vous avez donné différents mot de passe";
            }
        }
        if (!empty($saver)) {
            $messageSuccess = "Les champs suivants ont été mis à jour : " . implode(',', $saver);
        }
        if (!empty($error)) {
            $messageError = "Erreur lors de la mise à jour de : " . implode(',', $error);
        }
        $this->showProfile("showProfile", $messageSuccess, $messageError);
    }
}
