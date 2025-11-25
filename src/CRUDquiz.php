<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    echo "<link rel='stylesheet' href='./assets/style/CRUDrecherche.css'>";

    echo "<link rel='stylesheet' href='./assets/style/global.css'>";

    ?>
    <title>CRUD quiz</title>
</head>

<body>

    <button onclick="window.location.href='?page=CRUDrecherche'" class="retour">&lt; Retour</button>


    <div class="newCreations">
        /* modifier pour afficher details quiz*/
        /* ajouter bouton modifier et supprimer*/

        <div class="quiz">
            <article onclick="window.location.href='./QuizPage.php?quiz_id=<?= $quiz[$i]['id'] ?>'">
                <div class="quiz-cat">
                    <?php if (!empty($quiz[$i]['categories'])): ?>
                        <?php foreach ($quiz[$i]['categories'] as $cat): ?>
                            <span class="category"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <p class="quiz-genre"><?= htmlspecialchars($quiz[$i]['genre'] ?? '') ?></p>
                <br>
                <p class="quiz-title"><?= htmlspecialchars($quiz[$i]['title'] ?? '') ?></p>
                <br>
                <p class="quiz-description"><?= htmlspecialchars($quiz[$i]['description'] ?? '') ?></p>
                <br>
                <p class="quiz-auteur">Par : <?= htmlspecialchars($quiz[$i]['user_name'] ?? '') ?></p>
                <br>
                <div class="quiz-footer">
                    <p class="quiz-date">publié le : <?= htmlspecialchars($quiz[$i]['date'] ?? '') ?></p>
                    <div class="quiz-reactions">
                        <span class="reaction like">♥ <?= htmlspecialchars($quiz[$i]['nbjaime'] ?? 0) ?></span>
                        <span class="reaction dislike">♡ <?= htmlspecialchars($quiz[$i]['nbjaimepas'] ?? 0) ?></span>
                    </div>
                </div>
            </article>
        </div>


        /* a modifier pour afficher question de quiz*/
        /* ajouter bouton modifier et supprimer*/
        <?php for ($i = 0; $i < 7; $i++): ?>
            <div class="quiz">
                <article onclick="window.location.href='./QuizPage.php?quiz_id=<?= $quiz[$i]['id'] ?>'">
                    <div class="quiz-cat">
                        <?php if (!empty($quiz[$i]['categories'])): ?>
                            <?php foreach ($quiz[$i]['categories'] as $cat): ?>
                                <span class="category"><?= htmlspecialchars($cat) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <p class="quiz-genre"><?= htmlspecialchars($quiz[$i]['genre'] ?? '') ?></p>
                    <br>
                    <p class="quiz-title"><?= htmlspecialchars($quiz[$i]['title'] ?? '') ?></p>
                    <br>
                    <p class="quiz-description"><?= htmlspecialchars($quiz[$i]['description'] ?? '') ?></p>
                    <br>
                    <p class="quiz-auteur">Par : <?= htmlspecialchars($quiz[$i]['user_name'] ?? '') ?></p>
                    <br>
                    <div class="quiz-footer">
                        <p class="quiz-date">publié le : <?= htmlspecialchars($quiz[$i]['date'] ?? '') ?></p>
                        <div class="quiz-reactions">
                            <span class="reaction like">♥ <?= htmlspecialchars($quiz[$i]['nbjaime'] ?? 0) ?></span>
                            <span class="reaction dislike">♡ <?= htmlspecialchars($quiz[$i]['nbjaimepas'] ?? 0) ?></span>
                        </div>
                    </div>
                </article>
            </div>
        <?php endfor; ?>
    </div>

</body>

</html>