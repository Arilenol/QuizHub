<?php
$title = "Quiz";
$style = './assets/style/quiz/quiz.css';
require_once '../src/views/partials/header.php';
?>

<div class="quiz-réalisation">
    <div class="buttonAction">
        <?php if (isset($_GET['test'])): ?>
            <button type="button" class="retour" onclick="window.location.href='?page=test&id=<?= $quizId ?>'">
                ← Retour
            </button>

        <?php else: ?>
            <button class="retour" onclick="window.location.href='?page=home'">← Retour</button>
        <?php endif; ?>
        <button class="signalement" onclick="window.location.href='?page=signalement'">Signaler ce quiz</button>
    </div>
    <?php if (empty($question)) : ?>


        <!-- FIN DU QUIZ -->
        <div class="question fin-quiz">
            <h2>🎉 Félicitations !</h2>
            <p class="fin">Vous avez terminé le quiz.</p>

            <?php if ($_GET['page'] === 'standard') : ?>
                <div class="actions-fin">
                    <button class="valider" onclick="window.location.href='?page=standard&id=<?= $quizId ?>'">Recommencer le quiz</button>
                    <button class="valider" onclick="window.location.href='?page=catalogue'">Voir d’autres quiz</button>
                    <button class="valider" onclick="window.location.href='?page=home'">Retour à l’accueil</button>
                </div>
                <p class="fin">Vous avez aimé le quiz ? N'hésiter pas à le noter : </p>
                <div class="quiz-reactions">
                    <?php if (isset($_SESSION['id'])) : ?>
                        <form method="POST" action="?page=standard&idQuestion=<?= $idQuestion ?>&id=<?= $quizId ?>">
                            <button type="submit" name="reaction" value="like" class="reaction like">
                                👍 <?= htmlspecialchars($reactions['nbjaime'] ?? 0) ?>
                            </button>
                        </form>
                        <form method="POST" action="?page=standard&idQuestion=<?= $idQuestion ?>&id=<?= $quizId ?>">
                            <button type="submit" name="reaction" value="dislike" class="reaction dislike">
                                👎 <?= htmlspecialchars($reactions['nbjaimepas'] ?? 0) ?>
                            </button>
                        </form>

                    <?php else : ?>

                        <p>
                            Pour débloquer cette fonctionnalité
                            <a href="?page=log&typelog=connection">Connectez-vous</a> d'abord
                        </p>

                    <?php endif; ?>
                </div>

        </div>
    <?php endif; ?>
</div>

<?php else : ?>

    <div class="question">

        <h2><?= htmlspecialchars($question['question']) ?></h2>

        <div class="ensemble-réponse">
            <?php $oddClass = (count($reponse) % 2 !== 0) ? ' odd' : ''; ?>

            <?php $isTest = ($_GET['page'] === 'test'); ?>

            <form method="<?= $isTest ? 'post' : 'get' ?>" id="quizForm" class="<?= $oddClass ?>">

                <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page']) ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($question['quiz_id']) ?>">
                <input type="hidden" name="idQuestion" value="<?= htmlspecialchars($question['numeroQuiz']) ?>">

                <!-- Seulement en mode standard -->
                <?php if (!$isTest): ?>
                    <input type="hidden" name="reponse" value="<?= !$showAnswer ? 'visible' : '' ?>">
                <?php endif; ?>


                <!-- Boucle des réponses -->
                <?php foreach ($reponse as $rep): ?>
                    <?php
                    $inputId   = "rep_" . $rep['id'];
                    $isCorrect = ((int) $rep['estCorrecte'] === 1);
                    ?>

                    <div class="réponse">

                        <input
                            type="checkbox"
                            id="<?= $inputId ?>"
                            name="<?= $isTest ? 'answer[]' : 'rep[]' ?>"
                            value="<?= $rep['id'] ?>"
                            <?= $showAnswer ? 'disabled' : '' ?>>

                        <?php if ($showAnswer && !$isTest): ?>
                            <label
                                for="<?= $inputId ?>"
                                style="
                                cursor: auto;
                                box-shadow: none;
                                transform: none;
                                <?= $isCorrect
                                    ? 'background-color:#5bb95b;border:2px solid #3cb43c;'
                                    : 'background-color:#ffb3b3;border:2px solid #d62828;'
                                ?>
                            ">
                                <?= htmlspecialchars($rep['reponse']) ?>
                            </label>

                        <?php else: ?>
                            <label for="<?= $inputId ?>">
                                <?= htmlspecialchars($rep['reponse']) ?>
                            </label>
                        <?php endif; ?>

                    </div>

                <?php endforeach; ?>

            </form>
        </div>


        <!-- Bouton validation -->
        <div class="bouton-container">

            <?php if (!$showAnswer && !$isTest): ?>
                <!-- STANDARD → première étape → validateur -->
                <button class="submit" type="submit" form="quizForm">Valider</button>

            <?php elseif ($isTest): ?>
                <!-- TEST → toujours POST -->
                <button class="valider" type="submit" form="quizForm">Continuer</button>

            <?php else: ?>
                <!-- STANDARD → réponse affichée → suivant en GET -->
                <button
                    class="valider"
                    onclick="window.location.href='?page=standard&id=<?= $question['quiz_id'] ?>&idQuestion=<?= $question['numeroQuiz'] + 1 ?>'">
                    Continuer
                </button>
            <?php endif; ?>

        </div>

    </div>

<?php endif; ?>

</div>
</body>

</html>