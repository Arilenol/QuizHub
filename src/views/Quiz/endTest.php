<?php
$title = "Fin de test";
$style = './assets/style/quiz/endTest.css';
require_once '../src/views/partials/header.php';
?>

<div class="end">
    <h2>Vos réponses</h2>
    <div class="answers">
        <?php var_dump($_SESSION) ?>
        <?php foreach ($_SESSION['answers'] as $questionNumber => $a) : ?>
            <article onclick="window.location.href='?page=standard&idQuestion=<?= $questionNumber ?>&reponse=visible'">
                <p><?= ($a[0]) ? 'Bonne réponse' : 'Mauvaise réponse' ?></p>
            </article>
        <?php endforeach; ?>

    </div>


</div>