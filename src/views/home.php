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
    <div class="admin-button-container">
        <a href="./?page=CRUD" class="admin-button">
            Accès au CRUD
        </a>
    </div>
<?php endif; ?>
<div class="nav">
    <button class="button" onclick="document.getElementById('2').scrollIntoView({ behavior: 'smooth' })">
        <span></span>
        <p>Entraînements Populaires</p>
    </button>
    <?php if (isset($_SESSION['id'])): ?>
        <button class="button" onclick="document.getElementById('1').scrollIntoView({ behavior: 'smooth' })">
            <span></span>
            <p>Les créations de mes amis</p>
        </button>
    <?php endif; ?>
    <button class="button" onclick="document.getElementById('3').scrollIntoView({ behavior: 'smooth' })">
        <span></span>
        <p>Leçons populaires</p>
    </button>
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
        <?php
        $limit = isset($_SESSION['id'])
            ? count($quizNextPart)
            : min(5, count($quizNextPart));
        ?>

        <?php for ($i = 0; $i < $limit; $i++): ?>
            <?php
            $genre = $quizNextPart[$i]['genre'];
            $id = $quizNextPart[$i]['id'];

            /* Détermination de la page */
            if ($genre === 'test') {
                $page = 'pageInterQuiz';
            } elseif ($genre === 'leçon') {
                $page = 'lesson';
            } else {
                $page = $genre;
            }

            /* Construction des paramètres */
            $params = "id=" . $id;

            if ($genre === 'leçon') {
                $params .= "&categorie=view";
            }

            if ($genre === 'flashcard') {
                $params .= "&action=start";
            }

            if ($genre === 'test') {
                $params .= "&type=test";
            }

            /* URL finale */
            $url = "./?page={$page}&{$params}";
            ?>
            <article onclick="window.location.href='<?= $url ?>'" class="quiz">
                <div class="quiz-header">
                    <div class="quiz-cat">
                        <?php if (!empty($quizNextPart[$i]['categories'])): ?>
                            <?php foreach ($quizNextPart[$i]['categories'] as $cat): ?>
                                <span class="category"><?= htmlspecialchars($cat) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php
                    if ($quizNextPart[$i]['genre'] == "flashcard"):
                    ?>
                        <button type="button" class="button download-button" value="<?= $quizNextPart[$i]['id'] ?>">
                            <span></span>
                            <svg class="download-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill="white" d="M13 8V2H7v6H2l8 8l8-8h-5zM0 18h20v2H0v-2z" />
                            </svg>
                        </button>
                    <?php
                    endif;
                    ?>
                </div>
                <div class="quiz-content">
                    <p class="quiz-genre"><?= htmlspecialchars($quizNextPart[$i]['genre'] ?? '') ?></p>
                    <p class="quiz-title"><?= htmlspecialchars($quizNextPart[$i]['title'] ?? '') ?></p>
                    <p class="quiz-description"><?= htmlspecialchars($quizNextPart[$i]['description'] ?? '') ?></p>
                </div>
                <div class="quiz-footer">
                    <form action="?page=profil&action=creatorProfil" method="POST">
                        <input type="hidden" name="creatorId" value="<?= $quizNextPart[$i]['creatorId'] ?>">
                        <p class="quiz-auteur">Par : <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;font-size:large;"> <?= htmlspecialchars($quizNextPart[$i]['user_name'] ?? '') ?> </button></p>
                    </form>
                    <p class="quiz-date">Publié le : <?= htmlspecialchars($quizNextPart[$i]['date'] ?? '') ?></p>
                    <div class="quiz-reactions">
                        <span class="reaction like">👍 <?= htmlspecialchars($quizNextPart[$i]['nbjaime'] ?? 0) ?></span>
                        <span class="reaction dislike">👎 <?= htmlspecialchars($quizNextPart[$i]['nbjaimepas'] ?? 0) ?></span>
                    </div>
                </div>
            </article>

        <?php endfor; ?>
        <?php if (isset($_SESSION['id'])): ?>
            <article onclick="window.location.href='?page=catalogue&numPage=&searchAuthor=<?= urlencode($quizNextPart[0]['user_name']) ?>&categorie=&tri=date_desc&genre='"
                class="quiz seeMore">
                Voir plus
            </article>
        <?php else: ?>
            <?php if (count($quizNextPart) >= 5): ?>
                <article onclick="window.location.href='?page=catalogue&numPage=&searchAuthor=&categorie=&tri=date_desc&genre='" class="quiz seeMore">
                    Voir plus
                </article>

            <?php endif; ?>
        <?php endif; ?>

    </div>
