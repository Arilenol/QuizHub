<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php 

        echo "<link rel='stylesheet' href='./assets/style/home.css'>";
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
        <div class="button">
            <span></span>
            <p>S'inscrire</p>
        </div>
        <div class="button">
            <span></span>
            <p>Connexion</p>
        </div>
    </div>
</header>