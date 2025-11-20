<?php
require_once ROOT . '/src/models/LogModel.php';
require_once ROOT . '/config/config.php';

class LogController {

    public function showRegister() {
        require ROOT . '/src/views/log/register.php';
    }

    public function showConnection() {
        require ROOT . '/src/views/log/connection.php';
    }

    public function createUser() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $error = "Méthode invalide";
            require ROOT . '/src/views/log/register.php';
            return;
        }
        // Récupération + nettoyage
        $email    = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Vérification des champs obligatoires
        if ($email === '' || $username === '' || $password === '') {
            $error = "Tous les champs doivent être remplis.";
            require ROOT . '/src/views/log/register.php';
            return;
        }

        // Vérification du mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Format d’email invalide.";
            require ROOT . '/src/views/log/register.php';
            return;
        }

        $db = getDbConnection();
        $model = new LogModel($db);

        // Vérifier si l’utilisateur existe déjà
        if ($model->getUserByEmail($email)) {
            $error = "Un compte existe déjà avec cet email.";
            require ROOT . '/src/views/log/register.php';
            return;
        }

        // Création du compte
        $created = $model->createUser($username, $email, $password);

        if ($created) {
            session_start();
            $_SESSION['user_email'] = $email;
            header("Location: ?page=home");
            exit;
        } else {
            $error = "Erreur lors de la création du compte.";
            require ROOT . '/src/views/log/register.php';
            return;
        }
    }

    public function loginUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $error = "Méthode invalide";
            require ROOT . '/src/views/log/connection.php';
            return;
        }
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if ($email === '' || $password === '') {
            $error = "Tous les champs doivent être remplis.";
            require ROOT . '/src/views/log/connection.php';
            return;
        }

        $db = getDbConnection();
        $model = new LogModel($db);

        if ($model->verifyPassword($email, $password)) {
            session_start();
            $_SESSION['email'] = $email;
            header("Location: ?page=home");
            exit;
        } else {
            $error = "Identifiants incorrects.";
            require ROOT . '/src/views/log/connection.php';
        }
    }
}
?>
