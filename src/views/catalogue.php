<?php
    $title = "Catalogue";
    $style = './assets/style/catalogue.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require 'partials/header.php'; 
?>
<div class="catalogue">

    <form method="GET" action = index.php>
        <input type="hidden" name = "page" value = "catalogue">
        <div class="search-author">
            <?php
                echo '<input type="text" name="searchAuthor" placeholder="Rechercher un auteur..." value="'.(isset($_GET['searchAuthor']) ? htmlspecialchars($_GET['searchAuthor']) : '').'">';
            ?>
        </div>
        <div class = "selects">
        <?php 
            if (isset($_GET['contenu']) && $_GET['contenu'] !== '') {
                echo '<input type="hidden" name="contenu" value="' . htmlspecialchars($_GET['contenu']) . '">';
            }
            ?>

            <select name = "categorie" onchange="this.form.submit()">
            <option value="">Toutes les catégories</option>
                <?php
                foreach ($cats as $cat) {
                    $selected = (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id']) ? 'selected' : '';
                    echo '<option value="' . $cat['id'] . '" '.$selected.'>' . htmlspecialchars($cat['categorieName']) . '</option>';
                }
                ?>
            </select>
            <select name="tri" onchange="this.form.submit()">
            <option value="">Trier par</option>
                <?php
                $triSelected = isset($_GET['tri']) ? $_GET['tri'] : '';
                
                $options = [
                    'date_desc' => 'Date (nouveau → ancien)',
                    'date_asc' => 'Date (ancien → nouveau)',
                    'title_asc' => 'Titre (A → Z)',
                    'title_desc' => 'Titre (Z → A)',
                    'difficulty_asc' => 'Difficulté (faible → élevé)',
                    'difficulty_desc' => 'Difficulté (élevé → faible)',
                    'author_asc' => "Auteur (A → Z)",
                    'author_desc' => "Auteur (Z → A)",
                    'genre_asc' => 'Genre (A → Z)',
                    'genre_desc' => 'Genre (Z → A)'
                ];
                foreach ($options as $val => $label) {
                    $sel = ($triSelected === $val) ? 'selected' : '';
                    echo '<option value="' . $val . '" ' . $sel . '>' . htmlspecialchars($label) . '</option>';
                }
                ?>
            </select>
        </div>
    </form>

    <div class="quiz-affichage">
        <?php 
        foreach ($quizzes as $quiz) {
            echo '<div class="quiz">
                <article onclick="window.location.href=\'./QuizPage.php?quiz_id=' . $quiz['id'] . '\'">
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
                <br><p class="quiz-auteur">Par : '.htmlspecialchars($quiz['nom_auteur'] ?? '') . '</p>
                
                <div class="quiz-footer">
                    <p class="quiz-date">publié le : ' . htmlspecialchars($quiz['date'] ?? '') . '</p>
                        <div class="quiz-reactions">
                            <span class="reaction like">♥ ' . htmlspecialchars($quiz['nbjaime'] ?? 0) . '</span>
                            <span class="reaction dislike">♡ ' . htmlspecialchars($quiz['nbjaimepas'] ?? 0) . '</span>
                        </div>
                    </div>
                </article>
            </div>';
        }
        ?>
    </div>
</div>

   