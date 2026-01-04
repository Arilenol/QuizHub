<?php
$title = "Fin de test";
$style = './assets/style/quiz/endTest.css';
require_once '../src/views/partials/header.php';
?>

<div class="quiz-réalisation">
    <button class="button" style="margin-top: 20px; margin-left: 20px;" onclick="window.location.href='?page=home'">
        <span></span>
        <p>Retour à l'accueil</p>
    </button>
    <h2>Vos réponses</h2>
    <div class="answers">
        <?php foreach ($_SESSION['answers'] as $questionNumber => $a) : ?>
            <article onclick="window.location.href='?page=standard&id=<?= $quizId ?>&idQuestion=<?= $questionNumber ?>&reponse=visible&test=test'">
                <p><?= ($a[0]) ? 'Bonne réponse' : 'Mauvaise réponse' ?></p>

                <p>Réponse(s) donnée(s) :
                    <?= implode(', ', $a[1]) ?>
                </p>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<div class="actions-fin">
    <button class="button" onclick="window.location.href='?page=test&id=<?= $quizId ?>'">
        <span></span>
        <p>Recommencer le quiz</p>
    </button>
    <button class="button" onclick="window.location.href='?page=catalogue'">
        <span></span>
        <p>Voir d’autres quiz</p>
    </button>
    <button class="button" onclick="window.location.href='?page=home'">
        <span></span>
        <p>Retour à l’accueil</p>
    </button>
</div>
<p class="fin">Vous avez aimé le quiz ? N'hésiter pas à le noter : </p>
<div class="quiz-reactions">
    <?php if (isset($_SESSION['id'])) : ?>
        <form method="POST" action="?page=test&action=end&id=<?= $quizId ?>">
            <button type="submit" name="reaction" value="like" class="reaction like">
                👍 <?= htmlspecialchars($reactions['nbjaime'] ?? 0) ?>
            </button>
        </form>
        <form method="POST" action="?page=test&action=end&id=<?= $quizId ?>">
            <button type="submit" name="reaction" value="dislike" class="reaction dislike">
                👎 <?= htmlspecialchars($reactions['nbjaimepas'] ?? 0) ?>
            </button>
        </form>
    <?php else : ?>

        <p>Pour débloquer cette fonctionnalité <a href="?page=log&typelog=connection"> Connectez-vous</a> d'abord</p>

    <?php endif; ?>
</div>