<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    echo "<link rel='stylesheet' href='./assets/style/CRUDrecherche.css'>";

    echo "<link rel='stylesheet' href='./assets/style/global.css'>";

    ?>
    <title>CRUD recherche</title>
</head>

<body>
    <div class="catalogue">
        <button onclick="window.location.href='?page=home'" class="retour">&lt; Retour</button>


        <form method="GET" action=index.php>
            <input type="hidden" name="page" value="CRUD">

            <div class="search-author">
                <?php
                echo '<input type="text" name="search" placeholder="écrire votre recherche" value="' . (isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '') . '">';
                ?>
                <button type="submit">Confirmer Recherche</button>
            </div>

            <div class="selects">
                <?php
                if (isset($_GET['contenu']) && $_GET['contenu'] !== '') {
                    echo '<input type="hidden" name="contenu" value="' . htmlspecialchars($_GET['contenu']) . '">';
                }

                $triSelected = isset($_GET['tri']) ? $_GET['tri'] : '';
                $filtreSelected = isset($_GET['filtre']) ? $_GET['filtre'] : '';
                $genreSelected = isset($_GET['genre']) ? $_GET['genre'] : '';
                ?>

                <select name="filtre">
                    <option value="quiz" <?= $filtreSelected === 'quiz' ? 'selected' : '' ?>>recherche quiz</option>
                    <option value="auteur" <?= $filtreSelected === 'auteur' ? 'selected' : '' ?>>recherche auteur</option>
                </select>

                <select name="genre">
                    <option value="" <?= $genreSelected === '' ? 'selected' : '' ?>>tout genre</option>
                    <option value="standard" <?= $genreSelected === 'quiz' ? 'selected' : '' ?>>standard</option>
                    <option value="test" <?= $genreSelected === 'test' ? 'selected' : '' ?>>test</option>
                    <option value="lecon" <?= $genreSelected === 'lecon' ? 'selected' : '' ?>>lecon</option>
                    <option value="flashcard" <?= $genreSelected === 'flashcard' ? 'selected' : '' ?>>flashcard</option>
                </select>

                <select name="categorie">
                    <option value="">Toutes les catégories</option>
                    <?php
                    foreach ($cats as $cat) {
                        $selected = (isset($_GET['categorie']) && $_GET['categorie'] == $cat['id']) ? 'selected' : '';
                        echo '<option value="' . $cat['id'] . '" ' . $selected . '>' . htmlspecialchars($cat['categorieName']) . '</option>';
                    }
                    ?>
                </select>

                <select name="tri">
                    <option value="">Trier par</option>
                    <?php
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


        <!-- Résultats -->
        <div class="quiz-affichage">

            <!-- Si on a des auteurs (recherche par auteur) -->
            <?php if (!empty($authors)): ?>
                <h2>Auteurs trouvés (<?= count($authors) ?>)</h2>
                <div class="quiz-affichage" id="authors-affichage">
                    <?php foreach ($authors as $author): ?>
                        <div class="quiz author-card" onclick="window.location.href='index.php?page=CRUDauteur&id=<?= $author['id'] ?>'">
                            <article>
                                <div class="quiz-cat">
                                    <span class="category">Auteur</span>
                                </div>
                                <p class="quiz-title"><?= htmlspecialchars($author['username']) ?></p>
                                <br>
                                <p class="quiz-description">Voir tous les quiz de cet auteur</p>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Résultats: Quizzes -->
            <div class="quiz-affichage" id="quiz-affichage">
                <?php
                foreach ($quizzes as $quiz) {
                    echo '<div class="quiz" onclick="window.location.href=\'index.php?page=CRUDquiz&id=' . $quiz['id'] . '\'">
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
                    <br><p class="quiz-auteur">Par : ' . htmlspecialchars($quiz['nom_auteur'] ?? '') . '</p>
                    
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

</body>

</html>