<?php
require_once ROOT . '/src/models/ProfileModel.php';
require_once ROOT . '/config/config.php';

class ProfileController
{

    private ProfileModel $model;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $db = getDbConnection();
        $this->model = new ProfileModel($db);
    }

    public function showProfile(?string $option = null)
    {
        if (isset($_SESSION['id'])) {
            $infosUser = $this->model->getCredentials($_SESSION['id']);
            $creation = $this->model->getCreationsNumber($_SESSION['id']);
            $played = $this->model->getGamesNumber($_SESSION['id']);
            if ($option !== null && $option === "showFriends") {
                $friends = $this->model->getFriends($_SESSION['id']);
            }
            if ($option !== null && $option === "showHistory") {
                $hist = $this->model->getQuizPlayed($_SESSION['id']);
            }
            if ($creation > 0) {
                $quiz = $this->model->getAllCreationsByUser($_SESSION['id']);
            }
            $activeTab = $_GET['action'] ?? 'creations';
            $activeTab = $activeTab === 'displayFriends' ? 'friends' : ($activeTab === 'showHistory' ? 'history' : 'creations');
            require ROOT . '/src/views/profil.php';
        } else {
            echo "Problème de chargement";
        }
    }
}
