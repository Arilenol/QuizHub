<?php
$title = "Fin de test";
$style = './assets/style/quiz/endTest.css';
require_once '../src/views/partials/header.php';
?>

<div class="quiz-réalisation">
    <button class="retour" onclick="history.back()">← Retour</button>
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
</div>