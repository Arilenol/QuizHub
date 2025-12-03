<?php
$title = "Demande d'amis";
$style = './assets/style/notification.css';
include 'partials/header.php';
?>

<h1>Gestion des demandes d'amis</h1>

<div class="friend-requests">
    <h2>Demandes reçues</h2>

    <?php foreach ($allFriendRequests as $friend) : ?>
        <div class="request">
            <div class="request-info">
                <div class="avatar"><?= $friend['username'][0] ?></div>
                <div>
                    <strong><?= $friend['username'] ?></strong><br>
                    <small><?= $friend['email'] ?></small>
                </div>
            </div>
            <div class="request-buttons">
                <button class="accept" onclick='window.location.href="?page=notification&action=add&id=<?= $friend["id"] ?>"'>Accepter</button>
                <button class="reject" onclick='window.location.href="?page=notification&action=delete&id=<?= $friend["id"] ?>"'>Refuser</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="invite-box">
    <h2>Envoyer une demande d’ami</h2>

    <?php
    if (isset($message)) {
        if ($message) {
            echo '<p style="color: green;">Demande envoyées avec succès</p>';
        } else {
            echo '<p style="color: red;">Echec de la demande (demande déjà envoyée ou email inexistant)</p>';
        }
    }
    ?>
    <form method="get" action="">
        <input type="hidden" name="page" value="notification">
        <input type="email" name="email" placeholder="Adresse email de votre ami" required>
        <button type="submit">Envoyer la demande</button>
    </form>

</div>

</body>

</html>