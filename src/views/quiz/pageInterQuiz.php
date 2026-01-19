<?php
    $title = 'Quiz';
    $style = './assets/style/pageInterQuiz.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>

<div class="inter-quiz-container">
    <!-- Bouton retour en haut -->
    <button class="btn-back" onclick="window.location.href='?page=home'">
        ← Retour
    </button>

    <!-- Section infos du quiz -->
    <div class="quiz-info">
        <!-- Section réactions en haut à droite -->
        <div class="quiz-reactions">
            <div class="reactions-display">
                <span class="reaction-count like">👍 <?= htmlspecialchars($reactions['nbjaime'] ?? 0) ?></span>
                <span class="reaction-count dislike">👎 <?= htmlspecialchars($reactions['nbjaimepas'] ?? 0) ?></span>
            </div>
        </div>

        <h1><?= htmlspecialchars($quizInfo['title'] ?? 'Quiz') ?></h1>
        
        <div class="quiz-description">
            <p><?= htmlspecialchars($quizInfo['description'] ?? 'Pas de description disponible') ?></p>
        </div>

        <button class="btn-launch" onclick="window.location.href='?page=<?= $type ?>&id=<?= $quizId ?>'">
            Lancer le quiz
        </button>
    </div>

    <!-- Section classement des amis -->
    <?php if ($userId !== null): ?>
        <div class="friends-leaderboard">
            <h2>🏆 Classement de vos amis</h2>
            
            <?php if (!empty($friendsLeaderboard)): ?>
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>Rang</th>
                            <th>Ami(e)</th>
                            <th>Meilleur score</th>
                            <th>Temps pris</th>
                            <th>Dernière réalisation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($friendsLeaderboard as $index => $friend): ?>
                            <tr>
                                <td class="rang"><?= $index + 1 ?></td>
                                <td class="nom"><?= htmlspecialchars( $friend['username']) ?></td>
                                <td class="score"><?= $friend['meilleur_score'] ? round($friend['meilleur_score']) . '%' : '-' ?></td>
                                <td class="temps"><?= $friend['tempsPris'] ? $friend['tempsPris'] : '-' ?></td>
                                <td class="date"><?= $friend['dateRealisation'] ? date('d/m/Y', strtotime($friend['dateRealisation'])) : '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-friends">Vous n'avez pas d'amis ayant essayé ce quiz.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
