<?php
$title = "Accueil";
$style = './assets/style/home.css';
include 'partials/header.php';
?>

<h1>Créations populaires</h1>

<div class="newCreations">
    <?php for ($i = 0; $i < count($quiz); $i++): ?>
        <article onclick="window.location.href='./?page=<?= $quiz[$i]['genre'] ?>&id=<?= $quiz[$i]['id'] ?> <?= $quiz[$i]['genre'] == 'flashcard' ? '&action=start' : '' ?>'" class="quiz">
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
                <p class="quiz-auteur">Par : <?= htmlspecialchars($quiz[$i]['user_name'] ?? '') ?></p>
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
                <p class="quiz-auteur">Par : <?= htmlspecialchars($lessons[$i]['user_name'] ?? '') ?></p>
                <p class="quiz-date">Publié le : <?= htmlspecialchars($lessons[$i]['lecon_date'] ?? '') ?></p>
                <div class="quiz-reactions">
                    <span class="reaction like">👍 <?= htmlspecialchars($lessons[$i]['nbjaime'] ?? 0) ?></span>
                    <span class="reaction dislike">👎 <?= htmlspecialchars($lessons[$i]['nbjaimepas'] ?? 0) ?></span>
                </div>
            </div>
        </article>
    <?php endfor; ?>

</div>


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
            <article onclick="window.location.href='./?page=<?= $quizNextPart[$i]['genre'] ?>&id=<?= $quizNextPart[$i]['id'] ?> <?= $quizNextPart[$i]['genre'] == 'flashcard' ? '&action=start' : '' ?>'" class="quiz">
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
                    <p class="quiz-auteur">Par : <?= htmlspecialchars($quizNextPart[$i]['user_name'] ?? '') ?></p>
                    <p class="quiz-date">Publié le : <?= htmlspecialchars($quizNextPart[$i]['date'] ?? '') ?></p>
                    <div class="quiz-reactions">
                        <span class="reaction like">👍 <?= htmlspecialchars($quizNextPart[$i]['nbjaime'] ?? 0) ?></span>
                        <span class="reaction dislike">👎 <?= htmlspecialchars($quizNextPart[$i]['nbjaimepas'] ?? 0) ?></span>
                    </div>
                </div>
            </article>
        <?php endfor; ?>

    <?php endif; ?>
    </div>

    </body>

    </html>