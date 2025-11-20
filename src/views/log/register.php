<?php 
$title = "Register";
$style = "./assets/style/log.css";
require_once '../src/views/partials/header.php'; 
?>
    <div class="log-container">
        <h2>S'inscrire</h2>
        <?php if (isset($error) && !empty($error)) : ?>
            <h3><?=$error;?></h3>
        <?php endif;?>
        <form action="?page=log&typelog=register" method="POST">
            <input type="text" name="username" placeholder="Nom d'utilisateur" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <p>Vous avez un compte ? <a href="?page=log&typelog=connection" class=connexion>Connectez-vous.</a></p>
            <button type="submit">S'inscrire</button>
        </form>
        <div class="back-link">
            <a href="?page=home">← Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
