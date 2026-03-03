<?php
class HomeModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère un quiz par son ID
     *
     * @param int $id Identifiant du quiz
     * @return array|false Tableau associatif du quiz, ou false si non trouvé
     */
    public function getById($id): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM quiz WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les informations des quiz avec l'utilisateur associé
     *
     * Inclut : genre, date, nom d'utilisateur, titre, difficulté, description.
     *
     * @return array Tableau associatif de quiz enrichis avec info utilisateur
     */
    public function getAllInfo(): array
    {
        $stmt = $this->db->query("
            SELECT 
                q.id,
                q.genre, 
                q.date, 
                u.username AS user_name,
                u.id AS creatorId,
                q.disponibilite,
                (
                    SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                    FROM categorie_quiz cq
                    JOIN categories c ON c.id = cq.category_id
                    WHERE cq.quiz_id = q.id
                ) AS categories,
                q.title, 
                q.difficulty, 
                q.description,
                (SELECT COUNT(*) FROM likes l WHERE l.quiz_id = q.id) AS nbjaime,
                (SELECT COUNT(*) FROM dislikes d WHERE d.quiz_id = q.id) AS nbjaimepas
            FROM quiz q
            JOIN users u ON u.id = q.user_id
            WHERE q.disponibilite = 'public'
            ORDER BY nbjaime DESC, (nbjaime - nbjaimepas) DESC;
        ");

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // transformation des catégories en tableau
        foreach ($results as &$row) {
            $row['categories'] = explode(',', $row['categories']);
        }

        return $results;
    }


    /**
     * Récupère toutes les informations des quiz avec l'utilisateur associé
     *
     * Inclut : genre, date, nom d'utilisateur, titre, difficulté, description.
     *
     * @return array Tableau associatif de quiz enrichis avec info utilisateur
     */
    public function getAllCreationsByUser(string|int $id): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                q.id,
                q.genre AS genre, 
                q.date, 
                u.username AS user_name,
                u.id AS creatorId, 
                (
                    SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                    FROM categorie_quiz cq
                    JOIN categories c ON c.id = cq.category_id
                    WHERE cq.quiz_id = q.id
                ) AS categories,
                q.title, 
                q.difficulty, 
                q.description,
                (SELECT COUNT(*) FROM likes l WHERE l.quiz_id = q.id) AS nbjaime,
                (SELECT COUNT(*) FROM dislikes d WHERE d.quiz_id = q.id) AS nbjaimepas
            FROM quiz q
            JOIN users u ON u.id = q.user_id
            WHERE u.id = :id
            ORDER BY nbjaime - nbjaimepas DESC
        ");

        $stmt->execute(['id' => $id]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // transformation des catégories en tableau
        foreach ($results as &$row) {
            $row['categories'] = explode(',', $row['categories']);
        }


        return $results;
    }

    /**
     * Récupère tous les quiz créés par les amis d'un utilisateur.
     *
     * Cette méthode sélectionne uniquement les quiz dont la disponibilité est définie à "ami"
     * et qui appartiennent aux amis de l'utilisateur donné. Les quiz de l'utilisateur lui-même
     * sont exclus. Les catégories associées à chaque quiz sont récupérées et transformées en tableau.
     *
     * @param int $userId Identifiant de l'utilisateur pour lequel récupérer les créations de ses amis.
     *
     * @return array<mixed> Tableau de quiz
     *
     */
    public function getAllCreationsByFriends(int $userId): array
    {
        $sql = "
            SELECT 
                q.id,
                'quiz' AS genre,
                q.user_id AS creatorId,
                q.date,
                u.username AS user_name,
                (
                    SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                    FROM categorie_quiz cq
                    JOIN categories c ON c.id = cq.category_id
                    WHERE cq.quiz_id = q.id
                ) AS categories,
                q.title,
                q.difficulty,
                q.description,
                (SELECT COUNT(*) FROM likes l WHERE l.quiz_id = q.id) AS nbjaime,
                (SELECT COUNT(*) FROM dislikes d WHERE d.quiz_id = q.id) AS nbjaimepas
            FROM quiz q
            JOIN users u ON u.id = q.user_id
            LEFT JOIN amiDisponibilite ad ON ad.quiz_id = q.id
            WHERE q.user_id != :me
            AND EXISTS (
                SELECT 1 FROM amis a
                WHERE (a.user1_id = :me AND a.user2_id = q.user_id)
                OR (a.user2_id = :me AND a.user1_id = q.user_id)
            )
            AND (
                q.disponibilite = 'public'
                OR (q.disponibilite = 'ami' AND (ad.ami_id = :me OR ad.ami_id = 0))
            )

            UNION ALL

            -- Leçons de tes amis
            SELECT 
                l.id,
                'leçon' AS genre,
                l.user_id AS creatorId,
                l.date,
                u.username AS user_name,
                (
                    SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                    FROM categorie_lecon cq
                    JOIN categories c ON c.id = cq.category_id
                    WHERE cq.lesson_id = l.id
                ) AS categories,
                l.title,
                NULL AS difficulty,
                l.description,
                (SELECT COUNT(*) FROM likeslecon li WHERE li.lecon_id = l.id) AS nbjaime,
                (SELECT COUNT(*) FROM dislikeslecon d WHERE d.lecon_id = l.id) AS nbjaimepas
            FROM lecon l
            JOIN users u ON u.id = l.user_id
            LEFT JOIN amiDisponibilite ad ON ad.lesson_id = l.id
            WHERE l.user_id != :me
            AND EXISTS (
                SELECT 1 FROM amis a
                WHERE (a.user1_id = :me AND a.user2_id = l.user_id)
                OR (a.user2_id = :me AND a.user1_id = l.user_id)
            )
            AND (
                l.disponibilite = 'public' 
                OR l.disponibilite IS NULL
                OR (l.disponibilite = 'ami' AND (ad.ami_id = :me OR ad.ami_id = 0))
            )
            ORDER BY nbjaime DESC;
";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['me' => $userId]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // transformer les catégories en tableau
        foreach ($results as &$row) {
            $row['categories'] = $row['categories']
                ? explode(',', $row['categories'])
                : [];
        }
        return $results;
    }



    /**
     * Récupère toutes les informations des quiz avec l'utilisateur associé par date décroissante
     *
     * Inclut : genre, date, nom d'utilisateur, titre, difficulté, description.
     *
     * @return array Tableau associatif de quiz enrichis avec info utilisateur
     */
    public function getAllNewCreations(): array
    {
        $stmt = $this->db->query("
                SELECT * FROM (

                    SELECT 
                        q.id,
                        q.genre,
                        q.date,
                        u.username AS user_name,
                        u.id AS creatorId,
                        (
                            SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                            FROM categorie_quiz cq
                            JOIN categories c ON c.id = cq.category_id
                            WHERE cq.quiz_id = q.id
                        ) AS categories,
                        q.title,
                        q.difficulty,
                        q.description,
                        (SELECT COUNT(*) FROM likes l WHERE l.quiz_id = q.id) AS nbjaime,
                        (SELECT COUNT(*) FROM dislikes d WHERE d.quiz_id = q.id) AS nbjaimepas,
                        (
                            (SELECT COUNT(*) FROM likes l WHERE l.quiz_id = q.id) -
                            (SELECT COUNT(*) FROM dislikes d WHERE d.quiz_id = q.id)
                        ) AS score

                    FROM quiz q
                    JOIN users u ON u.id = q.user_id
                    WHERE q.disponibilite = 'public'

                    UNION ALL

                    SELECT 
                        l.id,
                        'lecon' AS genre,
                        l.date,
                        u.username AS user_name,
                        u.id AS creatorId,
                        (
                            SELECT GROUP_CONCAT(DISTINCT c.categorieName)
                            FROM categorie_lecon cl
                            JOIN categories c ON c.id = cl.category_id
                            WHERE cl.lesson_id = l.id
                        ) AS categories,
                        l.title,
                        NULL AS difficulty,
                        l.description,
                        (SELECT COUNT(*) FROM likesLecon ll WHERE ll.like_id = l.id) AS nbjaime,
                        (SELECT COUNT(*) FROM dislikesLecon dl WHERE dl.dislike_id = l.id) AS nbjaimepas,
                        (
                            (SELECT COUNT(*) FROM likesLecon ll WHERE ll.like_id = l.id) -
                            (SELECT COUNT(*) FROM dislikesLecon dl WHERE dl.dislike_id = l.id)
                        ) AS score

                    FROM lecon l
                    JOIN users u ON u.id = l.user_id

                )

                ORDER BY date DESC, score DESC;
            ");
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // transformation des catégories en tableau
        foreach ($results as &$row) {
            $row['categories'] = $row['categories']
                ? explode(',', $row['categories'])
                : [];
        }

        return $results;
    }

    /**
     * Crée une nouvelle instance de suivi pour un utilisateur dans `user_streaks`.
     * Initialise `current_streak` et `longest_streak` à 0 et `last_activity_date` à hier.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     * @return bool True si l'insertion a réussi, false sinon.
     */
    public function createInstance(int|string $id): bool
    {
        $yesterday = (new DateTime('yesterday'))->format('Y-m-d');
        $stmt = $this->db->prepare("
        INSERT INTO user_streaks (user_id, current_streak, longest_streak, last_activity_date)
        VALUES (?, 0, 0, ?)
        ");
        return $stmt->execute([$id, $yesterday]);
    }

    /**
     * Vérifie si une instance de suivi n'existe pas encore pour un utilisateur.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     * @return bool True si aucune instance n'existe, false si une instance existe déjà.
     */
    public function checkIfNotInstance(int|string $id): bool
    {
        $stmt = $this->db->prepare("
        SELECT user_id FROM user_streaks WHERE user_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetchColumn();
        return empty($result) ? true : false;
    }



    /**
     * Vérifie si la date de dernière activité de l'utilisateur est NULL.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     * @return bool True si `last_activity_date` est NULL, false sinon.
     */
    public function checkDateIsNull(int|string $id): bool
    {
        $stmt = $this->db->prepare("
        SELECT last_activity_date
        FROM user_streaks
        WHERE user_id = ?
        ");

        $stmt->execute([$id]);
        return $stmt->fetchColumn() === null;
    }

    /**
     * Récupère la date du dernier quiz joué par l'utilisateur.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     * @return DateTime|null La date du dernier quiz joué, ou null si aucun quiz.
     */
    public function getLastDateQuizPlayed(int|string $id): ?DateTime
    {
        $stmt = $this->db->prepare("
        SELECT dateRealisation
        FROM resultat
        WHERE user_id = ?
        ORDER BY dateRealisation DESC
        LIMIT 1
    ");

        $stmt->execute([$id]);
        $date = $stmt->fetchColumn();

        return $date ? new DateTime($date) : null;
    }

    /**
     * Récupère la date de la dernière activité de l'utilisateur dans `user_streaks`.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     * @return DateTime|null Date de la dernière activité, ou null si non définie.
     */
    public function getLastActivity(int|string $id): ?DateTime
    {
        $stmt = $this->db->prepare("
        SELECT last_activity_date
        FROM user_streaks
        WHERE user_id = ?
    ");

        $stmt->execute([$id]);
        $date = $stmt->fetchColumn();

        return $date ? new DateTime($date) : null;
    }

    /**
     * Récupère la valeur actuelle de la série (streak) de l'utilisateur.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     * @return int Valeur du streak actuel (0 si non défini).
     */
    public function getCurrentStreak(int|string $id): int
    {
        $stmt = $this->db->prepare("
        SELECT current_streak
        FROM user_streaks
        WHERE user_id = ?
    ");

        $stmt->execute([$id]);
        $valueStreak = $stmt->fetchColumn();

        return intval($valueStreak ?? 0);
    }

    /**
     * Récupère la plus longue série (streak) enregistrée pour l'utilisateur.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     * @return int Valeur de la plus longue série.
     */
    public function getLongestStreak(int|string $id): int
    {
        $stmt = $this->db->prepare("
        SELECT longest_streak
        FROM user_streaks
        WHERE user_id = ?
    ");

        $stmt->execute([$id]);
        $valueStreak = $stmt->fetchColumn();

        return intval($valueStreak);
    }


    /**
     * Incrémente la série actuelle (`current_streak`) de l'utilisateur de 1.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     */
    public function incrementStreak(int|string $id): void
    {
        $stmt = $this->db->prepare("
        SELECT current_streak  
        FROM user_streaks
        WHERE user_id = ?
        ");

        $stmt->execute([$id]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("
        UPDATE user_streaks 
        SET current_streak = current_streak + 1
        WHERE user_id = ?
    ");
        $test = $stmt->execute([$id]);
    }

    /**
     * Définit la série actuelle (`current_streak`) de l'utilisateur à une valeur donnée.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     * @param int $value Nouvelle valeur pour `current_streak`.
     */
    public function setCurrentStreak(int|string $id, int $value): void
    {
        $stmt = $this->db->prepare("
        UPDATE user_streaks 
        SET current_streak = ?
        WHERE user_id = ?
    ");
        $stmt->execute([$value, $id]);
    }

    /**
     * Met à jour la plus longue série (`longest_streak`) si la série actuelle dépasse la précédente plus longue.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     * @param int $current Valeur actuelle du streak.
     */
    public function updateLongestIfNeeded(int|string $id, int $current): void
    {
        $longest = $this->getLongestStreak($id);
        if ($current > $longest) {
            $stmt = $this->db->prepare("
            UPDATE user_streaks 
            SET longest_streak = ?
            WHERE user_id = ?
        ");
            $stmt->execute([$current, $id]);
        }
    }


    /**
     * Met à jour la date de la dernière activité de l'utilisateur.
     *
     * @param int|string $id Identifiant de l'utilisateur.
     * @param DateTime $date Date à enregistrer comme dernière activité.
     * @return bool True si la mise à jour a réussi, false sinon.
     */
    public function updateLastActivity(int|string $id, DateTime $date): bool
    {
        $formattedDate = $date->format('Y-m-d');
        $stmt = $this->db->prepare("
        UPDATE user_streaks
        SET last_activity_date = ?
        WHERE user_id = ?
        ");
        return $stmt->execute([$formattedDate, $id]);
    }
}
