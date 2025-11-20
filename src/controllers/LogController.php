<?php
require_once ROOT . '/src/models/LessonModel.php';
require_once ROOT . '/config/config.php';
class LogController{

    public function showRegister(){
        require ROOT . '/src/views/log/register.php';
    }

    public function showConnection() {
        require ROOT . '/src/views/log/connection.php';
    }

    public function loginUser(){
        $db = getDbConnection();
        $model = new HomeModel($db);
    }

    public function createUser(){
        if ($_SERVER['REQUEST_METHOD']==='POST'){
            if (isset($_POST['email']) && !str_contains($_POST['email'],'@')){
                $error = "Le mail doit être sous la forme 'xxx@xx.'";
                require ROOT . '/src/views/log/register.php';
            }

            //Tous les champs nécéssaires à la création du compte
            $fields = ['email', 'username', 'password'];
            // On regarde si pour chaque champ on a une variable associée
            foreach ($fields as $f) {
            if (isset($_POST[$f]) || !trim($_POST[$f]) === "") {
                $email = htmlspecialchars($_POST['email']);
                $password = htmlspecialchars($_POST['password']);
                $username = htmlspecialchars($_POST['username']);
                $db = getDbConnection();
                $model = new LogModel($db);
                if ($this->userModel->verifyPassword($email, $password)) {
                    // Connexion réussie
                    session_start();
                    $_SESSION['user_email'] = $email;
                    header('Location: ?page=home');
                    exit;
            }
            } else {
                $error = "Problème dans la saisie des identifiants";
                require ROOT . '/src/views/log/register.php';
            }
            }
        }
    }
}
?>