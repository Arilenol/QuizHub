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
    <img onclick="window.location.href='/'" style="cursor: pointer;" src="./assets/images/logo.svg" alt="Logo">
    <div class="input">
        <span></span>
        <img src="./assets/images/loupe.svg" alt="Search Icon">
        <input type="text" placeholder="Rechercher des créations..."/>
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
                <svg width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M35 0C15.6702 0 0 15.6702 0 35C0 54.3298 15.6702 70 35 70C54.3298 70 70 54.3298 70 35C70 15.6702 54.3298 0 35 0ZM4.88372 35C4.88401 30.1471 6.05705 25.3661 8.30295 21.0641C10.5489 16.7622 13.8011 13.0667 17.7828 10.2923C21.7645 7.51795 26.3576 5.74688 31.1712 5.1299C35.9847 4.51292 40.8761 5.06832 45.4287 6.7488C49.9814 8.42928 54.0605 11.1851 57.3188 14.7815C60.5771 18.378 62.918 22.7085 64.1423 27.4045C65.3665 32.1005 65.4378 37.0227 64.35 41.7522C63.2622 46.4816 61.0477 50.8782 57.8949 54.5674C57.1875 52.081 55.8793 49.8067 54.0856 47.9451C51.5395 45.2981 48.3553 43.7581 45.5944 42.8986C43.5563 42.2637 41.6353 42.9963 40.3721 43.8526C39.2 44.6405 37.3116 45.5814 35 45.5814C32.6884 45.5814 30.8 44.6372 29.6279 43.8526C28.3647 42.9995 26.4437 42.2637 24.4056 42.8986C21.6414 43.7581 18.4605 45.2981 15.9144 47.9451C14.1211 49.8058 12.813 52.0789 12.1051 54.5642C7.43222 49.1183 4.86967 42.1759 4.88372 35ZM16.3214 58.6242C16.5688 55.3326 17.8191 53.0112 19.4372 51.3279C21.2377 49.4526 23.6047 48.2642 25.8577 47.5609C25.9684 47.5348 26.084 47.5382 26.193 47.5707C26.4445 47.6393 26.6824 47.7505 26.8963 47.8995C28.5274 49 31.3698 50.4651 35 50.4651C38.6302 50.4651 41.4726 49 43.1037 47.8995C43.3178 47.7517 43.5557 47.6415 43.807 47.574C43.9157 47.5404 44.0313 47.5359 44.1423 47.5609C46.3953 48.2642 48.7623 49.4526 50.5628 51.3279C52.1809 53.0112 53.4279 55.3326 53.6786 58.6242C48.3668 62.8403 41.7816 65.1291 35 65.1163C28.2184 65.1291 21.6332 62.8403 16.3214 58.6242ZM27.6744 28.4884C27.6744 24.4186 30.8326 21.1628 35 21.1628C39.1674 21.1628 42.3256 24.4186 42.3256 28.4884C42.3256 32.5581 39.1674 35.814 35 35.814C30.8326 35.814 27.6744 32.5581 27.6744 28.4884ZM35 16.2791C33.393 16.2661 31.7995 16.573 30.3123 17.1819C28.8252 17.7909 27.4741 18.6897 26.3377 19.8261C25.2014 20.9624 24.3025 22.3135 23.6936 23.8007C23.0846 25.2879 22.7777 26.8814 22.7907 28.4884C22.7777 30.0953 23.0846 31.6888 23.6936 33.176C24.3025 34.6632 25.2014 36.0143 26.3377 37.1507C27.4741 38.287 28.8252 39.1858 30.3123 39.7948C31.7995 40.4038 33.393 40.7107 35 40.6977C36.607 40.7107 38.2005 40.4038 39.6876 39.7948C41.1748 39.1858 42.5259 38.287 43.6623 37.1507C44.7986 36.0143 45.6975 34.6632 46.3064 33.176C46.9154 31.6888 47.2223 30.0953 47.2093 28.4884C47.2223 26.8814 46.9154 25.2879 46.3064 23.8007C45.6975 22.3135 44.7986 20.9624 43.6623 19.8261C42.5259 18.6897 41.1748 17.7909 39.6876 17.1819C38.2005 16.573 36.607 16.2661 35 16.2791Z" fill="white"/>
                </svg>
        </button>
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
