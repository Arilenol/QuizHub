<?php
$title = "Fin de test";
$style = './assets/style/quiz/endTest.css';
require_once '../src/views/partials/header.php';
?>

<div class="quiz-réalisation">
    <button class="retour" onclick="window.location.href='?page=home'">Retour à l'accueil</button>
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
    <button class="valider" onclick="window.location.href='?page=test&id=<?= $quizId ?>'">Recommencer le quiz</button>
    <button class="valider" onclick="window.location.href='?page=catalogue'">Voir d’autres quiz</button>
    <button class="valider" onclick="window.location.href='?page=home'">Retour à l’accueil</button>
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