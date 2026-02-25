<?php
$title = "Register";
$style = "./assets/style/log.css";
require_once '../src/views/partials/header.php';
?>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>
<div class="button" style="margin : 25px" onclick="history.back()">
    <span></span>
    <p>← Retour</p>
</div>
<div class="log-container">
    <h1>S'inscrire</h1>
    <?php if (isset($error) && !empty($error)) : ?>
        <h3><?= $error; ?></h3>
    <?php endif; ?>
    <form action="?page=log&typelog=register" method="POST">
        <div class="flatinput">
            <span></span>
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
        </div>
        <div class="flatinput">
            <span></span>
            <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="flatinput">
            <span></span>
            <input type="password" id="password" name="password" placeholder="Mot de passe" pattern=^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$
                title="Le mot de passe doit contenir au moins 8 caractères, un caractère spécial, une lettre et un chiffre" required>
            <i id="eyeMdp" class="fa-solid fa-eye fa-2xl"></i>
        </div>
        <div class="flatinput">
            <span></span>
            <input type="password" id="passwordVerif" name="passwordVerif" placeholder="Confirmation du mdp" required>
            <i id="eyeVerif" class="fa-solid fa-eye fa-2xl"></i>
        </div>
        <p>Vous avez un compte ? <a href="?page=log&typelog=connection" class=connexion>Connectez-vous.</a></p>
        <button type="submit" class="button">
            <span></span>
            <p>S'inscrire</p>
        </button>
    </form>
    <div class="back-link">
        <a href="?page=home">← Retour à l'accueil</a>
    </div>
    <script src="./assets/js/mdpScript.js"></script>
</div>
</body>

</html>