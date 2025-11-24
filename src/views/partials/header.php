<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 
        if (isset($style)){
            echo "<link rel='stylesheet' href='$style'>";
        }
        echo "<link rel='stylesheet' href='./assets/style/global.css'>";

    ?>
    <title><?= $title ?></title>
</head>
<body>
<header>
    <img src="./assets/images/logo.svg" alt="Logo">
    <div class="searchbar">
        <span></span>
        <img src="./assets/images/loupe.svg" alt="Search Icon">
        <p>Rechercher des créations...</p>
    </div>
    <div style="display: flex; flex-direction: row; gap: 25px;">
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        ?>
        <?php if (!isset($_SESSION['email'])): ?>
            <button class="button" data-action="register">
                <span></span>
                <p>S'inscrire</p>
        </button>
            <button class="button" data-action="login">
                <span></span>
                <p>Connexion</p>
            </button>
        <?php else: ?>
            <button class="button" style="padding: 15px;" data-action="account">
                <span></span>
                <p>Mon compte</p>
            </div>
        <?php endif; ?>
    </div>
</header>
<!-- Création des listeners des boutons du header -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('header .button').forEach(btn => {
        const action = btn.dataset.action;

        if (!action) return;

        btn.addEventListener('click', () => {
            switch(action) {
                case 'register':
                    window.location.href = '?page=log&typelog=register';
                    break;
                case 'login':
                    window.location.href = '?page=log&typelog=connection';
                    break;
                case 'account':
                    window.location.href = '?page=profil';
                    break;
            }
        });
    });
});
</script>
