<?php
class LessonModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère une leçon par son ID
     *
     * @param int $id Identifiant de la leçon
     * @return array|false Tableau associatif de la leçon, ou false si non trouvé
     */
    public function getLesson(int $id): array|false
    {
        $stmt = $this->db->prepare("
        SELECT 
            l.id,
            l.title,
            l.description,
            l.date AS date,
            l.quiz_id,
            l.disponibilite,
            u.username AS username,
            q.genre AS genre
        FROM lecon l
        JOIN users u ON u.id = l.user_id
        LEFT JOIN quiz q ON q.id = l.quiz_id
        WHERE l.id = ?
        LIMIT 1
    ");

        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: false;
    }

    /**
     * Récupère les différentes parties de la leçon par son ID
     *
     * @param int $id Identifiant de la leçon
     * @return array|false Tableau associatif des parties de la leçon, ou false si non trouvé
     */
    public function getPart(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT title, content,id
            FROM partie
            WHERE lecon_id = ?
            ORDER BY numeroPartie ASC
        ");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les leçons avec informations complètes en triant par date décroissante.
     *
     * Cette méthode retourne l'ensemble des leçons avec tous les détails associés :
     * titre, description, auteur, quiz associé, catégories, et comptage des likes/dislikes.
     *
     * @return array  Tableau de tableaux associatifs contenant les infos de chaque leçon.
     */
    public function getAllInfoLessonsOrderByDate(): array
    {
        $stmt = $this->db->query("
        SELECT
            l.id AS id,
            l.title AS lecon_title,
            l.description AS lecon_description,
            l.date AS lecon_date,
            'lecon' AS genre,
            u.id AS creatorId,
            u.username AS user_name,

            (
                SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                FROM categorie_lecon cq
                JOIN categories c ON c.id = cq.category_id
                WHERE cq.lesson_id = l.id
            ) AS categories,

            (SELECT COUNT(*) FROM likesLecon ll WHERE ll.lecon_id = l.id) AS nbjaime,
            (SELECT COUNT(*) FROM dislikesLecon dl WHERE dl.lecon_id = l.id) AS nbjaimepas

        FROM Lecon l
        JOIN users u ON u.id = l.user_id

        ORDER BY l.date DESC ,(nbjaime - nbjaimepas) DESC

    ");

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            $row['categories'] = $row['categories']
                ? explode(',', $row['categories'])
                : [];
        }

        return $results;
    }


    /**
     * Récupère toutes les leçons avec informations complètes.
     *
     * Cette méthode retourne l'ensemble des leçons avec tous les détails associés :
     * titre, description, auteur, quiz associé, catégories, et comptage des likes/dislikes.
     *
     * @return array  Tableau de tableaux associatifs contenant les infos de chaque leçon.
     */
    public function getAllInfoLessons(): array
    {
        $stmt = $this->db->query("
        SELECT
            l.id AS lecon_id,
            l.title AS lecon_title,
            l.description AS lecon_description,
            l.date AS lecon_date,

            u.id AS creatorId,
            u.username AS user_name,

            (
                SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                FROM categorie_lecon cq
                JOIN categories c ON c.id = cq.category_id
                WHERE cq.lesson_id = l.id
            ) AS categories,

            (SELECT COUNT(*) FROM likesLecon ll WHERE ll.lecon_id = l.id) AS nbjaime,
            (SELECT COUNT(*) FROM dislikesLecon dl WHERE dl.lecon_id = l.id) AS nbjaimepas

        FROM Lecon l
        JOIN users u ON u.id = l.user_id

        ORDER BY (nbjaime - nbjaimepas) DESC , l.date DESC

    ");

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($results as &$row) {
            $row['categories'] = $row['categories']
                ? explode(',', $row['categories'])
                : [];
        }

        return $results;
    }

    /**
     * Récupère les exemples associés à une partie de leçon.
     *
     * Cette méthode retourne tous les exemples liés à une partie spécifique,
     * classés par numéro d'ordre.
     *
     * @param int $id Identifiant de la partie
     * @return array|false Tableau associatif des exemples, ou false si non trouvé
     */
    public function getExemple(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT consigne, reponse, numeroExemple, partie_id
            FROM exemple
            WHERE partie_id = ?
            ORDER BY numeroExemple ASC
        ");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle leçon complète avec toutes ses parties, exemples et restrictions d'amis.
     *
     * Cette méthode effectue une transaction complète qui crée une leçon, insère
     * toutes ses parties, tous ses exemples, et les restrictions d'amis si applicable.
     * En cas d'erreur, la transaction est annulée et aucune donnée n'est conservée.
     *
     * @param int    $id              Identifiant de l'utilisateur créateur de la leçon.
     * @param string $title           Titre de la leçon.
     * @param string $description     Description de la leçon.
     * @param int    $nbParts         Nombre total de parties dans la leçon.
     * @param array  $nbExemple       Tableau spécifiant le nombre d'exemples pour chaque partie.
     * @param array  $TAB_CONTENU     Tableau contenant toutes les informations des parties et exemples.
     * @param array  $TAB_AMI_CHOISI  Tableau des IDs d'amis autorisés à accéder (si dispo='ami').
     * @param string $disponibilite   Type de disponibilité ('public', 'ami', etc.).
     * @param int|null $quizSelected  Identifiant du quiz associé à la leçon (optionnel).
     *
     * @return bool  true si la création est réussie, false en cas d'erreur.
     */
    public function createLesson(int $id, String $title, String $description, int $nbParts, array $nbExemple, array $TAB_CONTENU, array $TAB_AMI_CHOISI, array $TAB_CATEGORIE_CHOISI,  string $disponibilite, ?int $quizSelected): int|false
    {
        try {
            $this->db->beginTransaction();

            $newLesson = $this->insertLesson($id, $quizSelected, $title, $description, $disponibilite);
            if (!$newLesson) {
                throw new PDOException("erreur dans l\'insertion de la leçon dans LessonModel.php/createLesson");
            }
            for ($i = 0; $i < $nbParts; $i++) {

                $newPart = $this->insertPart($i, $newLesson, $TAB_CONTENU[$i]['name'], $TAB_CONTENU[$i]['content']);
                if (!$newPart) {
                    throw new PDOException('erreur dans l\'insertion de la partie ' . $i . ' dans LessonModel.php/createLesson');
                }
                for ($k = 0; $k < $nbExemple[$i]; $k++) {
                    $newExample = $this->insertExample($k, $newPart, $TAB_CONTENU[$i]['exemples'][$k]['consigne'], $TAB_CONTENU[$i]['exemples'][$k]['reponse']);
                    if (!$newExample) {
                        throw new PDOException('erreur dans l\'insertion de l\'exemple ' . $k . ' de la partie ' . $i . ' dans LessonModel.php/createLesson');
                    }
                }
            }
            foreach ($TAB_CATEGORIE_CHOISI as $categorie) {
                $newLessonCategorie = $this->insertLessonCategorie($newLesson, (int)$categorie);
                if (!$newLessonCategorie) {
                    throw new PDOException("erreur dans l\'insertion des catégories dans LessonModel.php/createLesson");
                }
            }
            if ($disponibilite == 'ami') {
                $amisSelectionnes = $this->normalizeSelectedFriends($id, $TAB_AMI_CHOISI);
                foreach ($amisSelectionnes as $ami) {
                    $newAmiDispo = $this->insertAmiDispo($newLesson, (int)$ami);
                    if (!$newAmiDispo) {
                        throw new PDOException("erreur dans l\'insertion des amis dans QuizModel.php/createQuiz");
                    }
                }
            }
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            error_log("Erreur création leçon : " . $e->getMessage());
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Insère une restriction d'accès à la leçon pour un ami spécifique.
     *
     * Cette méthode crée une association entre une leçon et un ami,
     * indiquant que l'ami peut accéder à la leçon.
     *
     * @param int $lesson_id  Identifiant de la leçon.
     * @param int $ami_id     Identifiant de l'ami autorisé à accéder à la leçon.
     *
     * @return int|false  Retourne l'ID de l'ami inséré, ou false en cas d'erreur.
     */
    public function insertAmiDispo(int $lesson_id, int $ami_id)
    {
        try {
            $newAmiDispo = $this->db->prepare("INSERT INTO amiDisponibilite(lesson_id, ami_id) VALUES (?, ?);");
            $newAmiDispo->bindValue(1, $lesson_id);
            $newAmiDispo->bindValue(2, $ami_id);

            $reussite = $newAmiDispo->execute();
            if ($reussite === false) {
                return false;
            } else {
                return $ami_id;
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion d'ami dispo : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les quizzes créés par un utilisateur.
     *
     * Cette méthode retourne la liste des quizzes associés à un auteur spécifié,
     * incluant leur ID et titre.
     *
     * @param int $authorId  Identifiant de l'utilisateur auteur.
     *
     * @return array|false  Tableau associatif des quizzes (id, title), ou false en cas d'erreur.
     */
    public function getQuizByAuthor(int $authorId)
    {
        $sql = $this->db->prepare("SELECT id,title FROM quiz WHERE user_id = ? ;");

        $sql->bindParam(1, $authorId);
        $reussite = $sql->execute();
        if ($reussite) {
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return false;
        }
    }

    /**
     * Insère une nouvelle leçon dans la base de données.
     *
     * Cette méthode crée un enregistrement leçon avec les informations fournies,
     * incluant potentiellement un quiz associé.
     *
     * @param int    $user_id     Identifiant de l'utilisateur créateur de la leçon.
     * @param int|null $quizSelected Identifiant du quiz associé à la leçon (optionnel).
     * @param string $title       Titre de la leçon.
     * @param string $description Description de la leçon.
     *
     * @return int|false  Retourne l'ID de la leçon insérée, ou false en cas d'erreur.
     */
    public function insertLesson(int $user_id, ?int $quizSelected, String $title, String $description, string $disponibilite)
    {
        try {
            $newLesson = $this->db->prepare("INSERT INTO Lecon (user_id, quiz_id, title, description, disponibilite) VALUES (?, ?, ?, ?, ?);");
            $newLesson->bindValue(1, $user_id);
            $newLesson->bindValue(2, $quizSelected);
            $newLesson->bindValue(3, $title);
            $newLesson->bindValue(4, $description);
            $newLesson->bindValue(5, $disponibilite);
            $reussite = $newLesson->execute();
            if (!$reussite) {
                return false;
            } else {
                return $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion de leçon : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Insère une nouvelle partie pour une leçon.
     *
     * Cette méthode crée un enregistrement partie avec un numéro d'ordre,
     * un titre et du contenu associé à une leçon.
     *
     * @param int    $numeroPartie  Numéro d'ordre de la partie au sein de la leçon.
     * @param int    $lecon_id      Identifiant de la leçon à laquelle appartient la partie.
     * @param string $title         Titre de la partie.
     * @param string $content       Contenu textuel de la partie.
     *
     * @return int|false  Retourne l'ID de la partie insérée, ou false en cas d'erreur.
     */
    public function insertPart(int $numeroPartie, int $lecon_id, String $title, string $content)
    {
        try {
            $newPart = $this->db->prepare("INSERT INTO Partie (numeroPartie, lecon_id, title, content) VALUES (?, ?, ?, ?);");
            $newPart->bindValue(1, $numeroPartie);
            $newPart->bindValue(2, $lecon_id);
            $newPart->bindValue(3, $title);
            $newPart->bindValue(4, $content);

            $reussite = $newPart->execute();

            if ($reussite) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion de partie : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Insère un nouvel exemple pour une partie de leçon.
     *
     * Cette méthode crée un enregistrement exemple avec une consigne et sa réponse,
     * associé à une partie donnée, avec un numéro d'ordre.
     *
     * @param int    $numeroExemple  Numéro d'ordre de l'exemple au sein de la partie.
     * @param int    $partie_id      Identifiant de la partie à laquelle appartient l'exemple.
     * @param string $consigne       Texte de la consigne de l'exemple.
     * @param string $reponse        Texte de la réponse à la consigne.
     *
     * @return int|false  Retourne l'ID de l'exemple inséré, ou false en cas d'erreur.
     */
    public function insertExample(int $numeroExemple, int $partie_id, string $consigne, string $reponse)
    {
        try {
            $newExample = $this->db->prepare("INSERT INTO Exemple (numeroexemple, partie_id, consigne, reponse) VALUES (?, ?, ?, ?);");
            $newExample->bindValue(1, $numeroExemple);
            $newExample->bindValue(2, $partie_id);
            $newExample->bindValue(3, $consigne);
            $newExample->bindValue(4, $reponse);

            $reussite = $newExample->execute();

            if ($reussite) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion d'exemple : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les amis d'un utilisateur.
     *
     * Cette méthode retourne une liste de tous les amis connectés à l'utilisateur
     * spécifié, en incluant leur ID et leur nom d'utilisateur.
     *
     * @param int $user_id  Identifiant de l'utilisateur pour lequel récupérer les amis.
     *
     * @return array  Tableau de tableaux associatifs contenant 'ami_id' et 'username' pour chaque ami.
     */
    public function getAmis(int $user_id)
    {
        $amis = $this->db->prepare("SELECT 
                                CASE 
                                WHEN user1_id = ? THEN user2_id
                                ELSE user1_id
                                END AS ami_id , username
                                FROM amis JOIN users ON ami_id = users.id 
                                WHERE ? = user1_id OR ? = user2_id;");
        $amis->bindvalue(1, $user_id);
        $amis->bindvalue(2, $user_id);
        $amis->bindvalue(3, $user_id);

        $amis->execute();

        $result = $amis->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * Insère une association entre un quiz et une catégorie.
     *
     * Cette méthode crée un lien dans la table `categorie_quiz` pour indiquer
     * qu'une catégorie est associée à un quiz spécifique.
     *
     * @param int $quiz_id      Identifiant du quiz.
     * @param int $categorie_id Identifiant de la catégorie à associer.
     *
     * @return int|false  Retourne l'ID de la catégorie insérée, ou false en cas d'erreur.
     */
    public function insertLessonCategorie(int $lesson_id, int $categorie_id)
    {
        try {
            $newQuizCategorie = $this->db->prepare("INSERT INTO categorie_lecon(category_id, lesson_id) VALUES (?, ?);");
            $newQuizCategorie->bindValue(1, $categorie_id);
            $newQuizCategorie->bindValue(2, $lesson_id);

            $reussite = $newQuizCategorie->execute();
            if ($reussite === false) {
                return false;
            } else {
                return $categorie_id;
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion de quiz categorie : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère la liste des catégories disponibles.
     *
     * Cette méthode retourne un tableau associatif contenant les catégories
     * (id et CategorieName) présentes dans la table `categories`.
     *
     * @return array|false  Tableau associatif des catégories, ou false en cas d'erreur.
     */
    public function getAllCategories(): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT DISTINCT id,CategorieName FROM categories;");
            $sql->execute();
            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        } catch (PDOException $e) {
            die("Fetching categories failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    /**
     * Récupère la liste des leçons créées par un utilisateur
     *
     * Cette méthode retourne un tableau associatif contenant les leçons
     * (id, title, date, ...).
     *
     * @return array|false  Tableau associatif des lecons, ou false en cas d'erreur.
     */
    public function getAllInfoLessonsByUser(string|int $id): array|false
    {
        $stmt = $this->db->prepare("
        SELECT
            l.id AS id,
            l.title AS title,
            l.description AS description,
            l.date AS date,
            'leçon' AS genre,

            u.id AS creatorId,
            u.username AS user_name,

            COALESCE(
                (
                    SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                    FROM categorie_lecon cq
                    JOIN categories c ON c.id = cq.category_id
                    WHERE cq.lesson_id = l.id
                ),
                (
                    SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                    FROM categorie_lecon cl
                    JOIN categories c ON c.id = cl.category_id
                    WHERE cl.lesson_id = l.id
                )
            ) AS categories,

            (SELECT COUNT(*) FROM likesLecon l2 WHERE l2.lecon_id = l.id) AS nbjaime,
            (SELECT COUNT(*) FROM dislikesLecon d2 WHERE d2.lecon_id = l.id) AS nbjaimepas

        FROM lecon l
        JOIN users u ON u.id = l.user_id
        WHERE l.user_id = ?
        ORDER BY l.date DESC
    ");

        $stmt->execute([$id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($results)) {
            return false;
        }

        // transformation des catégories en tableau
        foreach ($results as &$row) {
            $row['categories'] = $row['categories']
                ? explode(',', $row['categories'])
                : [];
        }

        return $results;
    }

    /**
     * Récupère le nombre de parties dans une leçon.
     *
     * @param int $idLesson  Identifiant de la leçon.
     * @return int           Nombre de parties dans la leçon.
     */
    public function getLessonSize(int $idLesson)
    {
        try {
            $sql = $this->db->prepare("SELECT COUNT(id) AS taille FROM Partie WHERE lecon_id = ?;");
            $sql->bindValue(1, $idLesson);
            $sql->execute();
            $size = $sql->fetchAll(PDO::FETCH_ASSOC);
            return intval($size[0]['taille']);
        } catch (PDOException $e) {

            die("Fetching categories failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère l'ID de l'utilisateur créateur d'une leçon.
     *
     * @param int $idLesson  Identifiant de la leçon.
     * @return int           Identifiant de l'utilisateur créateur.
     */
    public function getUserIdFromLesson(int $idLesson)
    {
        try {
            $sql = $this->db->prepare("SELECT user_id FROM Lecon WHERE id = ?;");
            $sql->bindValue(1, $idLesson);
            $sql->execute();
            $user = $sql->fetchAll(PDO::FETCH_ASSOC);
            return intval($user[0]['user_id']);
        } catch (PDOException $e) {

            die("Fetching categories failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Met à jour les catégories associées à une leçon.
     *
     * Cette méthode supprime toutes les catégories existantes et en ajoute de nouvelles.
     *
     * @param int   $lesson_id  Identifiant de la leçon.
     * @param array $categories Tableau des IDs de catégories à associer.
     * @return bool             true en cas de succès.
     */
    public function updateCategoriesLesson(int $lesson_id, array $categories)
    {

        try {
            $delete = $this->db->prepare("DELETE FROM categorie_lecon WHERE lesson_id = ?;");
            $delete->bindValue(1, $lesson_id);
            $delete->execute();
            foreach ($categories as $categorie) {
                $this->insertLessonCategorie($lesson_id, (int)$categorie);
            }
            return true;
        } catch (PDOException $e) {
            die("Updating categories for quiz failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("erreur : " . $e->getMessage());
        }
    }

    /**
     * Met à jour la disponibilité et les restrictions d'amis d'une leçon.
     *
     * Change le type de disponibilité (public, ami, privé) et gère les restrictions d'accès
     * pour les amis spécifiés.
     *
     * @param int    $lesson_id      Identifiant de la leçon.
     * @param string $disponibilite  Type de disponibilité ('public', 'ami', 'private').
     * @param array  $amis           Tableau des IDs d'amis autorisés (si dispo='ami').
     * @return bool                  true en cas de succès.
     */
    public function updateDisponibiliteLesson(int $lesson_id, string $disponibilite, array $amis)
    {
        try {
            $delete = $this->db->prepare("DELETE FROM amiDisponibilite WHERE lesson_id = ?;");
            $delete->bindValue(1, $lesson_id);
            $delete->execute();
            $update = $this->db->prepare("UPDATE Lecon SET disponibilite = ? WHERE id = ?;");
            $update->bindValue(1, $disponibilite);
            $update->bindValue(2, $lesson_id);
            $update->execute();
            if ($disponibilite == 'ami') {
                $ownerId = $this->getUserIdFromLesson($lesson_id);
                $amisSelectionnes = $this->normalizeSelectedFriends((int)$ownerId, $amis);
                foreach ($amisSelectionnes as $ami) {
                    $this->insertAmiDispo($lesson_id, (int)$ami);
                }
            }
            return true;
        } catch (PDOException $e) {
            die("Updating disponibilite for quiz failed: " . $e->getMessage());
        }
    }

    private function normalizeSelectedFriends(int $userId, array $amis): array
    {
        if (in_array('tous', $amis, true)) {
            $allFriends = $this->getAmis($userId);
            $ids = array_map(static fn($ami) => (int)$ami['ami_id'], $allFriends);
            return array_values(array_unique(array_filter($ids, static fn($id) => $id > 0)));
        }

        $ids = array_map('intval', $amis);
        return array_values(array_unique(array_filter($ids, static fn($id) => $id > 0)));
    }


    /**
     * Ajoute une nouvelle partie à une leçon.
     *
     * @param int    $idLesson     Identifiant de la leçon.
     * @param int    $numPart      Numéro d'ordre de la nouvelle partie.
     * @param string $partTitle    Titre de la partie.
     * @param string $partContent  Contenu de la partie.
     * @return bool                true en cas de succès.
     */
    public function addPartToLesson(int $idLesson, int $numPart, string $partTitle, string $partContent)
    {
        try {
            $newPartId = $this->insertPart($numPart, $idLesson, $partTitle, $partContent);
            return true;
        } catch (PDOException $e) {
            die("Adding question to quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère tous les parties et exemples d'une leçon.
     *
     * Cette méthode retourne les parties avec tous leurs exemples associés,
     * organisés de manière hiérarchique.
     *
     * @param int $idLesson  Identifiant de la leçon.
     * @return array         Tableau contenant les parties avec leurs exemples.
     */
    public function getPartsExFromLesson(int $idLesson)
    {
        try {
            $parts = $this->db->prepare("SELECT id, title,content AS partContent FROM Partie WHERE lecon_id = ? ORDER BY numeroPartie ASC;");
            $parts->bindValue(1, $idLesson);
            $parts->execute();
            $TAB_PART = $parts->fetchAll(PDO::FETCH_ASSOC);
            foreach ($TAB_PART as $index => $question) {
                $reponses = $this->db->prepare("SELECT id, consigne, reponse FROM Exemple WHERE partie_id = ? ;");
                $reponses->bindValue(1, $question['id']);
                $reponses->execute();
                $TAB_PART[$index]['exemples'] = $reponses->fetchAll(PDO::FETCH_ASSOC);
                $TAB_PART[$index]['nbExemple'] = count($TAB_PART[$index]['exemples']);
            }
            return $TAB_PART;
        } catch (PDOException $e) {
            die("Fetching parts and examples from lesson failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère les informations générales d'une leçon.
     *
     * Inclut le titre, description, disponibilité et quiz associé.
     *
     * @param int $idLesson  Identifiant de la leçon.
     * @return array|false   Tableau associatif des infos, ou false si non trouvé.
     */
    public function getLessonInfos(int $idLesson)
    {
        try {
            $quiz = $this->db->prepare("SELECT title, description, disponibilite, quiz_id FROM Lecon WHERE id = ?;");
            $quiz->bindvalue(1, $idLesson);
            $quiz->execute();
            return $quiz->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Fetching quiz infos failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère les catégories associées à une leçon.
     *
     * @param int $idLesson  Identifiant de la leçon.
     * @return array         Tableau des catégories avec id et nom.
     */
    public function getCategoriesFromLesson(int $idLesson)
    {
        try {
            $categories = $this->db->prepare("SELECT categories.id, categories.categorieName FROM categories 
            INNER JOIN categorie_lecon ON categories.id = categorie_lecon.category_id WHERE categorie_lecon.lesson_id = ?;");
            $categories->bindValue(1, $idLesson);
            $categories->execute();
            return $categories->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Fetching categories from quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère la liste des amis ayant accès à une leçon.
     *
     * @param int $lesson_id  Identifiant de la leçon.
     * @return array          Tableau des IDs d'amis autorisés.
     */
    public function getAmisSelection(int $lesson_id)
    {
        try {
            $amis = $this->db->prepare("SELECT ami_id FROM amiDisponibilite WHERE lesson_id = ?;");
            $amis->bindvalue(1, $lesson_id);
            $amis->execute();
            $result = $amis->fetchAll(PDO::FETCH_ASSOC);
            $TAB_AMIS = array();
            foreach ($result as $ami) {
                $TAB_AMIS[] = $ami['ami_id'];
            }
            return $TAB_AMIS;
        } catch (PDOException $e) {
            die("Fetching selected friends failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour le titre et contenu d'une partie de leçon.
     *
     * @param int    $idLesson     Identifiant de la leçon.
     * @param int    $numPart      Numéro d'ordre de la partie à modifier.
     * @param string $partTitle    Nouveau titre de la partie.
     * @param string $partContent  Nouveau contenu de la partie.
     * @return bool                true en cas de succès.
     */
    public function updatePartLesson(int $idLesson, int $numPart, string $partTitle, string $partContent)
    {
        try {
            $updatePart = $this->db->prepare("UPDATE Partie SET title = ?, content = ? WHERE lecon_id = ? AND numeroPartie = ? ;");
            $updatePart->bindValue(1, $partTitle);
            $updatePart->bindValue(2, $partContent);
            $updatePart->bindValue(3, (int)$idLesson);
            $updatePart->bindValue(4, (int)$numPart);
            $updatePart->execute();

            if ($updatePart->rowCount() === 0) {
                throw new PDOException("aucune ligne modifiées");
            }

            return true;
        } catch (PDOException $e) {
            die("Updating question in quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour le titre et la description d'une leçon.
     *
     * @param int    $idLesson     Identifiant de la leçon.
     * @param string $title        Nouveau titre de la leçon.
     * @param string $description  Nouvelle description de la leçon.
     * @return bool                true en cas de succès.
     */
    public function updateResumLesson(int $idLesson, string $title, string $description)
    {
        try {
            $updateResum = $this->db->prepare("UPDATE lecon SET title = ?, description = ? WHERE id = ?;");
            $updateResum->bindValue(1, $title);
            $updateResum->bindValue(2, $description);
            $updateResum->bindValue(3, $idLesson);
            $updateResum->execute();
            return true;
        } catch (PDOException $e) {
            die("Updating lesson resum failed: " . $e->getMessage());
        }
    }

    /**
     * Supprime une partie d'une leçon.
     *
     * @param int $idLesson  Identifiant de la leçon.
     * @param int $numPart   Numéro d'ordre de la partie à supprimer.
     * @return bool          true en cas de succès.
     */
    public function deletePartFromLesson(int $idLesson, int $numPart)
    {
        try {
            $getQuestion = $this->db->prepare("SELECT id FROM Partie WHERE lecon_id = ? AND numeroPartie = ?;");
            $getQuestion->bindValue(1, $idLesson);
            $getQuestion->bindValue(2, $numPart);
            $getQuestion->execute();
            $question = $getQuestion->fetch(PDO::FETCH_ASSOC);
            if ($question) {
                $deleteQuestion = $this->db->prepare("DELETE FROM Partie WHERE id = ?;");
                $deleteQuestion->bindValue(1, $question['id']);
                $deleteQuestion->execute();
            }
            return true;
        } catch (PDOException $e) {
            die("Deleting question from quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour le quiz associé à une leçon.
     *
     * @param int         $idLesson  Identifiant de la leçon.
     * @param int|string  $quiz      Identifiant du quiz, ou 'Aucun' pour supprimer l'association.
     * @return bool                  true en cas de succès.
     */
    public function updateQuizAssociated(int $idLesson, int|string $quiz)
    {
        try {
            if ($quiz === "Aucun") {
                $updateQuiz = $this->db->prepare("UPDATE Lecon SET quiz_id = NULL WHERE id = ?");
                $updateQuiz->bindValue(1, $idLesson);
            } else {
                $updateQuiz = $this->db->prepare("UPDATE Lecon SET quiz_id = ? WHERE id = ?");
                $updateQuiz->bindValue(1, (int)$quiz);
                $updateQuiz->bindValue(2, $idLesson);
            }
            $updateQuiz->execute();
            return true;
        } catch (PDOException $e) {
            die("updating quiz_id from lecon failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère le nombre d'exemples dans une partie de leçon.
     *
     * @param int $idLesson     Identifiant de la leçon.
     * @param int $numeroPartie Numéro d'ordre de la partie.
     * @return int              Nombre d'exemples dans la partie.
     */
    public function getNumberExFromPart(int $idLesson, int $numeroPartie)
    {
        try {
            $getNumberLesson = $this->db->prepare(
                "SELECT COUNT(Exemple.id) AS nombre FROM Partie JOIN Exemple ON Partie.id = Exemple.partie_id
                WHERE Partie.lecon_id = ? AND Partie.numeroPartie = ?"
            );
            $getNumberLesson->bindValue(1, (int)$idLesson);
            $getNumberLesson->bindValue(2, (int)$numeroPartie);
            $getNumberLesson->execute();
            $result = $getNumberLesson->fetchColumn();
            return (int)$result;
        } catch (PDOException $e) {
            die("selecting number of exemples from partie failed: " . $e->getMessage());
        }
    }

    /**
     * Ajoute un nouvel exemple à une partie de leçon.
     *
     * @param int    $idLesson  Identifiant de la leçon.
     * @param int    $iPart     Numéro d'ordre de la partie.
     * @param int    $kEx       Numéro d'ordre du nouvel exemple.
     * @param string $consigne  Texte de la consigne.
     * @param string $reponse   Texte de la réponse.
     * @return bool             true en cas de succès.
     */
    public function addExToPart(int $idLesson, int $iPart, int  $kEx, string $consigne, string $reponse)
    {
        try {
            $part = $this->db->prepare("SELECT id FROM Partie WHERE lecon_id = ? AND numeroPartie = ?");
            $part->bindValue(1, $idLesson);
            $part->bindValue(2, $iPart);
            $part->execute();
            $resultPart = $part->fetchAll(PDO::FETCH_ASSOC);
            $reussite = $this->insertExample($kEx, $resultPart[0]['id'], $consigne, $reponse);
            if (!$reussite) {
                throw new PDOException("erreur d'insertion d'exemple");
            } else {
                return true;
            }
        } catch (PDOException $e) {
            die("inserting exemple in partie failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour un exemple dans une partie de leçon.
     *
     * @param int    $idLesson  Identifiant de la leçon.
     * @param int    $iPart     Numéro d'ordre de la partie.
     * @param int    $kEx       Numéro d'ordre de l'exemple à modifier.
     * @param string $consigne  Nouvelle consigne.
     * @param string $reponse   Nouvelle réponse.
     * @return bool             true en cas de succès.
     */
    public function updateExFromPart(int $idLesson, int $iPart, int  $kEx, string $consigne, string $reponse)
    {
        try {
            $part = $this->db->prepare("SELECT id FROM Partie WHERE lecon_id = ? AND numeroPartie = ?");
            $part->bindValue(1, $idLesson);
            $part->bindValue(2, $iPart);
            $part->execute();
            $resultPart = $part->fetchColumn();

            $updateExemple = $this->db->prepare("UPDATE Exemple SET consigne = ?, reponse = ? WHERE partie_id = ? AND numeroExemple = ?");
            $updateExemple->bindValue(1, $consigne);
            $updateExemple->bindValue(2, $reponse);
            $updateExemple->bindValue(3, $resultPart);
            $updateExemple->bindValue(4, $kEx);
            $updateExemple->execute();

            return true;
        } catch (PDOException $e) {
            die("inserting exemple in partie failed: " . $e->getMessage());
        }
    }

    /**
     * Supprime un exemple d'une partie de leçon.
     *
     * @param int $idLesson  Identifiant de la leçon.
     * @param int $iPart     Numéro d'ordre de la partie.
     * @param int $kEx       Numéro d'ordre de l'exemple à supprimer.
     * @return bool          true en cas de succès.
     */
    public function deleteExFromPart(int $idLesson, int $iPart, int $kEx)
    {
        try {
            $part = $this->db->prepare("SELECT id FROM Partie WHERE lecon_id = ? AND numeroPartie = ?");
            $part->bindValue(1, $idLesson);
            $part->bindValue(2, $iPart);
            $part->execute();
            $resultPart = $part->fetchColumn();
            if ($resultPart === false) {
                throw new RuntimeException("Part not found");
            }
            $deleteEx = $this->db->prepare("DELETE FROM Exemple WHERE partie_id = ? AND numeroExemple = ?");
            $deleteEx->bindValue(1, $resultPart);
            $deleteEx->bindValue(2, $kEx);
            $deleteEx->execute();
            if ($deleteEx->rowCount() !== 1) {
                throw new PDOException("Example not found");
            }


            return true;
        } catch (PDOException $e) {
            die("deleting exemple in partie failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour la disponibilité d'une leçon (version simplifiée)
     */
    public function updateDisponibilite(int $lesson_id, string $disponibilite): bool
    {
        try {
            $update = $this->db->prepare("UPDATE Lecon SET disponibilite = ? WHERE id = ?");
            $update->bindParam(1, $disponibilite);
            $update->bindParam(2, $lesson_id, PDO::PARAM_INT);
            $update->execute();

            return $update->rowCount() > 0;
        } catch (PDOException $e) {
            die("Updating disponibilite failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}


