<?php
$title = "Catalogue";
$style = './assets/style/catalogue.css';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'partials/header.php';
?>

<div class="catalogue">
    <button onclick="window.location.href='index.php?page=home'" class="button" type="submit">
        <span></span>
        <p>< Retour</p>
    </button>
    <form method="GET" action="index.php">
        <input type="hidden" name="page" value="catalogue">
        <input type="hidden" name="numPage" value="<?php isset($page) ? $page : 1 ?>">
        <div class="search-author">
            <?php
            echo '<input type="text" name="searchAuthor" placeholder="Rechercher un auteur par mot-clé" value="' . (isset($_GET['searchAuthor']) ? htmlspecialchars($_GET['searchAuthor']) : '') . '">';
            ?>
        </div>
        <div class="selects">
            <?php
            if (isset($_GET['contenu']) && $_GET['contenu'] !== '') {
                echo '<input type="hidden" name="contenu" value="' . htmlspecialchars($_GET['contenu']) . '">';
            }
            ?>

            <p>Catégorie :</p>
            <select name="categorie" onchange="this.form.submit()">
                <option value="">Toutes les catégories</option>
                <?php
                foreach ($cats as $cat) {
                    $selected = (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id']) ? 'selected' : '';
                    echo '<option value="' . htmlspecialchars($cat['id']) . '" ' . $selected . '>' . htmlspecialchars($cat['categorieName']) . '</option>';
                }
                ?>
            </select>
            <p>Tri :</p>
            <select name="tri" onchange="this.form.submit()">
                <option value="">Aucun tri</option>
                <?php


                foreach ($options as $val => $label) {
                    $sel = ($tri === $val) ? 'selected' : '';
                    echo '<option value="' . $val . '" ' . $sel . '>' . htmlspecialchars($label) . '</option>';
                }
                ?>
            </select>
            <p>Genre :</p>
            <select name="genre" onchange="this.form.submit()">
                <option value="">Tous les genres</option>
                <?php
                foreach ($genres as $key => $g) {
                    $sel = ($genre === $key) ? 'selected' : '';
                    echo '<option value="' . $key . '" ' . $sel . '>' . htmlspecialchars($g) . '</option>';
                }
                ?>
            </select>
        </div>
    </form>

    <div class="quiz-affichage">
        <?php if (count($quizzes) == 0): ?>
            <p class="aucunResult">Aucun résultat ne correspond à votre recherche</p>
        <?php else: ?>
            <?php foreach ($quizzes as $quiz): ?>
                <?php
                    $url = '';
                    if ($quiz['genre'] === 'flashcard') {
                        $url = "index.php?page=flashcard&action=start&id=" . $quiz['id'];
                    } elseif ($quiz['genre'] === 'standard') {
                        $url = "index.php?page=standard&id=" . $quiz['id'];
                    } elseif ($quiz['genre'] === 'test') {
                        $url = "index.php?page=pageInterQuiz&type=test&id=" . $quiz['id'];
                    } elseif ($quiz['genre'] === 'leçon') {
                        $url = "index.php?page=lesson&categorie=view&id=" . $quiz['id'];
                    }
                ?>
                <div class="quiz" onclick="window.location.href='<?= $url ?>'">
                    <article>
                        <div class="quiz-header">
                            <div class="quiz-cat">
                                <?php if (!empty($quiz['categories']) && is_array($quiz['categories'])): ?>
                                    <?php foreach ($quiz['categories'] as $cat): ?>
                                        <?php $catName = $cat['categorieName'] ?? $cat['CategorieName'] ?? $cat['name'] ?? ''; ?>
                                        <span class="category"><?= htmlspecialchars($catName) ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                            <?php if($quiz['genre'] == "flashcard"): ?>
                                <button type="button" class="button download-button" value="<?= $quiz['id'] ?>">
                                    <span></span>
                                    <svg class="download-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill="white" d="M13 8V2H7v6H2l8 8l8-8h-5zM0 18h20v2H0v-2z"/>
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="quiz-content">
                            <p class="quiz-genre"><?= htmlspecialchars($quiz['genre'] ?? '') ?></p>
                            <p class="quiz-title"><?= htmlspecialchars($quiz['title'] ?? '') ?></p>
                            <p class="quiz-description"><?= htmlspecialchars($quiz['description'] ?? '') ?></p>
                            <p class="quiz-auteur">Par : <?= htmlspecialchars($quiz['username'] ?? '') ?></p>
                        </div>
                        <div class="quiz-footer">
                            <p class="quiz-date">publié le : <?= htmlspecialchars($quiz['date'] ?? '') ?></p>
                            <div class="quiz-reactions">
                                <span class="reaction like">👍 <?= htmlspecialchars($quiz['likes'] ?? 0) ?></span>
                                <span class="reaction dislike">👎 <?= htmlspecialchars($quiz['dislikes'] ?? 0) ?></span>
                            </div>
                        </div>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>