<?php endif; ?>

<?php if (isset($_SESSION['id']) && !empty($_SESSION['id'])): ?>
    <h1 id="1">Les créations de mes amis</h1>
    <?php if (isset($friendQuiz) && !empty($friendQuiz)): ?>
        <div class="newCreations">
            <?php for ($i = 0; $i < count($friendQuiz); $i++): ?>
                <?php
                $genre = $friendQuiz[$i]['genre'];
                $id = $friendQuiz[$i]['id'];

                /* Détermination de la page */
                if ($genre === 'test') {
                    $page = 'pageInterQuiz';
                } elseif ($genre === 'leçon') {
                    $page = 'lesson';
                } else {
                    $page = $genre;
                }

                /* Construction des paramètres */
                $params = "id=" . $id;

                if ($genre === 'leçon') {
                    $params .= "&categorie=view";
                }

                if ($genre === 'flashcard') {
                    $params .= "&action=start";
                }

                if ($genre === 'test') {
                    $params .= "&type=test";
                }

                /* URL finale */
                $url = "./?page={$page}&{$params}";
                ?>
                <article onclick="window.location.href='<?= $url ?>'" class="quiz">
                    <div class="quiz-header">
                        <div class="quiz-cat">
                            <?php if (!empty($friendQuiz[$i]['categories'])): ?>
                                <?php foreach ($friendQuiz[$i]['categories'] as $cat): ?>
                                    <span class="category"><?= htmlspecialchars($cat) ?></span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php
                        if ($friendQuiz[$i]['genre'] == "flashcard"):
                        ?>
                            <button type="button" class="button download-button" value="<?= $friendQuiz[$i]['id'] ?>">
                                <span></span>
                                <svg class="download-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill="white" d="M13 8V2H7v6H2l8 8l8-8h-5zM0 18h20v2H0v-2z" />
                                </svg>
                            </button>
                        <?php
                        endif;
                        ?>
                    </div>
                    <div class="quiz-content">
                        <p class="quiz-genre"><?= htmlspecialchars($friendQuiz[$i]['genre'] ?? '') ?></p>
                        <p class="quiz-title"><?= htmlspecialchars($friendQuiz[$i]['title'] ?? '') ?></p>
                        <p class="quiz-description"><?= htmlspecialchars($friendQuiz[$i]['description'] ?? '') ?></p>
                    </div>
                    <div class="quiz-footer">
                        <form action="?page=profil&action=creatorProfil" method="POST">
                            <input type="hidden" name="creatorId" value="<?= $quiz[$i]['creatorId'] ?>">
                            <p class="quiz-auteur">Par : <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;font-size:large;"> <?= htmlspecialchars($quiz[$i]['user_name'] ?? '') ?> </button></p>
                        </form>
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


<h1 id="2">Entraînements populaires</h1>

