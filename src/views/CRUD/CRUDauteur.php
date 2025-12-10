<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    echo "<link rel='stylesheet' href='./assets/style/CRUDrecherche.css'>";
    echo "<link rel='stylesheet' href='./assets/style/global.css'>";
    ?>
    <title>Auteur - <?= htmlspecialchars($author_name) ?></title>
</head>

<body>
    <div class="catalogue">
        <button onclick="window.location.href='?page=CRUD'" class="retour">&lt; Retour à la recherche</button>

        <!-- Fiche auteur -->
        <div style="margin: 30px 0; padding: 20px; background-color: #f0f8f9; border-radius: 10px; border-left: 4px solid #0AB1BD;">
            <h1 style="color: #0AB1BD; margin: 0;"><?= htmlspecialchars($author_name) ?></h1>
            <p style="color: #666; margin-top: 10px;">Quiz créés : <strong><?= count($quizzes) ?></strong></p>
        </div>

        <!-- Titre de la section -->
        <h2 style="margin-top: 30px; margin-bottom: 20px; border-bottom: 3px solid #0AB1BD; padding-bottom: 10px;">Tous les quiz de <?= htmlspecialchars($author_name) ?></h2>

        <!-- Affichage des quizzes de l'auteur -->
        <div class="quiz-affichage">
            <?php 
            if (!empty($quizzes)): 
                foreach ($quizzes as $quiz) {
                    echo '<div class="quiz" onclick="window.location.href=\'index.php?page=CRUDquiz&id=' . $quiz['id'] . '\'">
                        <article>
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
                            
                            <div class="quiz-footer">
                                <p class="quiz-date">publié le : ' . htmlspecialchars($quiz['date'] ?? '') . '</p>
                                <div class="quiz-reactions">
                                    <span class="reaction like">♥ ' . htmlspecialchars($quiz['likes'] ?? 0) . '</span>
                                    <span class="reaction dislike">♡ ' . htmlspecialchars($quiz['dislikes'] ?? 0) . '</span>
                                </div>
                            </div>
                        </article>
                    </div>';
                }
            else:
                echo '<p style="text-align: center; color: #999; padding: 40px;">Cet auteur n\'a pas encore créé de quiz.</p>';
            endif;
            ?>
        </div>
    </div>

</body>

</html>