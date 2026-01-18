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
                    echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . htmlspecialchars($cat['categorieName']) . '</option>';
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
        <?php
        foreach ($quizzes as $quiz) {
            if ($quiz['genre'] === 'flashcard') {
                $genre = 'flashcard';
                $suite = '&action=start';
            } elseif ($quiz['genre'] === 'standard') {
                $genre = 'standard';
                $suite = '';
            } elseif ($quiz['genre'] === 'test') {
                $genre = 'test';
                $suite = '';
            }elseif ($quiz['genre'] === 'leçon'){
                $genre = 'lesson';
                $suite = '&categorie=view';
            }
            echo '<div class="quiz" onclick="window.location.href=\'index.php?page=' . $genre . '' . $suite . '&id=' . $quiz['id'] . '\'">
        <article >
            <div class="quiz-cat">';
            if (!empty($quiz['categories']) && is_array($quiz['categories'])) {
                foreach ($quiz['categories'] as $cat) {

                    $catName = $cat['categorieName'] ?? $cat['CategorieName'] ?? $cat['name'] ?? '';
                    echo '<span class="category">' . htmlspecialchars($catName) . '</span>';
                }
            }
            echo '</div>
        <p class="quiz-genre">' . htmlspecialchars($quiz['genre'] ?? '') . '</p>
        <br><p class="quiz-title">' . htmlspecialchars($quiz['title'] ?? '') . '</p>
        <br><p class="quiz-description">' . htmlspecialchars($quiz['description'] ?? '') . '</p>
        <br><p class="quiz-auteur">Par : ' . htmlspecialchars($quiz['username'] ?? '') . '</p>
        
        <div class="quiz-footer">
            <p class="quiz-date">publié le : ' . htmlspecialchars($quiz['date'] ?? '') . '</p>
                <div class="quiz-reactions">';
                if ($quiz['genre'] != 'leçon'){
                    echo '
                    <span class="reaction like">👍 ' . htmlspecialchars($quiz['likes'] ?? 0) . '</span>
                    <span class="reaction dislike">👎 ' . htmlspecialchars($quiz['dislikes'] ?? 0) . '</span>';
                }
                echo '</div>
            </div>
        </article>
    </div>';
        }
        if (count($quizzes) == 0){
            echo '<p class="aucunResult">Aucun résultat ne correspond à votre recherche</p>';
        }
        ?>
    </div>
</div>