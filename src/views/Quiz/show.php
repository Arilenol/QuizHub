<?php
$title = "Quiz";
$style = './assets/style/quiz/quiz.css';
require_once '../src/views/partials/header.php';
?>

<div class="quiz-réalisation">

    <button class="retour" onclick="window.location.href='?page=home'">← Retour</button>

    <?php if (empty($question)) : ?>

        <!-- FIN DU QUIZ -->
        <div class="question fin-quiz">
            <h2>🎉 Félicitations !</h2>
            <p class="fin">Vous avez terminé le quiz.</p>

            <div class="actions-fin">
                <button class="valider" onclick="window.location.href='?page=home'">Recommencer le quiz</button>
                <button class="valider" onclick="window.location.href='?page=catalogue'">Voir d’autres quiz</button>
                <button class="valider" onclick="window.location.href='?page=home'">Retour à l’accueil</button>
            </div>
        </div>

    <?php else : ?>

        <!-- QUESTION -->
        <div class="question">

            <h2><?= htmlspecialchars($question['question']) ?></h2>

            <div class="ensemble-réponse">

                <form method="get" id="quizForm">

                    <input type="hidden" name="page" value="quiz">
                    <input type="hidden" name="id" value="<?= $question['quiz_id'] ?>">
                    <input type="hidden" name="idQuestion" value="<?= $question['numeroQuiz'] ?>">
                    <input type="hidden" name="reponse" value=<?= !$showAnswer ? 'visible' : '' ?>>

                    <?php foreach ($reponse as $rep) : ?>
                        <?php
                        $inputId   = "rep_" . $rep['id'];
                        $isCorrect = (int) $rep['estCorrecte'] === 1;
                        ?>

                        <div class="réponse">

                            <input type="checkbox"
                                id="<?= $inputId ?>"
                                name="answer[]"
                                value="<?= $rep['id'] ?>"
                                <?= $showAnswer ? 'disabled' : '' ?>>

                            <!-- Affichage coloré si reponse=visible -->
                            <?php if ($showAnswer): ?>
                                <label for="<?= $inputId ?>"
                                    style="cursor: auto;
                                    box-shadow: none;
                                    transform: none;
                                    <?= $isCorrect ? '  background-color: #5bb95bff;
                                    border: 2px solid #3cb43c; ' : '  background-color: #ffb3b3;
                                    border: 2px solid #d62828;' ?>">
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

            <?php if (!$showAnswer): ?>

                <div class="bouton-container">
                    <button class="submit" type="submit" form="quizForm">Valider</button>
                </div>

            <?php else: ?>

                <div class="bouton-container">
                    <button class="valider"
                        onclick="window.location.href='?page=quiz&id=<?= $question['quiz_id'] ?>&idQuestion=<?= $question['numeroQuiz'] + 1 ?>'">
                        Continuer
                    </button>
                </div>

            <?php endif; ?>

        </div>

    <?php endif; ?>

</div>
</body>

</html>