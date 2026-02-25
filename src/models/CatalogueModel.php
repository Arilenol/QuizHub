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
    public function searchContentByAll(int|null $user_id, int|null $recherche_cat, string $recherche_contenu = "", string $recherche_auteur = "", string $genre = "", string|null $tris = null): mixed
    {
        try {
            $baseSql = "SELECT * FROM 
            (SELECT DISTINCT quiz.id, title, quiz.description, difficulty, users.username, date, genre,
            (SELECT COUNT(like_id) FROM likes WHERE quiz_id = quiz.id) AS likes,
            (SELECT COUNT(dislike_id) FROM dislikes WHERE quiz_id = quiz.id) AS dislikes
            FROM quiz 
            LEFT JOIN categorie_quiz ON categorie_quiz.quiz_id = quiz.id
            LEFT JOIN categories ON categories.id = categorie_quiz.category_id
            INNER JOIN users ON users.id = quiz.user_id
            WHERE (
            quiz.disponibilite IS NULL
            OR quiz.disponibilite = 'public' 
            OR (quiz.disponibilite = 'ami' AND EXISTS(SELECT quiz_id FROM amiDisponibilite WHERE ami_id = ? AND quiz_id = quiz.id))
            OR quiz.user_id = ? )
            AND (categories.id = ? OR ? IS NULL) AND (quiz.title LIKE ? OR quiz.description LIKE ?) AND users.username LIKE ?
            UNION
            SELECT DISTINCT lesson.id, title, lesson.description, NULL AS difficulty, users.username, date, 'leçon' AS genre,
            NULL AS likes,NULL AS dislikes FROM Lecon AS lesson
            LEFT JOIN categorie_lecon ON categorie_lecon.lesson_id = lesson.id
            LEFT JOIN categories ON categories.id = categorie_lecon.category_id
            INNER JOIN users ON users.id = lesson.user_id
            WHERE (
            lesson.disponibilite IS NULL
            OR lesson.disponibilite = 'public'
            OR (lesson.disponibilite = 'ami' AND EXISTS(SELECT lesson_id FROM amiDisponibilite WHERE ami_id = ? AND lesson_id = lesson.id))
            OR lesson.user_id = ?)
            AND (categories.id = ? OR ? IS NULL) AND (lesson.title LIKE ? OR lesson.description LIKE ?) AND users.username LIKE ?) AS Requete
            WHERE Requete.genre LIKE ?";


            $allowedOrder = [
                'date_desc' => 'Requete.date DESC',
                'date_asc' => 'Requete.date ASC',
                'title_asc' => 'Requete.title ASC',
                'title_desc' => 'Requete.title DESC',
                'difficulty_asc' => 'Requete.difficulty ASC',
                'difficulty_desc' => 'Requete.difficulty DESC',
                'author_asc' => 'Requete.username ASC',
                'author_desc' => 'Requete.username DESC',
                'genre_asc' => 'Requete.genre ASC',
                'genre_desc' => 'Requete.genre DESC',
                'popup_asc' => '(Requete.likes - Requete.dislikes) ASC',
                'popup_desc' => '(Requete.likes - Requete.dislikes) DESC'
            ];

            if ($tris && isset($allowedOrder[$tris])) {
                $baseSql .= ' ORDER BY ' . $allowedOrder[$tris];
            }

            $sql = $this->db->prepare($baseSql . ' LIMIT 500;');
            $sql->bindValue(1, $user_id);
            $sql->bindValue(2, $user_id);
            $sql->bindValue(3, $recherche_cat);
            $sql->bindValue(4, $recherche_cat);
            $sql->bindValue(5, '%' . $recherche_contenu . '%');
            $sql->bindValue(6, '%' . $recherche_contenu . '%');
            $sql->bindValue(7, '%' . $recherche_auteur . '%');
            $sql->bindValue(8, $user_id);
            $sql->bindValue(9, $user_id);
            $sql->bindValue(10, $recherche_cat);
            $sql->bindValue(11, $recherche_cat);
            $sql->bindValue(12, '%' . $recherche_contenu . '%');
            $sql->bindValue(13, '%' . $recherche_contenu . '%');
            $sql->bindValue(14, '%' . $recherche_auteur . '%');
            $sql->bindValue(15, '%' . $genre . '%');
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


    /**
     * Récupère toutes les catégories associées à une leçon donnée.
     *
     * @param int $lesson_id L'identifiant de la leçon pour laquelle on veut récupérer les catégories.
     *
     * @return array<mixed> Un tableau associatif contenant les catégories. Chaque élément du tableau
     *                      a les clés suivantes :
     *                      - 'id' : L'identifiant de la catégorie.
     *                      - 'categorieName' : Le nom de la catégorie.
     *
     * @throws PDOException Si une erreur survient lors de l'exécution de la requête SQL.
     * @throws Exception   Pour toute autre erreur.
     */
    public function getCategoriesFromLesson($lesson_id): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT DISTINCT categories.id, categories.categorieName FROM categories 
            INNER JOIN categorie_lecon ON categorie_lecon.category_id = categories.id 
            WHERE categorie_lecon.lesson_id = ?;");
            $sql->bindParam(1, $lesson_id);
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
