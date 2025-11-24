<?php
$title = "Flashcard";
$style = "./assets/style/quiz/flashcard.css";
require_once '../src/views/partials/header.php';

// Extraire les données envoyées depuis le contrôleur
// crée $question, $quizId, $showAnswer, $prevId, $nextId
extract($viewData);
?>

<div class="container">
   <div class="button" style="grid-column-start: 1; grid-column-end: 2; grid-row-start: 1; grid-row-end: 2;" onclick="window.location.href = '?page=home'">
		<span></span>
    	<p>← Retour</p>
    </div>

  <div class="card">
    <h2><?= $showAnswer ? htmlspecialchars($question['reponse']) : htmlspecialchars($question['question']) ?></h2>
  </div>

  <?php if (!$showAnswer): ?>
    <div class="button" style="grid-column-start: 4; grid-column-end: 5; grid-row-start: 3; grid-row-end: 4;" onclick="this.previousElementSibling.style.transform = this.previousElementSibling.style.transform != 'rotateY(180deg)' ? 'rotateY(180deg)' : 'rotateY(0deg)'">
		<span></span>
    	<p>Afficher la réponse</p>
    </div>
  <?php endif; ?>
	<div class="button" style="grid-column-start: 3; grid-column-end: 4; grid-row-start: 4; grid-row-end: 5;" onclick="window.location.href = '<?php if($nextId): ?>?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $nextId ?><?php else: ?>?page=flashcard&action=end&id=<?= $quizId ?><?php endif; ?>'">
		<span></span>
		<p>Je sais</p>
	</div>
	<div class="button" style="grid-column-start: 5; grid-column-end: 6; grid-row-start: 4; grid-row-end: 5;" onclick="window.location.href = '<?php if($nextId): ?>?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $nextId ?><?php else: ?>?page=flashcard&action=end&id=<?= $quizId ?><?php endif; ?>'">
		<span></span>
		<p>Je ne sais pas</p>
	</div>
	<?php if(!$nextId): ?>
	<div class="button" style="grid-column-start: 6; grid-column-end: 7; grid-row-start: 4; grid-row-end: 5;" onclick="window.location.href = '?page=flashcard&action=end&id=<?= $quizId ?>'">
		<span></span>
		<p>Fin du quiz</p>
	</div>
	<?php endif; ?>

	<div class="button <?php if (!$prevId): ?>disabled <?php endif; ?>" style="grid-column-start: 2; grid-column-end: 3; grid-row-start: 4; grid-row-end: 5;" <?php if ($prevId): ?>onclick="window.location.href = '?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $prevId ?>'" <?php endif; ?>>
		<span></span>
		<p>Précédent</p>
	</div>
<?php if ($nextId): ?>
	<div class="button" style="grid-column-start: 6; grid-column-end: 7; grid-row-start: 4; grid-row-end: 5;" onclick="window.location.href = '?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $nextId ?>'">
		<span></span>
		<p>Suivant</p>
	</div>
<?php endif; ?>

</div>
</body>

</html>