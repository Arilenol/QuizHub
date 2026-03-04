<?php

class CRUDModel
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère toutes les catégories existantes.
     *
     * @return array<mixed> Tableau associatif contenant les catégories avec les clés :
     *                      - 'id' : int, identifiant de la catégorie
     *                      - 'CategorieName' : string, nom de la catégorie
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function getCategories(): mixed
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
     * Recherche les quiz selon catégorie, contenu, auteur et genre, avec option de tri.
     *
     * @param int $recherche_cat Identifiant de la catégorie
     * @param string $recherche_contenu Texte à rechercher dans le titre ou la description
     * @param string $recherche Nom de l'auteur à rechercher
     * @param string $genre Genre du quiz (optionnel, par défaut '')
     * @param string|null $tris Type de tri (optionnel, voir allowedOrder)
     *
     * @return array<mixed> Liste de quiz correspondant aux critères avec les clés :
     *                      - 'id', 'title', 'description', 'difficulty', 'user_id', 'date', 'genre',
     *                        'nbjaime', 'nbjaimepas'
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function searchQuizByAll(int $recherche_cat, string $recherche_contenu, string $recherche, string $genre = '', string|null $tris = null): mixed
    {
        try {
            $baseSql = "SELECT DISTINCT quiz.id, title, quiz.description, difficulty, quiz.user_id, date, genre,
            COALESCE(l.nbjaime, 0) as nbjaime, COALESCE(d.nbjaimepas, 0) as nbjaimepas FROM quiz 
            INNER JOIN categorie_quiz ON categorie_quiz.quiz_id = quiz.id 
            INNER JOIN categories ON categories.id = categorie_quiz.category_id 
            INNER JOIN users ON users.id = quiz.user_id
            LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaime FROM likes GROUP BY quiz_id) l ON l.quiz_id = quiz.id
            LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaimepas FROM dislikes GROUP BY quiz_id) d ON d.quiz_id = quiz.id
            WHERE categories.id = ? AND (quiz.title LIKE ? OR quiz.description LIKE ?) AND users.username LIKE ? AND (? = '' OR quiz.genre = ?)";


            $allowedOrder = [
                'date_desc' => 'quiz.date DESC',
                'date_asc' => 'quiz.date ASC',
                'title_asc' => 'quiz.title ASC',
                'title_desc' => 'quiz.title DESC',
                'difficulty_asc' => 'quiz.difficulty ASC',
                'difficulty_desc' => 'quiz.difficulty DESC',
                'author_asc' => 'users.username ASC',
                'author_desc' => 'users.username DESC',
                'genre_asc' => 'quiz.genre ASC',
                'genre_desc' => 'quiz.genre DESC'
            ];

            if ($tris && isset($allowedOrder[$tris])) {
                $baseSql .= ' ORDER BY ' . $allowedOrder[$tris];
            }

            $sql = $this->db->prepare($baseSql . ';');
            $sql->bindValue(1, $recherche_cat);
            $sql->bindValue(2, '%' . $recherche_contenu . '%');
            $sql->bindValue(3, '%' . $recherche_contenu . '%');
            $sql->bindValue(4, '%' . $recherche . '%');
            $sql->bindValue(5, $genre);
            $sql->bindValue(6, $genre);
            $sql->execute();

            $quiz = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $quiz;
        } catch (PDOException $e) {
            die("Searching categories failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    /**
     * Recherche les quiz par titre et genre, avec option de tri.
     *
     * @param string $recherche_titre Texte à rechercher dans le titre
     * @param string $genre Genre du quiz (optionnel, par défaut '')
     * @param string|null $tris Type de tri (optionnel)
     *
     * @return array<mixed> Liste de quiz correspondant aux critères
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function searchQuizByTitle(string $recherche_titre, string $genre = '', string|null $tris = null): mixed
    {
        try {
            $baseSql = "SELECT DISTINCT quiz.id, title, quiz.description, difficulty, quiz.user_id, date, genre,
            COALESCE(l.nbjaime, 0) as nbjaime, COALESCE(d.nbjaimepas, 0) as nbjaimepas FROM quiz 
            INNER JOIN categorie_quiz ON categorie_quiz.quiz_id = quiz.id 
            INNER JOIN categories ON categories.id = categorie_quiz.category_id 
            INNER JOIN users ON users.id = quiz.user_id
            LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaime FROM likes GROUP BY quiz_id) l ON l.quiz_id = quiz.id
            LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaimepas FROM dislikes GROUP BY quiz_id) d ON d.quiz_id = quiz.id
            WHERE quiz.title LIKE ? AND (? = '' OR quiz.genre = ?)";

            $allowedOrder = [
                'date_desc' => 'quiz.date DESC',
                'date_asc' => 'quiz.date ASC',
                'title_asc' => 'quiz.title ASC',
                'title_desc' => 'quiz.title DESC',
                'difficulty_asc' => 'quiz.difficulty ASC',
                'difficulty_desc' => 'quiz.difficulty DESC',
                'author_asc' => 'users.username ASC',
                'author_desc' => 'users.username DESC',
                'genre_asc' => 'quiz.genre ASC',
                'genre_desc' => 'quiz.genre DESC'
            ];

            if ($tris && isset($allowedOrder[$tris])) {
                $baseSql .= ' ORDER BY ' . $allowedOrder[$tris];
            }

            $sql = $this->db->prepare($baseSql . ';');
            $sql->bindValue(1, '%' . $recherche_titre . '%');
            $sql->bindValue(2, $genre);
            $sql->bindValue(3, $genre);
            $sql->execute();

            $quiz = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $quiz;
        } catch (PDOException $e) {
            die("Searching by title failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère le nom d'utilisateur d'un auteur à partir de son ID.
     *
     * @param int $user_id Identifiant de l'utilisateur
     *
     * @return string Nom d'utilisateur
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function getNomAuteur(int $user_id): string
    {
        try {
            $sql = $this->db->prepare("SELECT username FROM users WHERE id = ?;");
            $sql->bindParam(1, $user_id);
            $sql->execute();

            $auteur = $sql->fetch(PDO::FETCH_ASSOC);
            return $auteur['username'];
        } catch (PDOException $e) {
            die("Fetching author name failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Recherche les quiz par contenu et auteur, avec genre et tri optionnels.
     *
     * @param string $recherche_contenu Texte à rechercher dans le titre ou la description
     * @param string $recherche Nom de l'auteur à rechercher
     * @param string $genre Genre du quiz (optionnel)
     * @param string|null $tris Type de tri (optionnel)
     *
     * @return array<mixed> Liste de quiz correspondant aux critères
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function searchQuizByContentAndAuthor(string $recherche_contenu, string $recherche, string $genre = '', string|null $tris = null): mixed
    {
        try {
            $baseSql = "SELECT DISTINCT quiz.id, title, quiz.description, difficulty, quiz.user_id, date, genre,
            COALESCE(l.nbjaime, 0) as nbjaime, COALESCE(d.nbjaimepas, 0) as nbjaimepas FROM quiz 
            INNER JOIN categorie_quiz ON categorie_quiz.quiz_id = quiz.id 
            INNER JOIN categories ON categories.id = categorie_quiz.category_id 
            INNER JOIN users ON users.id = quiz.user_id
            LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaime FROM likes GROUP BY quiz_id) l ON l.quiz_id = quiz.id
            LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaimepas FROM dislikes GROUP BY quiz_id) d ON d.quiz_id = quiz.id
            WHERE (quiz.title LIKE ? OR quiz.description LIKE ?) AND users.username LIKE ? AND (? = '' OR quiz.genre = ?)";

            $allowedOrder = [
                'date_desc' => 'quiz.date DESC',
                'date_asc' => 'quiz.date ASC',
                'title_asc' => 'quiz.title ASC',
                'title_desc' => 'quiz.title DESC',
                'difficulty_asc' => 'quiz.difficulty ASC',
                'difficulty_desc' => 'quiz.difficulty DESC',
                'author_asc' => 'users.username ASC',
                'author_desc' => 'users.username DESC',
                'genre_asc' => 'quiz.genre ASC',
                'genre_desc' => 'quiz.genre DESC'
            ];
            if ($tris && isset($allowedOrder[$tris])) {
                $baseSql .= ' ORDER BY ' . $allowedOrder[$tris];
            }

            $sql = $this->db->prepare($baseSql . ';');
            $sql->bindValue(1, '%' . $recherche_contenu . '%');
            $sql->bindValue(2, '%' . $recherche_contenu . '%');
            $sql->bindValue(3, '%' . $recherche . '%');
            $sql->bindValue(4, $genre);
            $sql->bindValue(5, $genre);
            $sql->execute();

            $quiz = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $quiz;
        } catch (PDOException $e) {
            die("Searching categories failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère les catégories associées à un quiz donné.
     *
     * @param int $quiz_id Identifiant du quiz
     *
     * @return array<mixed> Tableau associatif avec les clés :
     *                      - 'id' : identifiant de la catégorie
     *                      - 'categorieName' : nom de la catégorie
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function getCategoriesFromQuiz(int $quiz_id): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT DISTINCT categories.id, categories.categorieName FROM categories 
            INNER JOIN categorie_quiz ON categorie_quiz.category_id = categories.id 
            WHERE categorie_quiz.quiz_id = ?;");
            $sql->bindParam(1, $quiz_id);
            $sql->execute();

            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        } catch (PDOException $e) {
            die("Fetching categories from quiz failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Recherche des auteurs par nom avec limite optionnelle.
     *
     * @param string $search Chaîne à rechercher dans le nom
     * @param int $limit Limite du nombre de résultats (par défaut 50)
     *
     * @return array<mixed> Liste d'auteurs ['id', 'username']
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function searchAuthors(string $search, int $limit = 50): array
    {
        try {
            $sql = $this->db->prepare("SELECT id, username FROM users WHERE username LIKE ? ORDER BY username ASC LIMIT ?");
            $sql->bindValue(1, '%' . $search . '%');
            $sql->bindValue(2, (int)$limit, PDO::PARAM_INT);
            $sql->execute();

            $authors = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $authors;
        } catch (PDOException $e) {
            die("Searching authors failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    /**
     * Récupère toutes les informations d'un auteur, sauf son mot de passe.
     *
     * @param int $author_id Identifiant de l'auteur
     *
     * @return array<mixed>|false Tableau associatif avec les clés : 'id', 'username', 'email', 'description', 'admin'
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function getAuthorInfo(int $author_id): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT id, username, email, description, admin FROM users WHERE id = ?");
            $sql->bindParam(1, $author_id);
            $sql->execute();

            $author = $sql->fetch(PDO::FETCH_ASSOC);
            return $author;
        } catch (PDOException $e) {
            die("Fetching author info failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    /**
     * Récupère tous les quiz créés par un auteur spécifique.
     *
     * @param int $author_id Identifiant de l'auteur
     *
     * @return array<mixed> Liste de quiz avec les clés : 'id', 'title', 'description', 'difficulty', 'user_id', 'date', 'genre', 'nbjaime', 'nbjaimepas'
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function getQuizzesByAuthor(int $author_id): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT quiz.id, quiz.title, quiz.description, quiz.difficulty, 
                                              quiz.user_id, quiz.date, quiz.genre, COALESCE(l.nbjaime, 0) as nbjaime, COALESCE(d.nbjaimepas, 0) as nbjaimepas 
                                       FROM quiz 
                                       LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaime FROM likes GROUP BY quiz_id) l ON l.quiz_id = quiz.id
                                       LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaimepas FROM dislikes GROUP BY quiz_id) d ON d.quiz_id = quiz.id
                                       WHERE quiz.user_id = ? 
                                       ORDER BY quiz.date DESC");
            $sql->bindParam(1, $author_id);
            $sql->execute();

            $quizzes = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $quizzes;
        } catch (PDOException $e) {
            die("Fetching author's quizzes failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    /**
     * Met à jour les informations d'un auteur.
     *
     * @param int $author_id Identifiant de l'auteur
     * @param string $username Nouveau nom d'utilisateur
     * @param string $email Nouvelle adresse email
     * @param string $description Nouvelle description
     *
     * @return bool True si la mise à jour a été effectuée
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function updateAuthor(int $author_id, string $username, string $email, string $description): bool
    {
        try {
            $sql = $this->db->prepare("UPDATE users SET username = ?, email = ?, description = ? WHERE id = ?");
            $sql->bindParam(1, $username);
            $sql->bindParam(2, $email);
            $sql->bindParam(3, $description);
            $sql->bindParam(4, $author_id);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Updating author failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Supprime un auteur.
     *
     * @param int $author_id Identifiant de l'auteur
     *
     * @return bool True si la suppression a été effectuée
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function deleteAuthor(int $author_id): bool
    {
        // Vérouiller l'admin
        $sql = $this->db->prepare("SELECT id FROM users WHERE id = ? and admin = 1");
        $sql->execute([$author_id]);
        $user = $sql->fetch(PDO::FETCH_ASSOC);
        if (!empty($user)) {
            return false;
        }
        try {
            $sql = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $sql->bindParam(1, $author_id);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Deleting author failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Recherche les leçons par titre, avec option de tri.
     *
     * @param string $recherche_titre Texte à rechercher dans le titre
     * @param string|null $tris Type de tri (optionnel)
     *
     * @return array<mixed> Liste de leçons correspondantes
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function searchLessonByTitle(string $recherche_titre, string|null $tris = null): mixed
    {
        try {
            $baseSql = "SELECT DISTINCT l.id, l.title, l.description, l.date, u.username, u.id as user_id,
            COALESCE(lk.nbjaime, 0) as nbjaime, COALESCE(dk.nbjaimepas, 0) as nbjaimepas FROM lecon l
            INNER JOIN users u ON u.id = l.user_id
            LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaime FROM likes GROUP BY quiz_id) lk ON lk.quiz_id = l.quiz_id
            LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaimepas FROM dislikes GROUP BY quiz_id) dk ON dk.quiz_id = l.quiz_id
            WHERE l.title LIKE ?";

            $allowedOrder = [
                'date_desc' => 'l.date DESC',
                'date_asc' => 'l.date ASC',
                'title_asc' => 'l.title ASC',
                'title_desc' => 'l.title DESC',
                'author_asc' => 'u.username ASC',
                'author_desc' => 'u.username DESC'
            ];

            if ($tris && isset($allowedOrder[$tris])) {
                $baseSql .= ' ORDER BY ' . $allowedOrder[$tris];
            }

            $sql = $this->db->prepare($baseSql . ';');
            $sql->bindValue(1, '%' . $recherche_titre . '%');
            $sql->execute();

            $lessons = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $lessons;
        } catch (PDOException $e) {
            die("Searching lessons by title failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Recherche les leçons par contenu et auteur, avec option de tri.
     *
     * @param string $recherche_contenu Texte à rechercher dans le titre ou description
     * @param string $recherche Nom de l'auteur à rechercher
     * @param string|null $tris Type de tri (optionnel)
     *
     * @return array<mixed> Liste de leçons correspondantes
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function searchLessonByContentAndAuthor(string $recherche_contenu, string $recherche, string|null $tris = null): mixed
    {
        try {
            $baseSql = "SELECT DISTINCT l.id, l.title, l.description, l.date, u.username, u.id as user_id,
            COALESCE(lk.nbjaime, 0) as nbjaime, COALESCE(dk.nbjaimepas, 0) as nbjaimepas FROM lecon l
            INNER JOIN users u ON u.id = l.user_id
            LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaime FROM likes GROUP BY quiz_id) lk ON lk.quiz_id = l.quiz_id
            LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaimepas FROM dislikes GROUP BY quiz_id) dk ON dk.quiz_id = l.quiz_id
            WHERE (l.title LIKE ? OR l.description LIKE ?) AND u.username LIKE ?";

            $allowedOrder = [
                'date_desc' => 'l.date DESC',
                'date_asc' => 'l.date ASC',
                'title_asc' => 'l.title ASC',
                'title_desc' => 'l.title DESC',
                'author_asc' => 'u.username ASC',
                'author_desc' => 'u.username DESC'
            ];
            if ($tris && isset($allowedOrder[$tris])) {
                $baseSql .= ' ORDER BY ' . $allowedOrder[$tris];
            }

            $sql = $this->db->prepare($baseSql . ';');
            $sql->bindValue(1, '%' . $recherche_contenu . '%');
            $sql->bindValue(2, '%' . $recherche_contenu . '%');
            $sql->bindValue(3, '%' . $recherche . '%');
            $sql->execute();

            $lessons = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $lessons;
        } catch (PDOException $e) {
            die("Searching lessons by content and author failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère les catégories associées à une leçon spécifique.
     *
     * @param int $lesson_id Identifiant de la leçon
     *
     * @return array<mixed> Tableau associatif avec les clés :
     *                      - 'id' : identifiant de la catégorie
     *                      - 'categorieName' : nom de la catégorie
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception Pour toute autre erreur.
     */
    public function getCategoriesFromLesson(int $lesson_id): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT DISTINCT categories.id, categories.categorieName FROM categories 
            INNER JOIN categorie_quiz ON categorie_quiz.category_id = categories.id 
            INNER JOIN quiz ON quiz.id = categorie_quiz.quiz_id
            INNER JOIN lecon ON lecon.quiz_id = quiz.id
            WHERE lecon.id = ?;");
            $sql->bindParam(1, $lesson_id);
            $sql->execute();

            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        } catch (PDOException $e) {
            die("Fetching categories from lesson failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
