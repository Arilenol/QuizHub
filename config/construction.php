<?php
require_once 'config.php';
function constructionBD(PDO $conn)
{
    try {
        $sql = "DROP TABLE IF EXISTS categories;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS quiz;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS categorie_quiz;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS amis;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS demandeAmi;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS amiDisponibilite;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS users;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS dislikes;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS likes;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS Lecon;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS Partie;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS Exemple;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS Question;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS Reponse;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS Carte;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS parametreQuiz;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS resultat;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS rechercheHistorique;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS TestStatistiques;";
        $conn->exec($sql);

        $sql = "DROP TABLE IF EXISTS categorie_lecon;";
        $conn->exec($sql);

        $sql = "DROP VIEW IF EXISTS recemmenfait;";
        $conn->exec($sql);

        $sql = "DROP TRIGGER IF EXISTS trg_bef_insert_demandeAmi;";
        $conn->exec($sql);

        $sql = "DROP TRIGGER IF EXISTS trg_after_insert_rechercheHistorique;";
        $conn->exec($sql);

        $sql = "DROP TRIGGER IF EXISTS trg_before_insert_TestStatistiques;";
        $conn->exec($sql);
        $sql = "DROP TRIGGER IF EXISTS trg_before_insert_Carte;";
        $conn->exec($sql);

        $sql = "DROP TRIGGER IF EXISTS trg_bef_insert_battleParticipants;";
        $conn->exec($sql);

        $sql = "DROP TRIGGER IF EXISTS trg_after_delete_question;";
        $conn->exec($sql);






        $sql = "CREATE TABLE IF NOT EXISTS categories(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                categorieName TEXT NOT NULL,
                description TEXT,
                UNIQUE(categorieName)
            );";

        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS users(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL,
                password TEXT NOT NULL,
                email TEXT NOT NULL,
                description TEXT DEFAULT '',
                admin BOOLEAN DEFAULT 0,
                UNIQUE(email)
            );";

        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS demandeAmi(
                demandeur_id INTEGER NOT NULL,
                receveur_id INTEGER NOT NULL,
                PRIMARY KEY(demandeur_id, receveur_id),
                FOREIGN KEY (demandeur_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (receveur_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE(demandeur_id, receveur_id)
            );";

        $conn->exec($sql);

        //trigger à faire ultérieurement
        $sql = "CREATE TABLE IF NOT EXISTS amis(
                user1_id INTEGER NOT NULL,
                user2_id INTEGER NOT NULL,
                PRIMARY KEY(user1_id, user2_id),
                FOREIGN KEY (user1_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (user2_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE(user1_id, user2_id)
            );";

        $conn->exec($sql);


        $sql = "CREATE TABLE IF NOT EXISTS quiz(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                title TEXT NOT NULL,
                description TEXT,
                difficulty INTEGER,
                disponibilite TEXT,
                date DATE,
                genre TEXT NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                CHECK (genre IN ('flashcard','standard','test')),
                CHECK (disponibilite IN ('public','private','ami'))
            );";
        $conn->exec($sql);


        $sql = "CREATE TABLE IF NOT EXISTS Lecon(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                quiz_id INTEGER,
                title TEXT NOT NULL,
                description TEXT,
                disponibilite TEXT,
                date DATE DEFAULT CURRENT_DATE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE,
                CHECK (disponibilite IN ('public','private','ami'))
            );";
        $conn->exec($sql);


        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS amiDisponibilite(
                quiz_id INTEGER,
                lesson_id INTEGER,
                ami_id INTEGER,
                PRIMARY KEY (quiz_id,lesson_id, ami_id),
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE,
                FOREIGN KEY (lesson_id) REFERENCES Lecon(id) ON DELETE CASCADE,
                FOREIGN KEY (ami_id) REFERENCES users(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        $sql = "CREATE TABLE likes (
                like_id INTEGER PRIMARY KEY AUTOINCREMENT,
                quiz_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                UNIQUE (quiz_id, user_id),
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );
            ";

        $conn->exec($sql);

        $sql = "CREATE TABLE dislikes (
                    dislike_id INTEGER PRIMARY KEY AUTOINCREMENT,
                    quiz_id INTEGER NOT NULL,
                    user_id INTEGER NOT NULL,
                    UNIQUE (quiz_id, user_id),
                    FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                );
                ";

        $conn->exec($sql);


        $sql = "CREATE TABLE IF NOT EXISTS categorie_quiz(
                category_id INTEGER,
                quiz_id INTEGER,
                PRIMARY KEY (category_id, quiz_id),
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
            );";
        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS Partie(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                numeroPartie INTEGER,
                lecon_id INTEGER,
                title TEXT NOT NULL,
                content TEXT,
                FOREIGN KEY (lecon_id) REFERENCES Lecon(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS Exemple(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                numeroExemple INTEGER,
                partie_id INTEGER,
                consigne TEXT NOT NULL,
                reponse TEXT NOT NULL,
                FOREIGN KEY (partie_id) REFERENCES Partie(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS Question(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                numeroQuiz INTEGER,
                quiz_id INTEGER NOT NULL,
                question TEXT NOT NULL,
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS Reponse(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                question_id INTEGER,
                reponse TEXT NOT NULL,
                estCorrecte BOOLEAN,
                FOREIGN KEY (question_id) REFERENCES Question(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        //trigger pour le check (il faut que le quiz soit de genre 'flashcard')
        $sql = "CREATE TABLE IF NOT EXISTS Carte (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                quiz_id INTEGER,
                numeroCarte INTEGER,
                question TEXT NOT NULL,
                reponse TEXT NOT NULL,
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
            );";


        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS parametreQuiz (
                quiz_id INTEGER PRIMARY KEY,
                afficherAvancement BOOLEAN,
                minuterie INTEGER,
                afficherScore BOOLEAN,
                recapitulatifFin BOOLEAN,
                ordreAleatoire BOOLEAN,
                repasserErreurs BOOLEAN,
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS resultat (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                quiz_id INTEGER,
                score INTEGER,
                tempsPris INTEGER,
                dateRealisation DATE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        //trigger à faire ultérieurement
        $sql = "CREATE TABLE IF NOT EXISTS rechercheHistorique (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                recherche TEXT NOT NULL,
                dateRecherche DATE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        //à vérifier
        $sql = "CREATE VIEW IF NOT EXISTS recemmenfait AS
                SELECT users.id AS user_id, quiz.id AS quiz_id, MAX(resultat.dateRealisation) AS derniere_realisation
                FROM users
                INNER JOIN resultat ON users.id = resultat.user_id
                INNER JOIN quiz ON quiz.id = resultat.quiz_id
                GROUP BY users.id, quiz.id ORDER BY resultat.dateRealisation DESC LIMIT 10;";

        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS BattleQuiz (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                defiant_id INTEGER NOT NULL,
                quiz_id INTEGER NOT NULL,
                dateBattle DATE,
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE,
                FOREIGN KEY (defiant_id) REFERENCES users(id) ON DELETE CASCADE
            );";
        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS BattleParticipants (
                battle_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                fini BOOLEAN NOT NULL,
                score INTEGER,
                PRIMARY KEY (battle_id, user_id),
                FOREIGN KEY (battle_id) REFERENCES BattleQuiz(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        //trigger pour le check (il faut que le quiz soit de genre 'test')
        $sql = "CREATE TABLE IF NOT EXISTS TestStatistiques (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                quiz_id INTEGER,
                difficulty INTEGER,
                moyenne FLOAT,
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS categorie_lecon(
                category_id INTEGER,
                lesson_id INTEGER,
                PRIMARY KEY (category_id, lesson_id),
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
                FOREIGN KEY (lesson_id) REFERENCES Lecon(id) ON DELETE CASCADE
            );";

        $conn->exec($sql);

        $sql = "CREATE TRIGGER trg_bef_insert_demandeAmi
            BEFORE INSERT ON demandeAmi
            BEGIN
                SELECT 
                CASE 
                    WHEN EXISTS (
                        SELECT 1 FROM amis 
                        WHERE (user1_id = NEW.demandeur_id AND user2_id = NEW.receveur_id) 
                           OR (user1_id = NEW.receveur_id AND user2_id = NEW.demandeur_id)
                    ) THEN
                        RAISE(ABORT, 'Vous êtes déjà amis avec cet utilisateur.')
                    WHEN EXISTS (
                        SELECT 1 FROM demandeAmi 
                        WHERE demandeur_id = NEW.receveur_id AND receveur_id = NEW.demandeur_id
                    ) THEN
                        RAISE(ABORT, 'Une demande d''ami en attente existe déjà dans l''autre sens.')
                END;
            END;";

        $conn->exec($sql);

        //trigger nécessaire car il n'y a que les 10 dernières recherches qui sont stockées
        $sql = "CREATE TRIGGER trg_after_insert_rechercheHistorique
            AFTER INSERT ON rechercheHistorique
            BEGIN
                DELETE FROM rechercheHistorique
                WHERE id NOT IN (
                    SELECT id FROM rechercheHistorique
                    ORDER BY dateRecherche DESC
                    LIMIT 10
                );
            END;";
        $conn->exec($sql);

        $sql = "CREATE TRIGGER trg_before_insert_TestStatistiques
            BEFORE INSERT ON TestStatistiques
            BEGIN
                SELECT 
                CASE 
                    WHEN NOT EXISTS (
                        SELECT 1 FROM quiz 
                        WHERE id = NEW.quiz_id AND genre = 'test'
                    ) THEN
                        RAISE(ABORT, 'Le quiz doit être de genre ''test'' pour insérer des statistiques.')
                END;
            END;";

        $conn->exec($sql);

        $sql = "CREATE TRIGGER trg_before_insert_Carte
            BEFORE INSERT ON Carte
            BEGIN
                SELECT 
                CASE 
                    WHEN NOT EXISTS (
                        SELECT 1 FROM quiz 
                        WHERE id = NEW.quiz_id AND genre = 'flashcard'
                    ) THEN
                        RAISE(ABORT, 'Le quiz doit être de genre ''flashcard'' pour insérer des cartes.')
                END;
            END;";

        $conn->exec($sql);

        $sql = "CREATE TRIGGER trg_bef_insert_battleParticipants
            BEFORE INSERT ON BattleParticipants
            BEGIN
                SELECT
                CASE
                    WHEN NOT EXISTS (
                        SELECT 1 FROM Amis
                        WHERE new.user_id = user1_id OR new.user_id = user2_id
                    ) THEN
                        RAISE(ABORT, 'L''utilisateur n''est pas ami avec le défieur.')
                END;
            END;";
        $conn->exec($sql);

        $sql="CREATE TRIGGER trg_after_delete_question
            AFTER DELETE ON Question
            BEGIN
                UPDATE Question 
                SET numeroQuiz = numeroQuiz - 1 
                WHERE quiz_id = OLD.quiz_id AND numeroQuiz > OLD.numeroQuiz;
            END;";

        $conn->exec($sql);

        // Insertions de données fictives



































        $conn->beginTransaction();
        try {
            mt_srand(12345); // seed reproductible

            // ---------- 10 users ----------
            $users = [];
            $stmtUser = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            for ($i = 1; $i <= 10; $i++) {
                $username = "user{$i}";
                $password = password_hash("pass{$i}", PASSWORD_DEFAULT);
                $email = "user{$i}@exemple.test";
                $stmtUser->execute([$username, $password, $email]);
                $users[] = $conn->lastInsertId();
            }

            // ---------- 20 categories ----------
            $stmtCat = $conn->prepare("INSERT INTO categories (categorieName, description) VALUES (?, ?)");
            for ($i = 1; $i <= 20; $i++) {
                $name = "Catégorie {$i}";
                $desc = "Description pour la catégorie {$i}";
                $stmtCat->execute([$name, $desc]);
                $categories[] = $conn->lastInsertId();
            }

            // helper prepared statements
            // include user_id so each quiz is linked to its owner
            $stmtQuiz = $conn->prepare("INSERT INTO quiz (user_id, title, description, difficulty, disponibilite, date, genre) VALUES (?, ?, ?, ?, ?, ?, ?)");
            //---------- modified line --------------
            $stmtParam = $conn->prepare("INSERT OR REPLACE INTO parametreQuiz (quiz_id, afficherAvancement, minuterie, afficherScore, recapitulatifFin, ordreAleatoire, repasserErreurs) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtTestStat = $conn->prepare("INSERT INTO TestStatistiques (quiz_id, difficulty, moyenne) VALUES (?, ?, ?)");
            $stmtCatQuiz = $conn->prepare("INSERT INTO categorie_quiz (category_id, quiz_id) VALUES (?, ?)");
            $stmtLecon = $conn->prepare("INSERT INTO Lecon (user_id, quiz_id, title, description) VALUES (?, ?, ?, ?)");
            $stmtPartie = $conn->prepare("INSERT INTO Partie (numeroPartie, lecon_id, title, content) VALUES (?, ?, ?, ?)");
            $stmtEx = $conn->prepare("INSERT INTO Exemple (numeroExemple, partie_id, consigne, reponse) VALUES (?, ?, ?, ?)");
            $stmtCarte = $conn->prepare("INSERT INTO Carte (quiz_id, numeroCarte, question, reponse) VALUES (?, ?, ?, ?)");
            $stmtQuestion = $conn->prepare("INSERT INTO Question (numeroQuiz, quiz_id, question) VALUES (?, ?, ?)");
            $stmtReponse = $conn->prepare("INSERT INTO Reponse (question_id, reponse, estCorrecte) VALUES (?, ?, ?)");
            $stmtResult = $conn->prepare("INSERT INTO resultat (user_id, quiz_id, score, tempsPris, dateRealisation) VALUES (?, ?, ?, ?, ?)");
            $stmtAmiDisp = $conn->prepare("INSERT INTO amiDisponibilite (quiz_id, ami_id) VALUES (?, ?)");
            $stmtDemande = $conn->prepare("INSERT INTO demandeAmi (demandeur_id, receveur_id) VALUES (?, ?)");
            $stmtAmis = $conn->prepare("INSERT INTO amis (user1_id, user2_id) VALUES (?, ?)");
            $stmtRecherche = $conn->prepare("INSERT INTO rechercheHistorique (user_id, recherche, dateRecherche) VALUES (?, ?, ?)");

            // ---------- Create ~30 quizzes (moyenne 3 par user) ----------
            $quiz_ids = [];
            $totalQuizzes = 30;
            $genres = ['flashcard', 'standard', 'test'];
            $dispos = ['public', 'private', 'ami'];
            for ($q = 1; $q <= $totalQuizzes; $q++) {
                $owner = $users[($q - 1) % count($users)]; // distribue les quizzes sur les users
                $title = "Quiz {$q}";
                $desc = "Description pour quiz {$q}";
                $difficulty = mt_rand(1, 5);
                $disponibilite = $dispos[array_rand($dispos)];
                $date = date('Y-m-d', strtotime("-" . mt_rand(0, 365) . " days"));
                $genre = $genres[array_rand($genres)];
                // pass owner as first parameter to populate user_id
                $stmtQuiz->execute([$owner, $title, $desc, $difficulty, $disponibilite, $date, $genre]);
                $quiz_id = $conn->lastInsertId();
                $quiz_ids[] = ['id' => $quiz_id, 'genre' => $genre, 'difficulty' => $difficulty];

                // categories link: assign 1-3 categories
                $numCats = mt_rand(1, 3);
                $used = [];
                for ($c = 0; $c < $numCats; $c++) {
                    $cat = $categories[array_rand($categories)];
                    if (in_array($cat, $used)) continue;
                    $used[] = $cat;
                    $stmtCatQuiz->execute([$cat, $quiz_id]);
                }

                // amiDisponibilite: si disponibilite='ami' link to a couple of friends (placeholder)
                if ($disponibilite === 'ami') {
                    $friendId = $users[array_rand($users)];
                    $stmtAmiDisp->execute([$quiz_id, $friendId]);
                }

                // parametres pour chaque quiz
                $affAv = mt_rand(0, 1);
                $minuterie = mt_rand(0, 600); // secondes
                $affScore = mt_rand(0, 1);
                $recap = mt_rand(0, 1);
                $ordre = mt_rand(0, 1);
                $repasser = mt_rand(0, 1);
                $stmtParam->execute([$quiz_id, $affAv, $minuterie, $affScore, $recap, $ordre, $repasser]);

                // TestStatistiques si genre == 'test'
                if ($genre === 'test') {
                    $moy = round(mt_rand(50, 95) / 10, 2) + $difficulty; // pseudo moyenne
                    $stmtTestStat->execute([$quiz_id, $difficulty, $moy]);
                }

                // === Prepare statements ===
                $stmtLecon = $conn->prepare("
                    INSERT INTO Lecon (user_id, quiz_id, title, description)
                    VALUES (?, ?, ?, ?)
                ");

                $stmtPartie = $conn->prepare("
                    INSERT INTO Partie (numeroPartie, lecon_id, title, content)
                    VALUES (?, ?, ?, ?)
                ");

                $stmtEx = $conn->prepare("
                    INSERT INTO Exemple (numeroExemple, partie_id, consigne, reponse)
                    VALUES (?, ?, ?, ?)
                ");


                if ($q <= 10) {

                    // 2 Leçons par quiz
                    for ($ln = 1; $ln <= 2; $ln++) {

                        $stmtLecon->execute([
                            $owner,
                            $quiz_id,
                            "Leçon {$ln} du quiz {$q}",
                            "Description de la leçon {$ln} liée au quiz {$q}"
                        ]);

                        $lecon_id = $conn->lastInsertId();

                        // 2 Parties par leçon
                        for ($pnum = 1; $pnum <= 2; $pnum++) {

                            $stmtPartie->execute([
                                $pnum,
                                $lecon_id,
                                "Partie {$pnum} - Leçon {$ln}",
                                "Contenu expliqué pour la partie {$pnum} de la leçon {$ln}."
                            ]);

                            $partie_id = $conn->lastInsertId();

                            // 2 Exemples par partie
                            for ($exnum = 1; $exnum <= 2; $exnum++) {

                                $stmtEx->execute([
                                    $exnum,
                                    $partie_id,
                                    "Consigne exemple {$exnum} pour la partie {$pnum}",
                                    "Réponse exemple {$exnum} pour la partie {$pnum}"
                                ]);
                            }
                        }
                    }
                }




                // Cartes pour flashcard quizzes
                if ($genre === 'flashcard') {
                    $numCartes = mt_rand(3, 6);
                    for ($ci = 1; $ci <= $numCartes; $ci++) {
                        $stmtCarte->execute([$quiz_id, $ci, "Question carte {$ci} (quiz {$quiz_id})", "Réponse carte {$ci}"]);
                    }
                }

                // Questions & Réponses: entre 5 et 10 questions, each 2-6 answers
                $nbQuestions = mt_rand(5, 10);
                for ($qi = 1; $qi <= $nbQuestions; $qi++) {
                    $stmtQuestion->execute([$qi, $quiz_id, "Question {$qi} pour le quiz {$quiz_id}"]);
                    $question_id = $conn->lastInsertId();
                    $nbReponses = mt_rand(2, 6);
                    // ensure at least one correct answer
                    $correctIndex = mt_rand(1, $nbReponses);
                    for ($ri = 1; $ri <= $nbReponses; $ri++) {
                        $isCorrect = ($ri === $correctIndex) ? 1 : 0;
                        $stmtReponse->execute([$question_id, "Réponse {$ri} pour question {$question_id}", $isCorrect]);
                    }
                }
            }



            foreach ($quiz_ids as $quiz) {
                $quiz_id = $quiz['id'];

                // Nombre aléatoire de likes et dislikes
                $nbLikes = mt_rand(0, 5);
                $nbDislikes = mt_rand(0, 3);

                // Shuffle des users pour éviter doublons
                $shuffledUsers = $users;
                shuffle($shuffledUsers);

                // Insertion des likes
                for ($i = 0; $i < $nbLikes && $i < count($shuffledUsers); $i++) {
                    $user_id = $shuffledUsers[$i];
                    $stmt = $conn->prepare("
                        INSERT OR IGNORE INTO likes (quiz_id, user_id)
                        VALUES (?, ?)
                    ");
                    $stmt->execute([$quiz_id, $user_id]);
                }

                // Re-shuffle pour dislikes
                $shuffledUsers = $users;
                shuffle($shuffledUsers);

                // Insertion des dislikes
                for ($i = 0; $i < $nbDislikes && $i < count($shuffledUsers); $i++) {
                    $user_id = $shuffledUsers[$i];
                    $stmt = $conn->prepare("
                        INSERT OR IGNORE INTO dislikes (quiz_id, user_id)
                        VALUES (?, ?)
                    ");
                    $stmt->execute([$quiz_id, $user_id]);
                }
            }


            // ---------- resultats : moyenne 2 par user, 1 user with 13 ----------
            $stmtCountResult = $conn->prepare("SELECT COUNT(*) FROM resultat WHERE user_id = ?");
            $stmtInsertRes = $stmtResult;
            // pick a user to have 13
            $heavyUser = $users[0];
            foreach ($users as $uid) {
                $target = ($uid == $heavyUser) ? 13 : mt_rand(1, 3); // others 1-3 avg ~2
                for ($r = 0; $r < $target; $r++) {
                    $quiz = $quiz_ids[array_rand($quiz_ids)];
                    $score = mt_rand(0, 100);
                    $temps = mt_rand(10, 3600);
                    $date = date('Y-m-d', strtotime("-" . mt_rand(0, 365) . " days"));
                    $stmtInsertRes->execute([$uid, $quiz['id'], $score, $temps, $date]);
                }
            }

            // ---------- demandes d'ami en attente : 5 ----------
            // generate 5 pending requests with distinct pairs and not friends
            $pairs = [];
            while (count($pairs) < 5) {
                $a = $users[array_rand($users)];
                $b = $users[array_rand($users)];
                if ($a == $b) continue;
                $key = $a . '-' . $b;
                $rev = $b . '-' . $a;
                if (isset($pairs[$key]) || isset($pairs[$rev])) continue;
                $pairs[$key] = [$a, $b];
                $stmtDemande->execute([$a, $b]);
            }

            // ---------- amis : ~10 relations (bidirectional single entry per pair) ----------
            $friendPairs = [];
            $tries = 0;
            while (count($friendPairs) < 10 && $tries < 200) {
                $a = $users[array_rand($users)];
                $b = $users[array_rand($users)];
                $tries++;
                if ($a == $b) continue;
                $k = min($a, $b) . '-' . max($a, $b);
                if (isset($friendPairs[$k])) continue;
                // insert one direction
                $stmtAmis->execute([$a, $b]);
                $friendPairs[$k] = [$a, $b];
            }

            // ---------- rechercheHistorique : keep many users with up to 10 each but trigger will trim ----------
            foreach ($users as $uid) {
                $nb = mt_rand(3, 12);
                for ($i = 0; $i < $nb; $i++) {
                    $q = "recherche_{$uid}_" . ($i + 1);
                    $date = date('Y-m-d', strtotime("-" . mt_rand(0, 60) . " days"));
                    $stmtRecherche->execute([$uid, $q, $date]);
                }
            }

            // ensure at least 3 rows in some smaller tables if necessary:
            // amiDisponibilite already had some. Add a few more arbitrary links
            foreach (array_slice($quiz_ids, 0, 6) as $qi) {
                $a = $users[array_rand($users)];
                $stmtAmiDisp->execute([$qi['id'], $a]);
            }

            $stmtCatLecon = $conn->prepare("INSERT INTO categorie_lecon(category_id, lesson_id) VALUES (?, ?)");

            // On prend entre 1 et 3 catégories au hasard
            $numCats = rand(1, 10);
            $used = [];
            for ($c = 0; $c < $numCats; $c++) {
                $cat = $categories[array_rand($categories)];
                if (in_array($cat, $used)) continue;
                $used[] = $cat;
                $stmtCatLecon->execute([$cat, $lecon_id]);
            }

            // Insert a few rows into resultat already done; ensure categorie_quiz has many rows (done).
            // Ensure Question/Reponse > 3 rows created via quiz loop.

            $conn->commit();
            echo "Insertions terminées.\n";
        } catch (Exception $e) {
            $conn->rollBack();
            echo "Erreur: " . $e->getMessage() . "\n";
            throw $e;
        }
    } catch (PDOException $e) {
        die("Creation table categories failed: " . $e->getMessage());
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}
