<?php
$title = "Accueil";
$style = './assets/style/home.css';
include 'partials/header.php';
?>


<?php if (isset($_SESSION['id']) && !empty($_SESSION['id'])): ?>
    <h1>Vos créations</h1>
<?php else :  ?>
    <h1>Créations récentes</h1>
<?php endif; ?>

<?php if (empty($quizNextPart)): ?>
    <p class="no-content">Vous n'avez encore créé aucune ressource.</p>
<?php else: ?>
    <div class="newCreations">
        <?php for ($i = 0; $i < count($quizNextPart); $i++): ?>
            <article onclick="window.location.href='./?page=<?= $quizNextPart[$i]['genre'] == 'standard' || $quizNextPart[$i]['genre'] == 'test' ? 'pageInterQuiz' : $quizNextPart[$i]['genre'] ?>&id=<?= $quizNextPart[$i]['id'] ?> <?= $quizNextPart[$i]['genre'] == 'lesson' ? '&categorie=view' : '' ?> <?= $quizNextPart[$i]['genre'] == 'flashcard' ? '&action=start' : '' ?> <?= $quizNextPart[$i]['genre'] == 'standard' ? '&type=standard' : '' ?> <?= $quizNextPart[$i]['genre'] == 'test' ? '&type=test' : '' ?>'" class="quiz">
                <div class="quiz-cat">
                    <?php if (!empty($quizNextPart[$i]['categories'])): ?>
                        <?php foreach ($quizNextPart[$i]['categories'] as $cat): ?>
                            <span class="category"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <p class="quiz-genre"><?= htmlspecialchars($quizNextPart[$i]['genre'] ?? '') ?></p>
                <br>
                <p class="quiz-title"><?= htmlspecialchars($quizNextPart[$i]['title'] ?? '') ?></p>
                <br>
                <p class="quiz-description"><?= htmlspecialchars($quizNextPart[$i]['description'] ?? '') ?></p>
                <br>

                <br>
                <div class="quiz-footer">
                    <p class="quiz-auteur">Par : <span class="nom-auteur"> <?= htmlspecialchars($quizNextPart[$i]['user_name'] ?? '') ?></span></p>
                    <p class="quiz-date">Publié le : <?= htmlspecialchars($quizNextPart[$i]['date'] ?? '') ?></p>
                    <div class="quiz-reactions">
                        <span class="reaction like">👍 <?= htmlspecialchars($quizNextPart[$i]['nbjaime'] ?? 0) ?></span>
                        <span class="reaction dislike">👎 <?= htmlspecialchars($quizNextPart[$i]['nbjaimepas'] ?? 0) ?></span>
                    </div>
                </div>
            </article>

        <?php endfor; ?>

    </div>
<?php endif; ?>

<?php if (isset($_SESSION['id']) && !empty($_SESSION['id'])): ?>
    <h1>Les créations de mes amis</h1>
    <?php if (isset($friendQuiz) && !empty($friendQuiz)): ?>
        <div class="newCreations">
            <?php for ($i = 0; $i < count($friendQuiz); $i++): ?>
                <article onclick="window.location.href='./?page=<?= $friendQuiz[$i]['genre'] == 'standard' || $friendQuiz[$i]['genre'] == 'test' ? 'pageInterQuiz' : $friendQuiz[$i]['genre'] ?>&id=<?= $friendQuiz[$i]['id'] ?> <?= $friendQuiz[$i]['genre'] == 'lesson' ? '&categorie=view' : '' ?> <?= $friendQuiz[$i]['genre'] == 'flashcard' ? '&action=start' : '' ?> <?= $friendQuiz[$i]['genre'] == 'standard' ? '&type=standard' : '' ?> <?= $friendQuiz[$i]['genre'] == 'test' ? '&type=test' : '' ?>'" class="quiz">
                    <div class="quiz-cat">
                        <?php if (!empty($friendQuiz[$i]['categories'])): ?>
                            <?php foreach ($friendQuiz[$i]['categories'] as $cat): ?>
                                <span class="category"><?= htmlspecialchars($cat) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <p class="quiz-genre"><?= htmlspecialchars($friendQuiz[$i]['genre'] ?? '') ?></p>
                    <br>
                    <p class="quiz-title"><?= htmlspecialchars($friendQuiz[$i]['title'] ?? '') ?></p>
                    <br>
                    <p class="quiz-description"><?= htmlspecialchars($friendQuiz[$i]['description'] ?? '') ?></p>
                    <br>

                    <br>
                    <div class="quiz-footer">
                        <p class="quiz-auteur">Par : <span class="nom-auteur"> <?= htmlspecialchars($friendQuiz[$i]['user_name'] ?? '') ?></span></p>
                        <p class="quiz-date">Publié le : <?= htmlspecialchars($friendQuiz[$i]['date'] ?? '') ?></p>
                        <div class="quiz-reactions">
                            <span class="reaction like">👍 <?= htmlspecialchars($friendQuiz[$i]['nbjaime'] ?? 0) ?></span>
                            <span class="reaction dislike">👎 <?= htmlspecialchars($friendQuiz[$i]['nbjaimepas'] ?? 0) ?></span>
                        </div>
                    </div>
                </article>
            <?php endfor; ?>
        </div>
    <?php else : ?>
        <p class="no-content">Vos amis n'ont créé aucune ressource.</p>
    <?php endif; ?>
