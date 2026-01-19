
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

                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_quiz">
                        <input type="hidden" name="quiz_id" value="<?= htmlspecialchars($quiz['id']) ?>">

                        <p class="quiz-title" style="display: block;"><input type="text" name="title" value="<?= htmlspecialchars($quiz['title']) ?>" required style="width: 100%; border: none; background: transparent; font-size: inherit; font-weight: bold;"></p>
                        <p class="quiz-description" style="display: block;"><textarea name="description" required style="width: 100%; border: none; background: transparent; font-size: inherit; resize: vertical; min-height: 60px;"><?= htmlspecialchars($quiz['description']) ?></textarea></p>
                        <p class="quiz-genre" style="display: block; width: 60px;"><?= htmlspecialchars($quiz['genre'] ?? '') ?></p>
                        <p class="quiz-auteur">Par : <?= htmlspecialchars($quiz['nom_auteur'] ?? '') ?></p>

                        <div class="quiz-footer">
                            <p class="quiz-date">publié le : <?= htmlspecialchars($quiz['date'] ?? '') ?></p>
                            <div class="quiz-reactions">
                                <span class="reaction like">👍 <?= htmlspecialchars($quiz['nbjaime'] ?? 0) ?></span>
                                <span class="reaction dislike">👎 <?= htmlspecialchars($quiz['nbjaimepas'] ?? 0) ?></span>
                            </div>
                        </div>

                        <div class="quiz-actions">
                            <label>Difficulté: <input type="number" name="difficulty" value="<?= htmlspecialchars($quiz['difficulty']) ?>" min="1" max="10" required style="width: 60px;"></label>
                            <button type="submit">Modifier Quiz</button>
                            <button type="button" onclick="if(confirm('Supprimer ce quiz ?')) { document.getElementById('delete-form').submit(); }">Supprimer Quiz</button>
                        </div>

                        <div class="quiz-disponibilite" style="margin-top: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9;">
                            <p style="font-weight: bold; margin-bottom: 10px;">Mode de publication :</p>
                            <select name="disponibilite" required style="padding: 5px; font-size: inherit;">
                                <option value="public" <?= ($quiz['disponibilite'] ?? '') === 'public' ? 'selected' : '' ?>>Publique</option>
                                <option value="ami" <?= ($quiz['disponibilite'] ?? '') === 'ami' ? 'selected' : '' ?>>Seulement les amis</option>
                                <option value="private" <?= ($quiz['disponibilite'] ?? '') === 'private' ? 'selected' : '' ?>>Privé</option>
                            </select>
                            <button type="submit" name="action" value="update_disponibilite" style="margin-top: 10px; margin-left: 10px;">Mettre à jour la disponibilité</button>
                        </div>
                    </form>
                    <form id="delete-form" method="POST" action="" style="display: none;">
                        <input type="hidden" name="action" value="delete_quiz">
                        <input type="hidden" name="quiz_id" value="<?= htmlspecialchars($quiz['id']) ?>">
                    </form>
                </article>
            </div>

            <!-- Liste des questions (chaque question a un id et une class) -->
            <h2>Questions du quiz (<?= count($questionsWithAnswers) ?>)</h2>

            <?php if (!empty($questionsWithAnswers)): ?>
                <?php foreach ($questionsWithAnswers as $question): ?>
                    <div id="question-<?= htmlspecialchars($question['id']) ?>" class="question-card">
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="action" value="update_question">
                            <input type="hidden" name="question_id" value="<?= htmlspecialchars($question['id']) ?>">
                            <h3>Question <?= htmlspecialchars($question['numeroQuiz']) ?></h3>
                            <p class="question-text"><input type="text" name="enonce" value="<?= htmlspecialchars($question['enonce']) ?>" required style="width: 100%; border: none; background: transparent; font-size: inherit;"></p>
                            <button type="submit" style="margin-left: 10px;">Modifier</button>
                        </form>
                        <button type="button" onclick="if(confirm('Supprimer cette question ?')) { document.getElementById('delete-question-<?= $question['id'] ?>').submit(); }" style="margin-left: 10px;">Supprimer Question</button>
                        <form id="delete-question-<?= $question['id'] ?>" method="POST" action="" style="display: none;">
                            <input type="hidden" name="action" value="delete_question">
                            <input type="hidden" name="question_id" value="<?= htmlspecialchars($question['id']) ?>">
                        </form>

                        <?php if (!empty($question['answers']) && is_array($question['answers'])): ?>
                            <div class="answers">
                                <h4>Réponses :</h4>
                                <ul>
                                    <?php foreach ($question['answers'] as $answer): ?>
                                        <li class="answer <?= $answer['est_correct'] ? 'correct' : '' ?>">
                                            <form method="POST" action="" style="display: inline;">
                                                <input type="hidden" name="action" value="update_answer">
                                                <input type="hidden" name="answer_id" value="<?= htmlspecialchars($answer['id']) ?>">
                                                <input type="text" name="texte" value="<?= htmlspecialchars($answer['texte']) ?>" required style="border: none; background: transparent;">
                                                <label><input type="checkbox" name="est_correct" <?= $answer['est_correct'] ? 'checked' : '' ?>> Correct</label>
                                                <button type="submit">Mod</button>
                                            </form>
                                            <button type="button" onclick="if(confirm('Supprimer cette réponse ?')) { document.getElementById('delete-answer-<?= $answer['id'] ?>').submit(); }">Sup</button>
                                            <form id="delete-answer-<?= $answer['id'] ?>" method="POST" action="" style="display: none;">
                                                <input type="hidden" name="action" value="delete_answer">
                                                <input type="hidden" name="answer_id" value="<?= htmlspecialchars($answer['id']) ?>">
                                            </form>
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

            <?php if (!empty($flashcardCards)): ?>
                <!-- Liste des cartes flashcard -->
                <h2>Cartes de la flashcard (<?= count($flashcardCards) ?>)</h2>
                
                <?php foreach ($flashcardCards as $card): ?>
                    <div id="card-<?= htmlspecialchars($card['id']) ?>" class="question-card">
                        <form method="POST" action="" style="display: inline;">
                            <input type="hidden" name="action" value="update_card">
                            <input type="hidden" name="card_id" value="<?= htmlspecialchars($card['id']) ?>">
                            <h3>Carte <?= htmlspecialchars($card['numeroCarte']) ?></h3>
                            
                            <div style="margin: 10px 0;">
                                <label style="display: block; margin-bottom: 5px;"><strong>Question :</strong></label>
                                <textarea name="question" required style="width: 100%; border: 1px solid #ccc; padding: 5px; font-size: inherit; min-height: 60px;"><?= htmlspecialchars($card['question']) ?></textarea>
                            </div>
                            
                            <div style="margin: 10px 0;">
                                <label style="display: block; margin-bottom: 5px;"><strong>Réponse :</strong></label>
                                <textarea name="reponse" required style="width: 100%; border: 1px solid #ccc; padding: 5px; font-size: inherit; min-height: 60px;"><?= htmlspecialchars($card['reponse']) ?></textarea>
                            </div>
                            
                            <button type="submit" style="margin-top: 10px;">Modifier</button>
                        </form>
                        <button type="button" onclick="if(confirm('Supprimer cette carte ?')) { document.getElementById('delete-card-<?= $card['id'] ?>').submit(); }" style="margin-left: 10px;">Supprimer Carte</button>
                        <form id="delete-card-<?= $card['id'] ?>" method="POST" action="" style="display: none;">
                            <input type="hidden" name="action" value="delete_card">
                            <input type="hidden" name="card_id" value="<?= htmlspecialchars($card['id']) ?>">
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>