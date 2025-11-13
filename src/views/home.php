
<?php 
$title = "Accueil";
$style = './assets/style/home.css';
include 'partials/header.php'; ?>
    <h1>Créations populaires</h1>
    <div class=popCreations>
            <?php
                //en attente des nbJaimes
            ?>
    </div>
    <h1>Vos créations</h1>
    <div class=newCreations>
            <?php
                // attente du js pour faire avec un foreach
                // foreach ($quiz as $q) {
                //     echo '<article></article>';
                // }
                for ($i=0; $i < 7; $i++) { 
                    echo '<div class="quiz">';
        
                    echo '<article onclick="window.location.href=\'./QuizPage.php?quiz_id=' . $quiz[$i]['id'] . '\'">';
                    echo '<div class="quiz-cat">';
                    
                        if (!empty($quiz[$i]['categories']) && is_array($quiz[$i]['categories'])) {
                            foreach ($quiz[$i]['categories'] as $cat) {
                                
                                $catName = $cat['categorieName'] ?? $cat['CategorieName'] ?? $cat['name'] ?? '';
                                echo '<span class="category">' . htmlspecialchars($catName) . '</span>';
                            }
                        }
                    echo '</div>';
                    echo '<p class="quiz-genre">' . htmlspecialchars($quiz[$i]['genre'] ?? '') . '</p>';
                    echo '<br><p class="quiz-title">' . htmlspecialchars($quiz[$i]['title'] ?? '') . '</p>';
                    echo '<br><p class="quiz-description">' . htmlspecialchars($quiz[$i]['description'] ?? '') . '</p>';
                    echo '<br><p class="quiz-auteur">Par : '.htmlspecialchars($quiz[$i]['user_name'] ?? '') . '</p>';
                    // footer row with date on left and reactions on right
                    echo '<div class="quiz-footer">';
                    echo '<p class="quiz-date">publié le : ' . htmlspecialchars($quiz[$i]['date'] ?? '') . '</p>';
                    echo '<div class="quiz-reactions">';
                    echo '<span class="reaction like">♥ ' . htmlspecialchars($quiz[$i]['likes'] ?? 0) . '</span>';
                    echo '<span class="reaction dislike">♡ ' . htmlspecialchars($quiz[$i]['dislikes'] ?? 0) . '</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '</article>';
                    echo '</div>';
                }
            ?>
    </div>
</body>
</html>