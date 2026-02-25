<?php
$title = "Flashcard";
$style = "./assets/style/quiz/flashcard.css";
require_once '../src/views/partials/header.php';


?>
<?php if (!($viewData === null)) :
	// Extraire les données envoyées depuis le contrôleur
	// crée $question, $quizId, $showAnswer, $prevId, $nextId
	extract($viewData);

?>
	<div class="buttonAction">
		<div class="button" onclick="window.location.href = '?page=home'">
			<span></span>
			<p>← Retour</p>
		</div>
		<?php if ($current <= $total) : ?>
			<div class="progression">
				<progress class="progress-container" value="<?= $current ?>" max="<?= $total ?>"></progress>
				<p><?= $current ?>/<?= $total ?> question(s) réalisée(s)</p>
			</div>
		<?php endif; ?>
		<div class="button signalement" onclick="window.location.href = '?page=signalement&id=<?= $quizId ?>&type=quiz'">
			<span></span>
			<p>Signaler ce quiz</p>
		</div>
	</div>

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
				<?php if (!$hasDisliked) : ?>
					<form method="POST" action="?page=flashcard&action=end&id=<?= $quizId ?>">
						<button type="submit"
							name="reaction"
							value="like"
							class="like"
							id=<?= $hasDisliked ? 'disabled' : '' ?>>
							👍 <?= htmlspecialchars($reactions['nbjaime'] ?? 0) ?>
						</button>
					</form>
				<?php endif; ?>
				<?php if ($hasDisliked) : ?>
					<button type=<?= $hasDisliked ? '' : 'submit' ?>
						name="reaction"
						value="like"
						class="like"
						id=<?= $hasDisliked ? 'disabled' : '' ?>>
						👍 <?= htmlspecialchars($reactions['nbjaime'] ?? 0) ?>
					</button>
				<?php endif; ?>
				<?php if (!$hasLiked) : ?>
					<form method="POST" action="?page=flashcard&action=end&id=<?= $quizId ?>">
						<button type='submit'
							name="reaction"
							value="dislike"
							class="dislike"
							id=<?= $hasLiked ? 'disabled' : '' ?>>
							👎 <?= htmlspecialchars($reactions['nbjaimepas'] ?? 0) ?>
						</button>
					<?php endif; ?>
					<?php if ($hasLiked) : ?>
						<button type='submit'
							name="reaction"
							value="dislike"
							class="dislike"
							id=<?= $hasLiked ? 'disabled' : '' ?>>
							👎 <?= htmlspecialchars($reactions['nbjaimepas'] ?? 0) ?>
						</button>
					<?php endif; ?>
					</form>
				<?php else : ?>

					<p>Pour débloquer cette fonctionnalité <a href="?page=log&typelog=connection"> Connectez-vous</a> d'abord</p>

				<?php endif; ?>
		</div>
	</div>

<?php endif; ?>
</body>

</html>