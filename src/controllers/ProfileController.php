<?php
require_once ROOT . '/src/models/ProfileModel.php';
require_once ROOT . '/config/config.php';

class ProfileController{

    public function showProfile() {
        $db = getDbConnection();
        $model = new ProfileModel($db);
        session_start();
        if (isset($_SESSION['email'])){
            $user = $model -> getIdUserFromEmail($_SESSION['email']);
            $creation = $model ->getCreationsNumber($user['id']);
            $played = $model -> getGamesNumber($user['id']);
            require ROOT . '/src/views/profil.php';
        } else {
            echo "Problème de chargement";
        }
    }

}
?>