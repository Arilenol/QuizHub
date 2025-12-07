<?php
$title = "Flashcard";
$style = "./assets/style/quiz/flashcard.css";
require_once '../src/views/partials/header.php';


?>
<div class="buttonAction">
	<button class="retour" onclick="window.location.href = '?page=home'">← Retour</button>
	<button class="signalement" onclick="window.location.href='?page=signalement'">Signaler ce quiz</button>
</div>
<?php if (!($viewData === null)) :
	// Extraire les données envoyées depuis le contrôleur
	// crée $question, $quizId, $showAnswer, $prevId, $nextId
	extract($viewData);

?>
	<div class="container">

		<div class="card" style="grid-column-start: 1; grid-column-end: 8; grid-row-start: 2; grid-row-end: 3;">
			<div class="card-face card-front">
				<h2><?= htmlspecialchars($question['question']) ?>
			</div>
			<div class="card-face card-back">
				<h2><?= htmlspecialchars($question['reponse']) ?>
			</div>
		</div>

		<?php if (!$showAnswer): ?>
			<div class="button" style="grid-column-start: 4; grid-column-end: 5; grid-row-start: 3; grid-row-end: 4;" onclick="this.previousElementSibling.style.transform = this.previousElementSibling.style.transform != 'rotateY(180deg)' ? 'rotateY(180deg)' : 'rotateY(0deg)'">
				<span></span>
				<p>Afficher la réponse</p>
			</div>
		<?php endif; ?>
		<div class="button" style="grid-column-start: 3; grid-column-end: 4; grid-row-start: 4; grid-row-end: 5;" onclick="window.location.href = '<?php if ($nextId): ?>?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $nextId ?><?php else: ?>?page=flashcard&action=end&id=<?= $quizId ?><?php endif; ?>'">
			<span></span>
			<p>Je sais</p>
		</div>
		<div class="button" style="grid-column-start: 5; grid-column-end: 6; grid-row-start: 4; grid-row-end: 5;" onclick="window.location.href = '<?php if ($nextId): ?>?page=flashcard&action=ongoing&id=<?= $quizId ?>&question=<?= $nextId ?><?php else: ?>?page=flashcard&action=end&id=<?= $quizId ?><?php endif; ?>'">
			<span></span>
			<p>Je ne sais pas</p>
		</div>
		<?php if (!$nextId): ?>
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
<?php else: ?>
	<!-- FIN DU QUIZ -->
	<div class="quiz-réalisation">
		<div class="fin-quiz">
			<h2>🎉 Félicitations !</h2>
			<p class="fin">Vous avez terminé la flashcard.</p>
			<div class="actions-fin">
				<button class="valider" onclick="window.location.href='?page=flashcard&id=<?= $quizId ?>&action=start'">Recommencer le quiz</button>
				<button class="valider" onclick="window.location.href='?page=catalogue'">Voir d’autres quiz</button>
				<button class="valider" onclick="window.location.href='?page=home'">Retour à l’accueil</button>
			</div>
		</div>
		<p class="fin">Vous avez aimé le quiz ? N'hésiter pas à le noter : </p>
		<div class="quiz-reactions">
			<?php if (isset($_SESSION['id'])) : ?>
				<form method="POST" action="?page=flashcard&action=end&user=like&id=<?= $quizId ?>">
					<button type="submit" name="reaction" value="like" class="reaction like">
						👍 <?= htmlspecialchars($reactions['nbjaime'] ?? 0) ?>
					</button>
				</form>
				<form method="POST" action="?page=flashcard&action=end&user=dislike&id=<?= $quizId ?>">
					<button type="submit" name="reaction" value="dislike" class="reaction dislike">
						👎 <?= htmlspecialchars($reactions['nbjaimepas'] ?? 0) ?>
					</button>
				</form>
			<?php else : ?>

				<p>Pour débloquer cette fonctionnalité <a href="?page=log&typelog=connection"> Connectez-vous</a> d'abord</p>

			<?php endif; ?>
		</div>
	</div>

<?php endif; ?>
</body>

</html>