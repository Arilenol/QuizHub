<?php 
$title = "Connexion";
$style = "./assets/style/log.css";
require_once '../src/views/partials/header.php'; 
?>
    <div class="log-container">
        <h1>Se connecter</h1>
        <?php if (isset($error) && !empty($error)) : ?>
            <h3><?=$error;?></h3>
        <?php endif;?>
        <form action="?page=log&typelog=connection" method="POST">
            <div class="flatinput">
                <span></span>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="flatinput">
                <span></span>
                <input type="password" name="password" placeholder="Mot de passe" required>
            </div>
            <p>Vous n'avez pas de compte ? <a href="?page=log&typelog=register" class=connexion>Inscrivez-vous.</a></p>
            <button type="submit" class="button">
                <span></span>
                <p>Se connecter</p>
            </button>
        </form>
        <div class="back-link">
            <a href="?page=home">← Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
