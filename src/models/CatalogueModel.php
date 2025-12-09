<?php

class CatalogueModel
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère la liste des catégories disponibles.
     *
     * Cette méthode retourne un tableau associatif contenant les catégories
     * (id et CategorieName) présentes dans la table `categories`.
     *
     * @return array|false  Tableau associatif des catégories, ou false en cas d'erreur.
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
     * Recherche des quizzes par catégorie, contenu et auteur.
     *
     * Construit une requête qui recherche les quizzes correspondant à une
     * catégorie, un texte présent dans le titre/description, et un nom d'auteur.
     * Le paramètre `$tris` permet d'ajouter un ORDER BY sécurisé (via whitelist).
     *
     * @param int|string|null $recherche_cat    ID de la catégorie recherchée (ou null pour ignorer).
     * @param string          $recherche_contenu Terme à chercher dans le titre/la description.
     * @param string          $recherche_auteur  Filtre sur le nom d'utilisateur de l'auteur.
     * @param string|null     $tris             Clé de tri autorisée (voir whitelist dans la méthode).
     *
     * @return array|false  Tableau associatif des quizzes correspondants, ou false en cas d'erreur.
     */
    public function searchQuizByAll($recherche_cat, $recherche_contenu, $recherche_auteur, $tris = null): mixed
    {
        try {
            $baseSql = "SELECT DISTINCT quiz.id, title, quiz.description, difficulty, quiz.user_id, date, genre FROM quiz 
            LEFT JOIN categorie_quiz ON categorie_quiz.quiz_id = quiz.id
            LEFT JOIN categories ON categories.id = categorie_quiz.category_id
            INNER JOIN users ON users.id = quiz.user_id
            WHERE quiz.id IN (SELECT quiz_id FROM amiDisponibilite WHERE ami_id = )categories.id = ? AND (quiz.title LIKE ? OR quiz.description LIKE ?) AND users.username LIKE ?";


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
            $sql->bindValue(4, '%' . $recherche_auteur . '%');
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
     * Récupère le nom d'utilisateur (username) d'un auteur par son ID.
     *
     * @param int $user_id  Identifiant de l'utilisateur.
     *
     * @return string|null  Le nom d'utilisateur si trouvé, ou null si non trouvé.
     */
    public function getNomAuteur($user_id): mixed
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
     * Recherche des quizzes par contenu et auteur.
     *
     * Cette méthode recherche les quizzes dont le titre ou la description
     * contient `$recherche_contenu` et dont l'auteur correspond à
     * `$recherche_auteur`. Le tri peut être passé via `$tris` (whitelist).
     *
     * @param string      $recherche_contenu Terme à chercher dans le titre/la description.
     * @param string      $recherche_auteur  Filtre sur le nom d'utilisateur de l'auteur.
     * @param string|null $tris              Clé de tri autorisée (optionnelle).
     *
     * @return array|false  Tableau associatif des quizzes correspondants, ou false en cas d'erreur.
     */
    public function searchQuizByContentAndAuthor($recherche_contenu, $recherche_auteur, $tris = null): mixed
    {
        try {
            $baseSql = "
        SELECT 
            quiz.id,
            quiz.title,
            quiz.description,
            quiz.difficulty,
            quiz.user_id,
            quiz.date,
            quiz.genre,
            users.username,
            (SELECT COUNT(*) FROM likes WHERE likes.quiz_id = quiz.id) AS likes,
            (SELECT COUNT(*) FROM dislikes WHERE dislikes.quiz_id = quiz.id) AS dislikes
        FROM quiz
        INNER JOIN users ON users.id = quiz.user_id
        WHERE (quiz.title LIKE ? OR quiz.description LIKE ?)
        AND users.username LIKE ?
        ";

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
            $sql->bindValue(3, '%' . $recherche_auteur . '%');
            $sql->execute();

            return $sql->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Searching categories failed: " . $e->getMessage());
        }
    }


    /**
     * Récupère les catégories associées à un quiz donné.
     *
     * @param int $quiz_id  Identifiant du quiz.
     *
     * @return array|false  Tableau associatif des catégories (id, categorieName), ou false en cas d'erreur.
     */
    public function getCategoriesFromQuiz($quiz_id): mixed
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
}
