<?php
$title = "Quiz";
$style = './assets/style/quiz/quiz.css';
require_once '../src/views/partials/header.php';
?>

<div class="quiz-réalisation">

    <button class="retour" onclick="window.location.href='?page=home'">← Retour</button>

    <?php if (empty($question)) : ?>

        <!-- PAGE FIN DE QUIZ -->
        <div class="question fin-quiz">
            <h2>🎉 Félicitations !</h2>
            <p>Vous avez terminé le quiz.</p>

            <div class="actions-fin">
                <button class="valider" onclick="window.location.href='?page=home'">Retour à l’accueil</button>
                <button class="valider" onclick="window.location.href='?page=catalogue'">Voir d’autres quiz</button>
            </div>
        </div>

    <?php else : ?>

        <!-- PAGE QUESTION -->
        <div class="question">

            <h2><?= htmlspecialchars($question['question']) ?></h2>

            <div class="ensemble-réponse">
                <form action="?page=quiz&reponse=visible" method="get">
                    <?php foreach ($reponse as $rep) : ?>
                        <?php $inputId = "reponse_" . $rep['id']; ?>

                        <div class="réponse">
                            <input type="checkbox"
                                id="<?= $inputId ?>"
                                name="answer"
                                value="<?= $rep['id'] ?>"
                                required>

                            <label for="<?= $inputId ?>">
                                <?= htmlspecialchars($rep['reponse']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <input type="hidden" name="id" value=<?= $question['quiz_id'] ?>>
                    <input type="hidden" name="idQuestion" value=<?= $question['id'] ?>>
                    <button type="submit">Envoyer</button>
                </form>
            </div>
            <button class="valider" onclick="window.location.href='?page=quiz&id=<?= $question['quiz_id'] ?>&idQuestion=<?= (int) $question['id'] + 1 ?>'">Valider</button>
        </div>

    <?php endif; ?>

</div>
</body>

</html>