<?php
$title = "Quiz";
$style = './assets/style/quiz/quiz.css';
require_once '../src/views/partials/header.php';
?>

<div class="quiz-réalisation">
    <div class="buttonAction">
        <?php if (isset($_GET['test'])): ?>
            <button type="submit" class="retour" form="retourForm">
                ← Retour
            </button>

            <form id="retourForm" method="post" action="?page=pageInterQuiz&id=<?= $quizId ?>&type=test">
                <input type="hidden" name="idQuestion" value="<?= $max +1 ?>">
            </form>

        <?php else: ?>
            <button class="retour" onclick="window.location.href='?page=home'">
                🏠 Accueil
            </button>
        <?php endif; ?>
        <?php if (!isset($_GET['test']) && ($idQuestion <= $max)): ?>
            <div class="progression">
                <progress class="progress-container" value="<?= $idQuestion ?>" max="<?= $max ?>"></progress>
                <p><?= $idQuestion ?>/<?= $max ?> question(s) réalisée(s)</p>
            </div>
        <?php endif; ?>
        <button class="button signalement" onclick="window.location.href='?page=signalement'">
            <span></span>
            <p>Signaler ce quiz</p>
        </button>
    </div>
    <?php if (($_GET['page']) === 'standard' && (isset($_GET['idQuestion']) && ($_GET['idQuestion']) > 1)
        || (($_GET['page']) === 'standard' && (isset($_GET['reponse']) && ($_GET['reponse']) === 'visible'))
    ): ?>
        <?php if (!isset($_GET['test'])) : ?>
            <button class="retourBis" style="align-self: start; " onclick="history.back()">← Revenir en arrière</button>
        <?php endif; ?>
    <?php endif; ?>
    <?php if (empty($question)) : ?>


        <!-- FIN DU QUIZ -->
        <div class="question fin-quiz">
            <h2>🎉 Félicitations !</h2>
            <p class="fin">Vous avez terminé le quiz.</p>

            <?php if ($_GET['page'] === 'standard') : ?>
                <div class="actions-fin">
                    <button class="button" onclick="window.location.href='?page=pageInterQuiz&id=<?= $quizId ?>&type=standard'"><span></span>
                        <p>Recommencer le quiz</p>
                    </button>
                    <button class="button" onclick="window.location.href='?page=catalogue'"><span></span>
                        <p>Voir d’autres quiz</p>
                    </button>
                    <button class="button" onclick="window.location.href='?page=home'"><span></span>
                        <p>Retour à l’accueil</p>
                    </button>
                </div>
                <p class="fin">Vous avez aimé le quiz ? N'hésiter pas à le noter : </p>
                <div class="quiz-reactions">
                    <?php if (isset($_SESSION['id'])) : ?>
                        <?php if (!$hasDisliked) : ?>
                            <form method="POST" action="?page=standard&id=<?= $quizId ?>&idQuestion=<?= $idQuestion ?>">
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
                            <form method="POST" action="?page=standard&id=<?= $quizId ?>&idQuestion=<?= $idQuestion ?>">
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
            <?php if (!isset($_GET['test'])): ?>

                <?php if (!$showAnswer && !$isTest): ?>
                    <!-- STANDARD → première étape → validateur -->
                    <button class="button" type="submit" form="quizForm" style="margin-top: 20px;">
                        <span></span>
                        <p>Valider</p>
                    </button>

                <?php elseif ($isTest): ?>
                    <!-- TEST → toujours POST -->
                    <button class="button" type="submit" form="quizForm" style="margin-top: 20px;">
                        <span></span>
                        <p>Continuer</p>
                    </button>

                <?php else: ?>
                    <!-- STANDARD → réponse affichée → suivant en GET -->
                    <button class="button" onclick="window.location.href='?page=standard&id=<?= $question['quiz_id'] ?>&idQuestion=<?= $question['numeroQuiz'] + 1 ?>'" style="margin-top: 20px;">
                        <span></span>
                        <p>Continuer</p>
                    </button>
                <?php endif; ?>
            <?php endif; ?>

        </div>

    </div>

<?php endif; ?>

</div>
</body>

</html>