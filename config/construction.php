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
                dateRealisation DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE CASCADE
                )
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

        $sql = "CREATE TRIGGER trg_after_delete_question
            AFTER DELETE ON Question
            BEGIN
                UPDATE Question 
                SET numeroQuiz = numeroQuiz - 1 
                WHERE quiz_id = OLD.quiz_id AND numeroQuiz > OLD.numeroQuiz;
            END;";

        $conn->exec($sql);

        $sql="CREATE TRIGGER trg_after_delete_part
            AFTER DELETE ON Partie
            BEGIN
                UPDATE Partie 
                SET numeroPartie = numeroPartie - 1 
                WHERE lecon_id = OLD.lecon_id AND numeroPartie > OLD.numeroPartie;
            END;";

        $conn->exec($sql);

        $sql="CREATE TRIGGER trg_after_delete_example
            AFTER DELETE ON Exemple
            BEGIN
                UPDATE Exemple 
                SET numeroExemple = numeroExemple - 1 
                WHERE partie_id = OLD.partie_id AND numeroExemple > OLD.numeroExemple;
            END;";

        $conn->exec($sql);

        $sql="CREATE TRIGGER trg_after_delete_card
            AFTER DELETE ON Carte
            BEGIN
                UPDATE Carte 
                SET numeroCarte = numeroCarte - 1 
                WHERE quiz_id = OLD.quiz_id AND numeroCarte > OLD.numeroCarte;
            END;";

        $conn->exec($sql);

        // Insertions de données fictives



































        $conn->beginTransaction();
        try {
            mt_srand(12345);

            /* ===================== USERS (FAKE) ===================== */
            $users = [];
            $stmtUser = $conn->prepare(
                "INSERT INTO users (username, password, email) VALUES (?, ?, ?)"
            );

            for ($i = 1; $i <= 10; $i++) {
                $stmtUser->execute([
                    "user$i",
                    password_hash("pass$i", PASSWORD_DEFAULT),
                    "user$i@exemple.test"
                ]);
                $users[] = $conn->lastInsertId();
            }

            /* ===================== CATEGORIES ===================== */
            $realCategories = [
                ['Mathématiques', 'Calcul, algèbre et logique'],
                ['Histoire', 'Histoire française et mondiale'],
                ['Géographie', 'Pays, capitales et continents'],
                ['Informatique', 'Programmation et bases informatiques'],
                ['Culture générale', 'Questions variées'],
                ['Sciences', 'Physique, chimie et biologie'],
                ['Français', 'Orthographe et grammaire'],
                ['Anglais', 'Vocabulaire et grammaire'],
                ['Développement web', 'HTML, CSS, JavaScript'],
                ['Bases de données', 'SQL et modélisation'],
            ];

            $categories = [];
            $stmtCat = $conn->prepare(
                "INSERT INTO categories (categorieName, description) VALUES (?, ?)"
            );

            foreach ($realCategories as $cat) {
                $stmtCat->execute($cat);
                $categories[] = $conn->lastInsertId();
            }

            /* ===================== PREPARED STATEMENTS ===================== */
            $stmtQuiz = $conn->prepare(
                "INSERT INTO quiz (user_id, title, description, difficulty, disponibilite, date, genre)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmtParam = $conn->prepare(
                "INSERT OR REPLACE INTO parametreQuiz
        (quiz_id, afficherAvancement, minuterie, afficherScore, recapitulatifFin, ordreAleatoire, repasserErreurs)
        VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmtTestStat = $conn->prepare(
                "INSERT INTO TestStatistiques (quiz_id, difficulty, moyenne) VALUES (?, ?, ?)"
            );

            $stmtCatQuiz = $conn->prepare(
                "INSERT INTO categorie_quiz (category_id, quiz_id) VALUES (?, ?)"
            );

            $stmtLecon = $conn->prepare(
                "INSERT INTO Lecon (user_id, quiz_id, title, description) VALUES (?, ?, ?, ?)"
            );

            $stmtPartie = $conn->prepare(
                "INSERT INTO Partie (numeroPartie, lecon_id, title, content) VALUES (?, ?, ?, ?)"
            );

            $stmtEx = $conn->prepare(
                "INSERT INTO Exemple (numeroExemple, partie_id, consigne, reponse) VALUES (?, ?, ?, ?)"
            );

            $stmtCarte = $conn->prepare(
                "INSERT INTO Carte (quiz_id, numeroCarte, question, reponse) VALUES (?, ?, ?, ?)"
            );

            $stmtQuestion = $conn->prepare(
                "INSERT INTO Question (numeroQuiz, quiz_id, question) VALUES (?, ?, ?)"
            );

            $stmtReponse = $conn->prepare(
                "INSERT INTO Reponse (question_id, reponse, estCorrecte) VALUES (?, ?, ?)"
            );

            $stmtResult = $conn->prepare(
                "INSERT INTO resultat (user_id, quiz_id, score, tempsPris, dateRealisation)
         VALUES (?, ?, ?, ?, ?)"
            );

            $stmtAmiDisp = $conn->prepare(
                "INSERT INTO amiDisponibilite (quiz_id, ami_id) VALUES (?, ?)"
            );

            $stmtDemande = $conn->prepare(
                "INSERT INTO demandeAmi (demandeur_id, receveur_id) VALUES (?, ?)"
            );

            $stmtAmis = $conn->prepare(
                "INSERT INTO amis (user1_id, user2_id) VALUES (?, ?)"
            );

            $stmtRecherche = $conn->prepare(
                "INSERT INTO rechercheHistorique (user_id, recherche, dateRecherche) VALUES (?, ?, ?)"
            );

            /* ===================== QUIZ ===================== */
            $quizData = [
                [
                    'title' => 'Capitales du monde',
                    'desc' => 'Testez vos connaissances sur les capitales',
                    'genre' => 'standard',
                    'difficulty' => 2,
                    'questions' => [
                        ['Quelle est la capitale du Canada ?', ['Ottawa', 'Toronto', 'Vancouver'], 0],
                        ['Quelle est la capitale du Japon ?', ['Tokyo', 'Osaka', 'Kyoto'], 0],
                    ]
                ],
                [
                    'title' => 'Bases du SQL',
                    'desc' => 'Requêtes SQL fondamentales',
                    'genre' => 'test',
                    'difficulty' => 3,
                    'questions' => [
                        ['Quelle commande permet de lire des données ?', ['SELECT', 'INSERT', 'DELETE'], 0],
                        ['Quelle clause filtre les résultats ?', ['WHERE', 'ORDER BY', 'GROUP BY'], 0],
                    ]
                ],
                [
                    'title' => 'Flashcards HTML',
                    'desc' => 'Balises HTML essentielles',
                    'genre' => 'flashcard',
                    'difficulty' => 1,
                    'cards' => [
                        ['<p>', 'Paragraphe'],
                        ['<a>', 'Lien hypertexte'],
                        ['<img>', 'Image'],
                    ]
                ],
            ];

            $quiz_ids = [];

            foreach ($quizData as $i => $qz) {

                $owner = $users[$i % count($users)];
                $stmtQuiz->execute([
                    $owner,
                    $qz['title'],
                    $qz['desc'],
                    $qz['difficulty'],
                    'public',
                    date('Y-m-d'),
                    $qz['genre']
                ]);

                $quiz_id = $conn->lastInsertId();
                $quiz_ids[] = ['id' => $quiz_id, 'genre' => $qz['genre'], 'difficulty' => $qz['difficulty']];

                $stmtParam->execute([$quiz_id, 1, 300, 1, 1, 1, 0]);

                //Insertion lecon
                $lessonTemplates = [
                    'SQL' => [
                        [
                            'title' => 'Introduction au SQL',
                            'desc'  => 'Comprendre le rôle et l’utilité du langage SQL',
                            'parts' => [
                                [
                                    'title' => 'Qu’est-ce que SQL ?',
                                    'content' => 'SQL est un langage permettant de manipuler des bases de données relationnelles.',
                                    'examples' => [
                                        ['Identifier une base', 'Une base contient des tables.'],
                                    ]
                                ],
                                [
                                    'title' => 'Première requête',
                                    'content' => 'La requête SELECT permet de lire des données.',
                                    'examples' => [
                                        ['SELECT * FROM users;', 'Récupère tous les utilisateurs.'],
                                    ]
                                ],
                            ]
                        ],
                    ],

                    'HTML' => [
                        [
                            'title' => 'Bases du HTML',
                            'desc'  => 'Structure d’une page HTML',
                            'parts' => [
                                [
                                    'title' => 'Balises principales',
                                    'content' => 'HTML utilise des balises pour structurer le contenu.',
                                    'examples' => [
                                        ['<p>', 'Paragraphe'],
                                        ['<h1>', 'Titre principal'],
                                    ]
                                ],
                                [
                                    'title' => 'Images et liens',
                                    'content' => 'Les balises <img> et <a> permettent d’afficher des images et des liens.',
                                    'examples' => [
                                        ['<img src="">', 'Affiche une image'],
                                        ['<a href="">', 'Lien cliquable'],
                                    ]
                                ],
                            ]
                        ],
                    ],

                    'Général' => [
                        [
                            'title' => 'Introduction au thème',
                            'desc'  => 'Présentation générale du sujet du quiz',
                            'parts' => [
                                [
                                    'title' => 'Notions clés',
                                    'content' => 'Cette partie présente les concepts importants à retenir.',
                                    'examples' => [
                                        ['Concept clé', 'Définition simple'],
                                    ]
                                ],
                                [
                                    'title' => 'Points importants',
                                    'content' => 'Résumé des éléments essentiels.',
                                    'examples' => [
                                        ['À retenir', 'Information essentielle'],
                                    ]
                                ],
                            ]
                        ],
                    ]
                ];

                foreach ($quiz_ids as $quiz) {

                    // récupération du titre du quiz
                    $stmtTitle = $conn->prepare("SELECT title, user_id FROM quiz WHERE id = ?");
                    $stmtTitle->execute([$quiz['id']]);
                    $quizRow = $stmtTitle->fetch(PDO::FETCH_ASSOC);

                    if (!$quizRow) continue;

                    $quizTitle = $quizRow['title'];
                    $owner     = $quizRow['user_id'];

                    // choix du template
                    if (stripos($quizTitle, 'SQL') !== false) {
                        $templates = $lessonTemplates['SQL'];
                    } elseif (stripos($quizTitle, 'HTML') !== false) {
                        $templates = $lessonTemplates['HTML'];
                    } else {
                        $templates = $lessonTemplates['Général'];
                    }

                    // 1 ou 2 leçons par quiz
                    $nbLessons = mt_rand(1, 2);
                    for ($l = 0; $l < $nbLessons; $l++) {

                        $tpl = $templates[array_rand($templates)];

                        $stmtLecon->execute([
                            $owner,
                            $quiz['id'],
                            $tpl['title'],
                            $tpl['desc']
                        ]);

                        $lecon_id = $conn->lastInsertId();

                        // parties
                        $partNum = 1;
                        foreach ($tpl['parts'] as $part) {

                            $stmtPartie->execute([
                                $partNum++,
                                $lecon_id,
                                $part['title'],
                                $part['content']
                            ]);

                            $partie_id = $conn->lastInsertId();

                            // exemples
                            $exNum = 1;
                            foreach ($part['examples'] as $ex) {
                                $stmtEx->execute([
                                    $exNum++,
                                    $partie_id,
                                    $ex[0],
                                    $ex[1]
                                ]);
                            }
                        }
                    }
                }



                shuffle($categories);
                foreach (array_slice($categories, 0, 2) as $cat) {
                    $stmtCatQuiz->execute([$cat, $quiz_id]);
                }

                if ($qz['genre'] === 'test') {
                    $stmtTestStat->execute([$quiz_id, $qz['difficulty'], mt_rand(60, 90)]);
                }

                if (!empty($qz['questions'])) {
                    foreach ($qz['questions'] as $idx => $q) {
                        $stmtQuestion->execute([$idx + 1, $quiz_id, $q[0]]);
                        $qid = $conn->lastInsertId();

                        foreach ($q[1] as $ri => $rep) {
                            $stmtReponse->execute([$qid, $rep, $ri === $q[2] ? 1 : 0]);
                        }
                    }
                }

                if ($qz['genre'] === 'flashcard') {
                    foreach ($qz['cards'] as $ci => $c) {
                        $stmtCarte->execute([$quiz_id, $ci + 1, "À quoi sert {$c[0]} ?", $c[1]]);
                    }
                }
            }

            $stmtLike = $conn->prepare(
                "INSERT OR IGNORE INTO likes (quiz_id, user_id) VALUES (?, ?)"
            );

            $stmtDislike = $conn->prepare(
                "INSERT OR IGNORE INTO dislikes (quiz_id, user_id) VALUES (?, ?)"
            );

            foreach ($quiz_ids as $quiz) {

                shuffle($users);

                $nbLikes = mt_rand(0, count($users));
                $nbDislikes = mt_rand(0, 3);

                // likes
                for ($i = 0; $i < $nbLikes; $i++) {
                    $stmtLike->execute([$quiz['id'], $users[$i]]);
                }

                // dislikes (reshuffle)
                shuffle($users);
                for ($i = 0; $i < $nbDislikes; $i++) {
                    $stmtDislike->execute([$quiz['id'], $users[$i]]);
                }
            }

            /* ===================== RESULTATS ===================== */
            foreach ($users as $u) {
                $n = ($u === $users[0]) ? 13 : mt_rand(1, 3);
                for ($i = 0; $i < $n; $i++) {
                    $q = $quiz_ids[array_rand($quiz_ids)];
                    $stmtResult->execute([
                        $u,
                        $q['id'],
                        mt_rand(40, 100),
                        mt_rand(60, 1800),
                        date('Y-m-d')
                    ]);
                }
            }

            /* ===================== SOCIAL ===================== */
            $friendPairs = [];
            $tries = 0;

            while (count($friendPairs) < 10 && $tries < 200) {
                $tries++;

                $a = $users[array_rand($users)];
                $b = $users[array_rand($users)];

                if ($a === $b) continue;

                // normalisation de la paire
                $u1 = min($a, $b);
                $u2 = max($a, $b);

                $key = $u1 . '-' . $u2;

                // déjà inséré → skip
                if (isset($friendPairs[$key])) continue;

                $stmtAmis->execute([$u1, $u2]);
                $friendPairs[$key] = true;
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
