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
            <h1 style="color: #0AB1BD; margin: 0;">Informations de l'auteur</h1>
            <form method="POST" action="">
                <input type="hidden" name="action" value="update">
                <div style="margin-top: 10px;">
                    <label for="username">Nom d'utilisateur :</label><br>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($author_info['username']) ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                </div>
                <div>
                    <label for="email">Email :</label><br>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($author_info['email']) ?>" required style="width: 100%; padding: 8px; margin-bottom: 10px;">
                </div>
                <div>
                    <label for="description">Description :</label><br>
                    <textarea id="description" name="description" style="width: 100%; padding: 8px; margin-bottom: 10px; height: 100px;"><?= htmlspecialchars($author_info['description']) ?></textarea>
                </div>
                <div>
                    <label>Admin :</label><br>
                    <input type="checkbox" id="admin" name="admin" <?= $author_info['admin'] ? 'checked' : '' ?> disabled> (Non modifiable ici)
                </div>
                <button type="submit" style="background-color: #0AB1BD; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-top: 10px;">Modifier</button>
            </form>
            <form method="POST" action="" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet auteur ?');" style="margin-top: 20px;">
                <input type="hidden" name="action" value="delete">
                <button type="submit" style="background-color: #ff3b3b; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Supprimer</button>
            </form>
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
                                    <span class="reaction like">👍 ' . htmlspecialchars($quiz['likes'] ?? 0) . '</span>
                                    <span class="reaction dislike">👎 ' . htmlspecialchars($quiz['dislikes'] ?? 0) . '</span>
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