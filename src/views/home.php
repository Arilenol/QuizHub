<?php
$title = "Accueil";
$style = './assets/style/home.css';
include 'partials/header.php';
?>

<?php 
// Vérifier si l'utilisateur est admin
$isAdmin = false;
if (isset($_SESSION['id'])) {
    require_once ROOT . '/config/config.php';
    $db = getDbConnection();
    $stmt = $db->prepare("SELECT admin FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $isAdmin = $user && (int)$user['admin'] === 1;
}
?>

<?php if ($isAdmin): ?>
    <div style="margin-bottom: 30px;">
        <a href="./?page=CRUD" class="admin-button" style="display: inline-block; padding: 12px 24px; background-color: #ff6b6b; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px; border: none; cursor: pointer; transition: background-color 0.3s;">
            Accès au CRUD
        </a>
    </div>
<?php endif; ?>

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
            <article onclick="window.location.href='./?page=<?= $quizNextPart[$i]['genre'] ?>&id=<?= $quizNextPart[$i]['id'] ?> <?= $quizNextPart[$i]['genre'] == 'lesson' ? '&categorie=view' : '' ?> <?= $quizNextPart[$i]['genre'] == 'flashcard' ? '&action=start' : '' ?>'" class="quiz">
                <div style="display: flex; flex-direction: row; justify-content:space-between">
                    <div class="quiz-cat">
                        <?php if (!empty($quizNextPart[$i]['categories'])): ?>
                            <?php foreach ($quizNextPart[$i]['categories'] as $cat): ?>
                                <span class="category"><?= htmlspecialchars($cat) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php
                        if($quizNextPart[$i]['genre'] == "flashcard"):
                    ?>
                        <button type="button" class="button download" style="padding: 10px" value="<?= $quiz[$i]['id'] ?>">
                            <span></span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                <path fill="white" d="M13 8V2H7v6H2l8 8l8-8h-5zM0 18h20v2H0v-2z"/>
                            </svg>
                        </button>
                    <?php
                        endif;
                    ?>
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
                <article onclick="window.location.href='./?page=<?= $friendQuiz[$i]['genre'] ?>&id=<?= $friendQuiz[$i]['id'] ?> <?= $friendQuiz[$i]['genre'] == 'lesson' ? '&categorie=view' : '' ?>'" <?= $friendQuiz[$i]['genre'] == 'flashcard' ? '&action=start' : '' ?>'" class="quiz">
                    <div style="display: flex; flex-direction: row; justify-content:space-between">
                        <div class="quiz-cat">
                            <?php if (!empty($friendQuiz[$i]['categories'])): ?>
                                <?php foreach ($friendQuiz[$i]['categories'] as $cat): ?>
                                    <span class="category"><?= htmlspecialchars($cat) ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php
                            if($friendQuiz[$i]['genre'] == "flashcard"):
                        ?>
                            <button type="button" class="button download" style="padding: 10px" value="<?= $quiz[$i]['id'] ?>">
                                <span></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                                    <path fill="white" d="M13 8V2H7v6H2l8 8l8-8h-5zM0 18h20v2H0v-2z"/>
                                </svg>
                            </button>
                        <?php
                            endif;
                        ?>
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
        <article onclick="window.location.href='./?page=<?= $quiz[$i]['genre'] == 'test' ? 'pageInterQuiz' : $quiz[$i]['genre'] ?>&id=<?= $quiz[$i]['id'] ?> <?= $quiz[$i]['genre'] == 'flashcard' ? '&action=start' : '' ?> <?= $quiz[$i]['genre'] == 'standard' ? '&type=standard' : '' ?> <?= $quiz[$i]['genre'] == 'test' ? '&type=test' : '' ?>'" class="quiz">
            <div style="display: flex; flex-direction: row; justify-content:space-between">
                <div class="quiz-cat">
                    <?php if (!empty($quiz[$i]['categories'])): ?>
                        <?php foreach ($quiz[$i]['categories'] as $cat): ?>
                            <span class="category"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php
                    if($quiz[$i]['genre'] == "flashcard"):
                ?>
                <button type="button" class="button download" style="padding: 10px" value="<?= $quiz[$i]['id'] ?>">
                    <span></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20">
                        <path fill="white" d="M13 8V2H7v6H2l8 8l8-8h-5zM0 18h20v2H0v-2z"/>
                    </svg>
                </button>
                <?php
                    endif;
                ?>
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