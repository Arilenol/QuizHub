
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    echo "<link rel='stylesheet' href='./assets/style/CRUDrecherche.css'>";
    echo "<link rel='stylesheet' href='./assets/style/CRUDquiz.css'>";
    echo "<link rel='stylesheet' href='./assets/style/global.css'>";
    ?>
    <title>CRUD quiz - Détails</title>
</head>

<body>
    <div id="catalogue" class="catalogue">
        <button onclick="window.location.href='?page=CRUD'" class="retour" id="retour">&lt; Retour</button>

        <div id="quiz-affichage" class="quiz-affichage">
            <!-- Fiche quiz (utilise la même structure qu'une card de la recherche) -->
            <div id="quiz-<?= htmlspecialchars($quiz['id']) ?>" class="quiz">
                <article>
                    <div class="quiz-cat">
                        <?php if (!empty($quiz['categories']) && is_array($quiz['categories'])): ?>
                            <?php foreach ($quiz['categories'] as $cat): ?>
                                <span class="category"><?= htmlspecialchars($cat['categorieName']) ?></span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <p class="quiz-genre"><?= htmlspecialchars($quiz['genre'] ?? '') ?></p>
                    <p class="quiz-title"><?= htmlspecialchars($quiz['title'] ?? '') ?></p>
                    <p class="quiz-description"><?= htmlspecialchars($quiz['description'] ?? '') ?></p>
                    <p class="quiz-auteur">Par : <?= htmlspecialchars($quiz['nom_auteur'] ?? '') ?></p>

                    <div class="quiz-footer">
                        <p class="quiz-date">publié le : <?= htmlspecialchars($quiz['date'] ?? '') ?></p>
                        <div class="quiz-reactions">
                            <span class="reaction like">👍 <?= htmlspecialchars($quiz['nbjaime'] ?? 0) ?></span>
                            <span class="reaction dislike">👎 <?= htmlspecialchars($quiz['nbjaimepas'] ?? 0) ?></span>
                        </div>
                    </div>

                    <div class="quiz-actions">
                        <button onclick="alert('Modifier en développement')">Modifier</button>
                        <button onclick="alert('Supprimer en développement')">Supprimer</button>
                    </div>
                </article>
            </div>

            <!-- Liste des questions (chaque question a un id et une class) -->
            <h2>Questions du quiz (<?= count($questionsWithAnswers) ?>)</h2>

            <?php if (!empty($questionsWithAnswers)): ?>
                <?php foreach ($questionsWithAnswers as $question): ?>
                    <div id="question-<?= htmlspecialchars($question['id']) ?>" class="question-card">
                        <h3>Question <?= htmlspecialchars($question['numeroQuiz']) ?></h3>
                        <p class="question-text"><?= htmlspecialchars($question['enonce'] ?? '') ?></p>

                        <?php if (!empty($question['answers']) && is_array($question['answers'])): ?>
                            <div class="answers">
                                <h4>Réponses :</h4>
                                <ul>
                                    <?php foreach ($question['answers'] as $answer): ?>
                                        <li class="answer <?= $answer['est_correct'] ? 'correct' : '' ?>">
                                            <?= htmlspecialchars($answer['texte']) ?>
                                            <?php if ($answer['est_correct']): ?>
                                                <span class="correct-badge">✓ Correct</span>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php else: ?>
                            <p class="no-answers">Aucune réponse pour cette question</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="no-questions">Aucune question trouvée pour ce quiz</p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>