<?php
    //5 h 00 de travail pour l'instant
    //require_once 'header.php';

    require_once ROOT . '/config/construction.php';


    // Use BASE_URL (computed in public/index.php) so URLs work regardless of server path
    echo '<link rel="stylesheet" href="' . BASE_URL . '/assets/style/global.css">';
    echo '<link rel="stylesheet" href="' . BASE_URL . '/assets/style/catalogue.css">';
    // corrected opening div (previously malformed which broke subsequent HTML parsing)
    echo '<div class="catalogue">';

    
    //constructionBD($conn);


    echo '<div class="search-author">';
    echo '<form method="GET">';
    echo '<input type="text" name="searchAuthor" placeholder="Rechercher un auteur..." value="'.(isset($_GET['searchAuthor']) ? htmlspecialchars($_GET['searchAuthor']) : '').'">';
    echo '</form>';
    echo '</div>';

    echo '<div class = "selects">';
    echo '<form method="GET" >';
    
    if (isset($_GET['searchAuthor']) && $_GET['searchAuthor'] !== '') {
        echo '<input type="hidden" name="searchAuthor" value="' . htmlspecialchars($_GET['searchAuthor']) . '">';
    }
    if (isset($_GET['contenu']) && $_GET['contenu'] !== '') {
        echo '<input type="hidden" name="contenu" value="' . htmlspecialchars($_GET['contenu']) . '">';
    }
    echo '<select name = "categorie" onchange="this.form.submit()">';
    echo '<option value="">Toutes les catégories</option>';
    foreach ($cats as $cat) {
        $selected = (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id']) ? 'selected' : '';
        echo '<option value="' . $cat['id'] . '" '.$selected.'>' . htmlspecialchars($cat['categorieName']) . '</option>';
    }
    echo '</select>';

    
    $triSelected = isset($_GET['tri']) ? $_GET['tri'] : '';
    echo '<select name="tri" onchange="this.form.submit()">';
    echo '<option value="">Trier par</option>';
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
    echo '</select>';

    echo '</form>';
    echo '</div>';
    //l'option "Toutes les catégories" n'a pas de value, donc la sienne par défaut est le contenu du texte
    /*$recherche_cat = isset($_GET['categorie'])&& !empty($_GET['categorie'])&& htmlspecialchars($_GET['categorie'])!='Toutes les catégories' ? $_GET['categorie'] : '';
    $recherche_contenu = isset($_GET['contenu'])&& !empty($_GET['contenu']) ? htmlspecialchars($_GET['contenu']) : '';
    $recherche_auteur = isset($_GET['searchAuthor']) && !empty($_GET['searchAuthor']) ? htmlspecialchars($_GET['searchAuthor']) : '';
    if($recherche_cat != ''){
        $categories_correspondantes = searchQuizByAll($recherche_cat,$recherche_contenu,$recherche_auteur);
    }else{
        $categories_correspondantes = searchQuizByContentAndAuthor($recherche_contenu,$recherche_auteur);
    }*/
    echo '<div class="quiz-affichage">';
    
    foreach ($quizzes as $quiz) {
        echo '<div class="quiz">';
        
        echo '<article onclick="window.location.href=\'' . BASE_URL . '/QuizPage.php?quiz_id=' . $quiz['id'] . '\'">';
        echo '<div class="quiz-cat">';
           
            if (!empty($quiz['categories']) && is_array($quiz['categories'])) {
                foreach ($quiz['categories'] as $cat) {
                    
                    $catName = $cat['categorieName'] ?? $cat['CategorieName'] ?? $cat['name'] ?? '';
                    echo '<span class="category">' . htmlspecialchars($catName) . '</span>';
                }
            }
        echo '</div>';
        echo '<p class="quiz-genre">' . htmlspecialchars($quiz['genre'] ?? '') . '</p>';
        echo '<br><p class="quiz-title">' . htmlspecialchars($quiz['title'] ?? '') . '</p>';
        echo '<br><p class="quiz-description">' . htmlspecialchars($quiz['description'] ?? '') . '</p>';
        echo '<br><p class="quiz-auteur">Par : '.htmlspecialchars($quiz['nom_auteur'] ?? '') . '</p>';
        echo '<br><p class="quiz-date">publié le : ' . htmlspecialchars($quiz['date'] ?? '') . '</p>';
        echo '</article>';
        echo '</div>';
    }
    echo '</div>';

    echo '</div>';

?>