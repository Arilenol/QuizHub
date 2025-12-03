<?php
$title = "Accueil";
$style = './assets/style/home.css';
include 'partials/header.php';
?>

<h1>Créations populaires</h1>

<div class="newCreations">
    <?php for ($i = 0; $i < 7; $i++): ?>
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

<h1>Vos créations</h1>

<div class="popCreations">
    <?php if (empty($userCreations)): ?>
        <p class="no-content">Vous n'avez encore créé aucune ressource.</p>
    <?php else: ?>
        <!-- Affichage des créations de l’utilisateur -->
        <?php foreach ($userCreations as $creation): ?>
            <article class="quiz">
                <p class="quiz-title"><?= htmlspecialchars($creation['title']) ?></p>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>

</html>