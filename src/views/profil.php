<?php
$title = "Profil";
$style = "./assets/style/profil.css";
require_once 'partials/header.php';
?>
<script src="./assets/js/profil.js" defer></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
</head>

<body>

    <button onclick="window.location.href='?page=home'" class="btn retour">&lt; Retour</button>

    <div class="container">

        <div class="profile-card">
            <img src="./assets/images/profil.jpg" alt="Photo de profil" class="avatar" />

            <div class="info">
                <h2><?= $infosUser['username'] ?></h2>
                <p class="username"><?= $infosUser['email'] ?></p>
                <p class="bio"><?= $infosUser['description'] ?></p>

                <div class="stats">
                    <div>
                        <span class="number"><?= $creation ?></span>
                        <span class="label">Quiz créés</span>
                    </div>
                    <div>
                        <span class="number"><?= $played ?></span>
                        <span class="label">Quiz joués</span>
                    </div>
                </div>
                <div class="action">
                    <button class="edit">Modifier le profil</button>
                    <button class="logout" onclick="window.location.href='?page=log&typelog=logout'">Déconnexion</button>
                </div>
            </div>
        </div>

        <div class="tabs">
            <span class="<?= $activeTab === 'creations' ? 'active' : '' ?>"
                onclick="window.location.href='?page=profil'">
                Mes créations
            </span>

            <span class="<?= $activeTab === 'history' ? 'active' : '' ?>"
                onclick="window.location.href='?page=profil&action=showHistory'">
                Historique
            </span>

            <span class="<?= $activeTab === 'friends' ? 'active' : '' ?>"
                onclick="window.location.href='?page=profil&action=displayFriends'">
                Mes amis
            </span>
        </div>

        <div class="quiz-container">
            <?php if (isset($friends)) : ?>

                <?php if ($friends === false) : ?>
                    <p>
                        Vous n'avez pas d'amis pour le moment.
                        <a href="?page=notification">Cliquez ici pour en ajouter</a>
                    </p>
                <?php else : ?>
                    <?php foreach ($friends as $friend) : ?>
                        <div class="friend-card">
                            <div class="avatar"><?= htmlspecialchars($friend['friend_name'][0]) ?></div>
                            <div>
                                <h3><?= htmlspecialchars($friend['friend_name']) ?></h3>
                                <p><?= htmlspecialchars($friend['friend_email']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php elseif (isset($quiz)) : ?>

                <?php if ($quiz === false) : ?>
                    <p class="no-content">Vous avez réalisé aucun quiz pour le moment.</p>
                <?php else : ?>
                    <div class="newCreations">
                        <?php for ($i = 0; $i < count($quiz); $i++): ?>
                            <article class="quiz">
                                <div class="quiz-cat">
                                    <?php if (!empty($quiz[$i]['categories'])): ?>
                                        <?php foreach ($quiz[$i]['categories'] as $cat): ?>
                                            <span class="category"><?= htmlspecialchars($cat) ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <p class="quiz-genre"><?= htmlspecialchars($quiz[$i]['genre'] ?? '') ?></p>
                                <br>
                                <p class="quiz-title"><?= htmlspecialchars($quiz[$i]['title'] ?? '') ?></p>
                                <br>
                                <p class="quiz-description"><?= htmlspecialchars($quiz[$i]['description'] ?? '') ?></p>
                                <br>
                                <br>
                                <div class="editQuizPart">
                                    <!-- a dev -->
                                    <button id="deleteQuiz" onclick="window.location.href='?page=home'">Supprimer le quiz</button>
                                    <button id="editQuiz" onclick="window.location.href='?page=standard&categorie=modify&id=<?= $quiz[$i]['id'] ?>'">Modifier le quiz</button>
                                    <button id="playQuiz" onclick="window.location.href='./?page=<?= $quiz[$i]['genre'] ?>&id=<?= $quiz[$i]['id'] ?> <?= $quiz[$i]['genre'] == 'flashcard' ? '&action=start' : '' ?>'">Jouer</button>
                                </div>
                                <div class="quiz-footer">
                                    <p class="quiz-auteur">Par : Vous</p>
                                    <p class="quiz-date">Publié le : <?= htmlspecialchars($quiz[$i]['date'] ?? '') ?></p>
                                    <div class="quiz-reactions">
                                        <span class="reaction like">👍 <?= htmlspecialchars($quiz[$i]['nbjaime'] ?? 0) ?></span>
                                        <span class="reaction dislike">👎 <?= htmlspecialchars($quiz[$i]['nbjaimepas'] ?? 0) ?></span>
                                    </div>

                            </article>
                        <?php endfor; ?>


                    <?php endif; ?>
                <?php elseif (isset($hist)) : ?>
                    <div class="newCreations">
                        <?php for ($i = 0; $i < count($hist); $i++): ?>
                            <article class="quiz">
                                <div class="quiz-cat">
                                    <?php if (!empty($hist[$i]['categories'])): ?>
                                        <?php foreach ($hist[$i]['categories'] as $cat): ?>
                                            <span class="category"><?= htmlspecialchars($cat) ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                                <p class="quiz-genre"><?= htmlspecialchars($hist[$i]['genre'] ?? '') ?></p>
                                <br>
                                <p class="quiz-title"><?= htmlspecialchars($hist[$i]['title'] ?? '') ?></p>
                                <br>
                                <p class="quiz-description"><?= htmlspecialchars($hist[$i]['description'] ?? '') ?></p>
                                <br>
                                <br>
                                <div class="quiz-footer">
                                    <p class="quiz-auteur">Par : Vous</p>
                                    <p class="quiz-date">Fait le : <?= htmlspecialchars($hist[$i]['dateRealisation'] ?? '') ?></p>
                                    <div class="quiz-reactions">
                                        <span class="reaction like">👍 <?= htmlspecialchars($hist[$i]['nbjaime'] ?? 0) ?></span>
                                        <span class="reaction dislike">👎 <?= htmlspecialchars($hist[$i]['nbjaimepas'] ?? 0) ?></span>
                                    </div>

                            </article>
                        <?php endfor; ?>

                    <?php else :  ?>
                        <p class="no-content">Vous n'avez créé aucun quiz pour le moment.</p>
                    <?php endif; ?>
                    </div>

                    </div>
                    <!-- Overlay -->
                    <div class="modal-overlay" id="profileModal">
                        <div class="modal">

                            <button class="close-modal" id="closeModal">&times;</button>

                            <h2>Modifier le profil</h2>

                            <form method="POST" action="?page=profil&action=updateProfile">
                                <div class="form-group">
                                    <label for="username">Nom d'utilisateur</label>
                                    <input type="text" name="username" id="username"
                                        value="<?= htmlspecialchars($infosUser['username']) ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" id="email"
                                        value="<?= htmlspecialchars($infosUser['email']) ?>">
                                </div>

                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <input type="text" name="description" id="description"
                                        value="<?= htmlspecialchars($infosUser['description']) ?>">
                                </div>

                                <div class="form-group">
                                    <label for="password">Nouveau mot de passe</label>
                                    <div class="input-wrapper">
                                        <input type="password" name="password" id="password"
                                            placeholder="Laisser vide pour ne pas changer">
                                        <i id="eyeMdp" class="fa-solid fa-eye"></i>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="passwordVerif">Confirmer le mot de passe</label>
                                    <div class="input-wrapper">
                                        <input type="password" id="passwordVerif"
                                            placeholder="Laisser vide pour ne pas changer">
                                        <i id="eyeVerif" class="fa-solid fa-eye"></i>
                                    </div>
                                </div>

                                <button type="submit" class="save-btn">Sauvegarder les changements</button>

                            </form>

                        </div>
                    </div>

</body>

</html>