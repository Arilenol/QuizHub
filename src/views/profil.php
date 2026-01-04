<?php
$title = "profil";
$style = "./assets/style/profil.css";
require_once 'partials/header.php'; ?>
</head>

<body>

    <button onclick="window.location.href='?page=home'" class="button" style="margin: 20px 0 0 20px"><span></span><p>&lt; Retour</p></button>

    <div class="container">

        <div class="profile-card">
            <img src="./assets/images/profil.jpg" alt="Photo de profil" class="avatar" />

            <div class="info">
                <h2>COULON-DEPUCCIO Kilian</h2>
                <p class="username">@kilianc</p>
                <p class="bio">J'aime faire des quiz pour aider les gens à apprendre de nouvelles choses.</p>

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
                <div class = "action">
                <button class="button"><span></span><p>Modifier le profil</p></button>
                <button class="button signalement" onclick="window.location.href='?page=log&typelog=logout'"><span></span><p>Déconnexion</p></button>
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
            <?php if (isset($friends)) :
                if ($friends === false) :  ?>
                    <p>Vous n'avez pas d'amis pour le moment. <a href="?page=notification">Cliquez ici pour en ajouter</p></a>
                    <?php else :
                    foreach ($friends as $friend) : ?>
                        <div class="friend-card">
                            <div class="avatar"><?= $friend['friend_name'][0] ?></div>
                            <div>
                                <h3><?= $friend['friend_name'] ?></h3>
                                <p><?= $friend['friend_email'] ?></p>
                            </div>
                        </div>
                <?php endforeach;
                endif; ?>
            <?php else : ?>
                <div class="quiz-card">
                    <div class="tags">
                        <span>Thème</span>
                        <span>Type</span>
                    </div>
                    <h3>Titre</h3>
                    <p>Description</p>
                </div>

                <div class="quiz-card">
                    <div class="tags">
                        <span>Thème</span>
                        <span>Type</span>
                    </div>
                    <h3>Titre</h3>
                    <p>Description</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>

</html>