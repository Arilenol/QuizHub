<?php
$title = "Flashcard";
$style = "./assets/style/quiz/flashcard.css";
require_once '../src/views/partials/header.php';

// Extraire les données envoyées depuis le contrôleur
// crée $question, $quizId, $showAnswer, $prevId, $nextId
extract($viewData);
?>

<div class="container">

  <button class="retour" onclick="window.location.href='?page=home'">← Retour</button>

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
      <a href="?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $prevId ?>" class="btnNavLeft">Précédent;</a>
    <?php endif; ?>
    <?php if ($nextId): ?>
      <a href="?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $nextId ?>" class="btnNavRight">Suivant;</a>
    <?php endif; ?>
  </div>

</div>
</body>

</html>