<?php endif; ?>


<h1>Créations populaires</h1>

<div class="newCreations">
    <?php for ($i = 0; $i < count($quiz); $i++): ?>
        <article onclick="window.location.href='./?page=<?= $quiz[$i]['genre'] == 'standard' || $quiz[$i]['genre'] == 'test' ? 'pageInterQuiz' : $quiz[$i]['genre'] ?>&id=<?= $quiz[$i]['id'] ?> <?= $quiz[$i]['genre'] == 'flashcard' ? '&action=start' : '' ?> <?= $quiz[$i]['genre'] == 'standard' ? '&type=standard' : '' ?> <?= $quiz[$i]['genre'] == 'test' ? '&type=test' : '' ?>'" class="quiz">
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

            <br>
            <div class="quiz-footer">
                <p class="quiz-auteur">Par : <span class="nom-auteur"> <?= htmlspecialchars($quiz[$i]['user_name'] ?? '') ?> </span></p>
                <p class="quiz-date">Publié le : <?= htmlspecialchars($quiz[$i]['date'] ?? '') ?></p>
                <div class="quiz-reactions">
                    <span class="reaction like">👍 <?= htmlspecialchars($quiz[$i]['nbjaime'] ?? 0) ?></span>
                    <span class="reaction dislike">👎 <?= htmlspecialchars($quiz[$i]['nbjaimepas'] ?? 0) ?></span>
                </div>
            </div>
        </article>
    <?php endfor; ?>

</div>

<h1>Leçons populaires</h1>

<div class="newCreations">
    <?php for ($i = 0; $i < count($lessons); $i++): ?>
        <article onclick="window.location.href='./?page=lesson&categorie=view&id=<?= $lessons[$i]['lecon_id'] ?>'" class="quiz">
            <div class="quiz-cat">
                <?php if (!empty($lessons[$i]['categories'])): ?>
                    <?php foreach ($lessons[$i]['categories'] as $cat): ?>
                        <span class="category"><?= htmlspecialchars($cat) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <p class="quiz-genre"> leçon </p>
            <br>
            <p class="quiz-title"><?= htmlspecialchars($lessons[$i]['lecon_title'] ?? '') ?></p>
            <br>
            <p class="quiz-description"><?= htmlspecialchars($lessons[$i]['lecon_description'] ?? '') ?></p>
            <br>

            <br>
            <div class="quiz-footer">
                <p class="quiz-auteur">Par : <span class="nom-auteur"> <?= htmlspecialchars($lessons[$i]['user_name'] ?? '') ?></span></p>
                <p class="quiz-date">Publié le : <?= htmlspecialchars($lessons[$i]['lecon_date'] ?? '') ?></p>
                <div class="quiz-reactions">
                    <span class="reaction like">👍 <?= htmlspecialchars($lessons[$i]['nbjaime'] ?? 0) ?></span>
                    <span class="reaction dislike">👎 <?= htmlspecialchars($lessons[$i]['nbjaimepas'] ?? 0) ?></span>
                </div>
            </div>
        </article>
    <?php endfor; ?>

</div>

<div class="endDirection">
    <button onclick="window.location.href='?page=catalogue'">Voir plus</button>
</div>
</body>

</html>