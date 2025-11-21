<?php
$title = "profil";
$style = "./assets/style/profil.css";
require_once 'partials/header.php'; ?>
</head>

<body>

    <button onclick="window.location.href='?page=home'" class="btn retour">&lt; Retour</button>

    <div class="container">

        <div class="profile-card">
            <img src="./assets/images/profil.jpg" alt="Photo de profil" class="avatar" />

            <div class="info">
                <h2>COULON-DEPUCCIO Kilian</h2>
                <p class="username">@kilianc</p>
                <p class="bio">J'aime faire des quiz pour aider les gens à apprendre de nouvelles choses.</p>

                <div class="stats">
                    <div>
                        <span class="number">4</span>
                        <span class="label">Quiz créés</span>
                    </div>
                    <div>
                        <span class="number">18</span>
                        <span class="label">Quiz joués</span>
                    </div>
                </div>

                <button class="btn edit">Modifier le profil</button>
                <button class="btn edit" onclick="window.location.href='?page=deconnexion'">Déconnexion</button>
            </div>
        </div>

        <div class="tabs">
            <span>Mes créations</span>
            <span>Historique</span>
        </div>

        <div class="quiz-container">
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
        </div>

    </div>
</body>

</html>