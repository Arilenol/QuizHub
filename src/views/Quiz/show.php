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
            <p>Vous avez terminé le quiz.</p>

            <div class="actions-fin">
                <button class="valider" onclick="window.location.href='?page=home'">Retour à l’accueil</button>
                <button class="valider" onclick="window.location.href='?page=catalogue'">Voir d’autres quiz</button>
            </div>
        </div>

    <?php else : ?>

        <!-- QUESTION -->
        <div class="question">

            <h2><?= htmlspecialchars($question['question']) ?></h2>

            <div class="ensemble-réponse">
                <form method="get">


                    <?php foreach ($reponse as $rep) : ?>
                        <?php
                        $inputId = "rep_" . $rep['id'];
                        $isCorrect = (int)$rep['estCorrecte'] === 1;
                        ?>

                        <div class="réponse">
                            <input type="checkbox"
                                id="<?= $inputId ?>"
                                name="answer[]"
                                value="<?= $rep['id'] ?>"
                                <?= $showAnswer ? 'disabled' : '' ?>>

                            <?php if ($showAnswer): ?>
                                <?php if ($isCorrect): ?>
                                    <label for="<?= $inputId ?>" class="correct"><?= htmlspecialchars($rep['reponse']) ?></label>
                                <?php else: ?>
                                    <label for="<?= $inputId ?>" class="wrong"><?= htmlspecialchars($rep['reponse']) ?></label>
                                <?php endif; ?>
                            <?php else: ?>
                                <label for="<?= $inputId ?>"><?= htmlspecialchars($rep['reponse']) ?></label>
                            <?php endif; ?>
                        </div>

                    <?php endforeach; ?>


                <?php endif; ?>
                <?php if (!$showAnswer): ?>
                    <button class="submit" type="submit">Valider</button>
                <?php else: ?>
                    <button class="valider"
                        onclick="window.location.href='?page=quiz&id=<?= $question['quiz_id'] ?>&idQuestion=<?= $question['numeroQuiz'] + 1 ?>'">
                        Continuer
                    </button>

                </form>
            </div>

        </div>

    <?php endif; ?>

</div>

</body>

</html>