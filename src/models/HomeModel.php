<?php
class HomeModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    private function getAll(): array
    {
        $stmt = $this->db->query("SELECT * FROM quiz");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            ORDER BY nbjaime DESC;
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
     * Récupère toutes les informations des quiz avec l'utilisateur associé par date décroissante
     *
     * Inclut : genre, date, nom d'utilisateur, titre, difficulté, description.
     *
     * @return array Tableau associatif de quiz enrichis avec info utilisateur
     */
    public function getAllNewCreations(): array
    {
        $stmt = $this->db->query("
            SELECT 
                q.id,
                q.genre, 
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
            ORDER BY q.date DESC;
        ");

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // transformation des catégories en tableau
        foreach ($results as &$row) {
            $row['categories'] = explode(',', $row['categories']);
        }

        return $results;
    }

    // public function tmp()
    // {
    //     $stmt = $this->db->query("
    //     CREATE TABLE user_streaks (
    //                                 user_id INT PRIMARY KEY,
    //                                 current_streak INT NOT NULL DEFAULT 0,
    //                                 longest_streak INT NOT NULL DEFAULT 0,
    //                                 last_activity_date DATE 
    //                                 ) 
    //     ");
    // }

    public function createInstance(int|string $id): bool
    {
        // $today = (new DateTime())->format('Y-m-d');
        $stmt = $this->db->prepare("
        INSERT INTO user_streaks (user_id, current_streak, longest_streak, last_activity_date)
        VALUES (?, 0, 0, ?)
        ");
        return $stmt->execute([$id, NULL]);
    }

    public function checkIfNotInstance(int|string $id): bool
    {
        $stmt = $this->db->prepare("
        SELECT user_id FROM user_streaks WHERE user_id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetchColumn();
        return empty($result) ? true : false;
    }


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


    public function getCurrentStreak(int|string $id)
    {
        $stmt = $this->db->prepare("
        SELECT current_streak
        FROM user_streaks
        WHERE user_id = ?
    ");

        $stmt->execute([$id]);
        $valueStreak = $stmt->fetchColumn();

        return intval($valueStreak);
    }

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

    public function setCurrentStreak(int|string $id, int $value): void
    {
        $stmt = $this->db->prepare("
        UPDATE user_streaks 
        SET current_streak = ?
        WHERE user_id = ?
    ");
        $stmt->execute([$value, $id]);
    }

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