<div class="newCreations">
    <?php for ($i = 0; $i < count($quiz) && $i < 5; $i++): ?>
        <article onclick="window.location.href='./?page=<?= $quiz[$i]['genre'] == 'test' ? 'pageInterQuiz' : $quiz[$i]['genre'] ?>&id=<?= $quiz[$i]['id'] ?> <?= $quiz[$i]['genre'] == 'flashcard' ? '&action=start' : '' ?> <?= $quiz[$i]['genre'] == 'standard' ? '&type=standard' : '' ?> <?= $quiz[$i]['genre'] == 'test' ? '&type=test' : '' ?>'" class="quiz">
            <div class="quiz-header">
                <div class="quiz-cat">
                    <?php if (!empty($quiz[$i]['categories'])): ?>
                        <?php foreach ($quiz[$i]['categories'] as $cat): ?>
                            <span class="category"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <?php
                if ($quiz[$i]['genre'] == "flashcard"):
                ?>
                    <button type="button" class="button download-button" value="<?= $quiz[$i]['id'] ?>">
                        <span></span>
                        <svg class="download-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill="white" d="M13 8V2H7v6H2l8 8l8-8h-5zM0 18h20v2H0v-2z" />
                        </svg>
                    </button>
                <?php
                endif;
                ?>
            </div>
            <div class="quiz-content">
                <p class="quiz-genre"><?= htmlspecialchars($quiz[$i]['genre'] ?? '') ?></p>
                <p class="quiz-title"><?= htmlspecialchars($quiz[$i]['title'] ?? '') ?></p>
                <p class="quiz-description"><?= htmlspecialchars($quiz[$i]['description'] ?? '') ?></p>
            </div>
            <div class="quiz-footer">
                <form action="?page=profil&action=creatorProfil" method="POST">
                    <input type="hidden" name="creatorId" value="<?= $quiz[$i]['creatorId'] ?>">
                    <p class="quiz-auteur">Par : <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;font-size:large;"> <?= htmlspecialchars($quiz[$i]['user_name'] ?? '') ?> </button></p>
                </form>
                <p class="quiz-date">Publié le : <?= htmlspecialchars($quiz[$i]['date'] ?? '') ?></p>
                <div class="quiz-reactions">
                    <span class="reaction like">👍 <?= htmlspecialchars($quiz[$i]['nbjaime'] ?? 0) ?></span>
                    <span class="reaction dislike">👎 <?= htmlspecialchars($quiz[$i]['nbjaimepas'] ?? 0) ?></span>
                </div>
            </div>
        </article>

    <?php endfor; ?>
    <?php if (count($quiz) >= 5): ?>
        <article onclick="window.location.href='?page=catalogue&numPage=&searchAuthor=&categorie=&tri=popup_desc&genre='" class="quiz seeMore">
            Voir plus
        </article>

    <?php endif; ?>

</div>

<h1 id="3">Leçons populaires</h1>

<div class="newCreations">
    <?php for ($i = 0; $i < count($lessons) && $i < 5; $i++): ?>
        <article onclick="window.location.href='./?page=lesson&categorie=view&id=<?= $lessons[$i]['lecon_id'] ?>'" class="quiz">
            <div class="quiz-header">
                <div class="quiz-cat">
                    <?php if (!empty($lessons[$i]['categories'])): ?>
                        <?php foreach ($lessons[$i]['categories'] as $cat): ?>
                            <span class="category"><?= htmlspecialchars($cat) ?></span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div class="quiz-content">
                <p class="quiz-genre"> leçon </p>
                <p class="quiz-title"><?= htmlspecialchars($lessons[$i]['lecon_title'] ?? '') ?></p>
                <p class="quiz-description"><?= htmlspecialchars($lessons[$i]['lecon_description'] ?? '') ?></p>
            </div>
            <div class="quiz-footer">
                <form action="?page=profil&action=creatorProfil" method="POST">
                    <input type="hidden" name="creatorId" value="<?= $lessons[$i]['creatorId'] ?>">
                    <p class="quiz-auteur">Par : <button type="submit" style="background:none;border:none;color:blue;text-decoration:underline;cursor:pointer;font-size:large;"> <?= htmlspecialchars($lessons[$i]['user_name'] ?? '') ?> </button></p>
                </form>
                <p class="quiz-date">Publié le : <?= htmlspecialchars($lessons[$i]['lecon_date'] ?? '') ?></p>
                <div class="quiz-reactions">
                    <span class="reaction like">👍 <?= htmlspecialchars($lessons[$i]['nbjaime'] ?? 0) ?></span>
                    <span class="reaction dislike">👎 <?= htmlspecialchars($lessons[$i]['nbjaimepas'] ?? 0) ?></span>
                </div>
            </div>
        </article>
    <?php endfor; ?>
    <?php if (count($lessons) >= 5): ?>
        <article onclick="window.location.href='?page=catalogue&numPage=&searchAuthor=&categorie=&tri=popup_desc&genre=leçon'" class="quiz seeMore">
            Voir plus
        </article>

    <?php endif; ?>

</div>

<div class="endDirection">
    <button onclick="window.location.href='?page=catalogue'">Voir tout</button>
</div>
</body>

</html>