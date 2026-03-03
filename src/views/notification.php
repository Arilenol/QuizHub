<?php
$title = "Demande d'amis";
$style = './assets/style/notification.css';
include 'partials/header.php';
?>

<div class="button" style="margin : 25px" onclick="window.location.href='?page=home'">
    <span></span>
    <p>← Retour</p>
</div>
<div class=" friend-requests">
    <h2>Demandes d'amis reçues</h2>
    <?php if (!isset($allFriendRequests) || empty($allFriendRequests)): ?>

        <p class="notif">Aucune demande d'amis reçues</p>

    <?php else: ?>

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
                    <form method="POST" class="inline-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="id" value="<?= $friend["id"] ?>">
                        <button type="submit" class="accept">Accepter</button>
                    </form>

                    <form method="POST" class="inline-form">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= $friend["id"] ?>">
                        <button type="submit" class="reject">Refuser</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="friend-requests">
    <h2>Notifications reçues</h2>
    <?php if (!isset($notifications) || empty($notifications)): ?>

        <p class="notif">Aucune notification reçue</p>

    <?php else: ?>
        <?php foreach ($notifications as $signal) : ?>
            <div class="request">
                <div class="request-info">
                    <div class="notif-content">
                        <strong><?= htmlspecialchars($signal['type']) ?></strong><br>
                        <p class="notif-message">
                            <?= htmlspecialchars($signal['message']) ?>
                        </p>
                        <small class="notif-date"><?= htmlspecialchars($signal['date_creation']) ?></small>
                    </div>
                </div>
                <div class="request-buttons">
                    <?php if (!$signal['type'] === "Demande d'ami"): ?>
                        <button class="toggle-message" data-date="<?= $signal['date_creation'] ?>" data-message="<?= $signal["message"] ?>" data-type="<?= $signal["type"] ?>">Voir plus</button>
                    <?php endif; ?>
                    <button class="reject" onclick='deleteNotification(<?= $signal["id"] ?>)' title="Supprimer">✕</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="invite-box">
    <h2>Envoyer une demande d’ami</h2>

    <?php if (!empty($_SESSION['flash'])): ?>
        <p style="<?= $_SESSION['flash'] === 'Échec de la demande.' ? 'color: red;' : 'color: green;' ?>">
            <?= $_SESSION['flash'];
            unset($_SESSION['flash']); ?>
        </p>
    <?php endif; ?>
    <form method="POST" action="?page=notification">
        <input type="hidden" name="action" value="sendRequest">
        <input type="email" name="email" placeholder="Adresse email de votre ami" required>
        <button type="submit">Envoyer la demande</button>
    </form>

</div>
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <button type="button" class="close-modal" id="closeDeleteModal">
            &times;
        </button>

        <h3 id="type"></h3>

        <p id="message"></p>

        <small id="date"></small>


        <button class="admin-button" onclick="window.location.href='?page=CRUD'">Accéder au CRUD</button>

    </div>
</div>

<script>
    function deleteNotification(notifId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = 'deleteNotif';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = notifId;

        form.appendChild(actionInput);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
    document.querySelectorAll('.toggle-message').forEach(button => {
        button.addEventListener('click', () => {
            const type = document.getElementById("type");
            const message = document.getElementById("message")
            const date = document.getElementById("date")
            console.log(button.dataset.message)
            message.textContent = button.dataset.message
            date.textContent = button.dataset.date

            type.innerHTML = `${button.dataset.type}`

            deleteModal.style.display = 'flex'
        });
    });
    const closeBtn = document.getElementById('closeDeleteModal');
    const modal = document.getElementById("deleteModal")

    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });
</script>

</body>

</html>