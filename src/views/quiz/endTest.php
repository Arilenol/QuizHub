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
        <?php $_SESSION['rightAnswers'] = 0; ?>
        <?php foreach ($_SESSION['answers'] as $questionNumber => $a) : ?>
            <article <?= $a[0] ? '' : 'class="falseAnswer' ?> onclick="window.location.href='?page=standard&id=<?= $quizId ?>&idQuestion=<?= $questionNumber ?>&reponse=visible&test=test'">
                <p><?= ($a[0]) ? 'Bonne réponse' : 'Mauvaise réponse' ?></p>
                <?php if ($a[0]) {
                    $_SESSION['rightAnswers']++;
                } ?>
                <p>Réponse(s) donnée(s) :
                    <?= implode(', ', $a[1]) ?>
                </p>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<div class="infoAnswer">
    <p>
        Votre note sur ce test :
        <span class="score">
            <?= $_SESSION['rightAnswers'] ?> / <?= count($_SESSION['answers']) ?>
        </span>
    </p>
</div>
<?php $this->saveScore($_SESSION['id'],$quizId, $_SESSION['rightAnswers'].'/'.count($_SESSION['answers'])) ?>

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
        <?php if (!$hasDisliked) : ?>
            <form method="POST" action="?page=test&id=<?= $quizId ?>">
                <button type="submit"
                    name="reaction"
                    value="like"
                    class="like"
                    id=<?= $hasDisliked ? 'disabled' : '' ?>>
                    👍 <?= htmlspecialchars($reactions['nbjaime'] ?? 0) ?>
                </button>
                <input type="hidden" name="idQuestion" value=<?= $idQuestion ?>>
            </form>
        <?php endif; ?>
        <?php if ($hasDisliked) : ?>
            <button type=<?= $hasDisliked ? '' : 'submit' ?>
                name="reaction"
                value="like"
                class="like"
                id=<?= $hasDisliked ? 'disabled' : '' ?>>
                👍 <?= htmlspecialchars($reactions['nbjaime'] ?? 0) ?>
            </button>
        <?php endif; ?>
        <?php if (!$hasLiked) : ?>
            <form method="POST" action="?page=test&id=<?= $quizId ?>">
                <button type='submit'
                    name="reaction"
                    value="dislike"
                    class="dislike"
                    id=<?= $hasLiked ? 'disabled' : '' ?>>
                    👎 <?= htmlspecialchars($reactions['nbjaimepas'] ?? 0) ?>
                </button>
                <input type="hidden" name="idQuestion" value=<?= $idQuestion ?>>
            <?php endif; ?>
            <?php if ($hasLiked) : ?>
                <button type='submit'
                    name="reaction"
                    value="dislike"
                    class="dislike"
                    id=<?= $hasLiked ? 'disabled' : '' ?>>
                    👎 <?= htmlspecialchars($reactions['nbjaimepas'] ?? 0) ?>
                </button>
            <?php endif; ?>
            </form>
        <?php else : ?>

            <p>Pour débloquer cette fonctionnalité <a href="?page=log&typelog=connection"> Connectez-vous</a> d'abord</p>

        <?php endif; ?>
</div>

</body>

</html>