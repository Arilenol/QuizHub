<?php
$title = "Signalement envoyé";
$style = './assets/style/quiz/signalement.css';
require_once '../src/views/partials/header.php';
?>

<div class="signalement-page">
    <div style="text-align: center; padding: 40px;">
        <h1 style="color: green;">Votre signalement a bien été envoyé</h1>
        <p style="font-size: 16px; margin: 20px 0;">
            Merci de votre signalement. Notre équipe d'administration sera notifiée et examinera votre demande dans les meilleurs délais.
        </p>
        <a href="?page=home" style="
            display: inline-block;
            padding: 12px 30px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
        ">Retour à l'accueil</a>
    </div>
</div>

</body>

</html>
