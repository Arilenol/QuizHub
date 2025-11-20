<?php
$title = "Flashcard";
$style = "./assets/style/flashcard.css";
require_once 'partials/header.php';

// Extraire les données envoyées depuis le contrôleur
// crée $question, $quizId, $showAnswer, $prevId, $nextId
extract($viewData); 
?>

<div class="container">

    <button class="btn retour" onclick="window.location.href='?page=home'">&lt; Retour</button>

    <div class="card">
      <h2><?= $showAnswer ? htmlspecialchars($question['reponse']) : htmlspecialchars($question['question']) ?></h2>
    </div>

    <?php if (!$showAnswer): ?>
      <a href="?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $question['id'] ?>&reponse=visible" class="btn show">Afficher la réponse</a>
    <?php endif; ?>

    <div class="answers">
      <?php if ($showAnswer): ?>
        <?php if ($nextId): ?>
          <a href="?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $nextId ?>" class="btn answer">Je sais</a>
          <a href="?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $nextId ?>" class="btn answer">Je ne sais pas</a>
        <?php else: ?>
          <a href="?page=flashcard&action=end&id=<?= $quizId ?>" class="btn answer">Fin du quiz</a>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="arrows">
      <?php if ($prevId): ?>
        <a href="?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $prevId ?>" class="btn nav left">&lt;</a>
      <?php endif; ?>
      <?php if ($nextId): ?>
        <a href="?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $nextId ?>" class="btn nav right">&gt;</a>
      <?php endif; ?>
    </div>

</div>
</body>
</html>
