<?php

class CatalogueModel {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    public function getCategories(): mixed{
        try{
            $sql = $this->db->prepare("SELECT DISTINCT id,CategorieName FROM categories;");
            $sql->execute();
            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        }catch(PDOException $e){
            die("Fetching categories failed: " . $e->getMessage());
        }catch(Exception $e){
            die("Error: " . $e->getMessage());
        }
    }

    public function searchQuizByAll($recherche_cat,$recherche_contenu,$recherche_auteur,$tris = null): mixed{
        try{
            $baseSql = "SELECT DISTINCT quiz.id, title, quiz.description, difficulty, quiz.user_id, date, genre,
            quiz.nbjaime,quiz.nbjaimepas FROM quiz 
            INNER JOIN categorie_quiz ON categorie_quiz.quiz_id = quiz.id 
            INNER JOIN categories ON categories.id = categorie_quiz.category_id 
            INNER JOIN users ON users.id = quiz.user_id
            WHERE categories.id = ? AND (quiz.title LIKE ? OR quiz.description LIKE ?) AND users.username LIKE ?";

            
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
            $sql->bindValue(1,$recherche_cat);
            $sql->bindValue(2,'%'.$recherche_contenu.'%');
            $sql->bindValue(3,'%'.$recherche_contenu.'%');
            $sql->bindValue(4,'%'.$recherche_auteur.'%');
            $sql->execute();

            $quiz = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $quiz;
        }catch(PDOException $e){
            die("Searching categories failed: " . $e->getMessage());
        }catch(Exception $e){
            die("Error: " . $e->getMessage());
        }
    }
    public function getNomAuteur($user_id): mixed{
        try{
            $sql = $this->db->prepare("SELECT username FROM users WHERE id = ?;");
            $sql->bindParam(1,$user_id);
            $sql->execute();

            $auteur = $sql->fetch(PDO::FETCH_ASSOC);
            return $auteur['username'];
        }catch(PDOException $e){
            die("Fetching author name failed: " . $e->getMessage());
        }catch(Exception $e){
            die("Error: " . $e->getMessage());
        }
    }
    public function searchQuizByContentAndAuthor($recherche_contenu,$recherche_auteur,$tris = null): mixed{
        try{
            $baseSql = "SELECT DISTINCT quiz.id, title, quiz.description, difficulty, quiz.user_id, date, genre,
            quiz.nbjaime,quiz.nbjaimepas FROM quiz 
            INNER JOIN categorie_quiz ON categorie_quiz.quiz_id = quiz.id 
            INNER JOIN categories ON categories.id = categorie_quiz.category_id 
            INNER JOIN users ON users.id = quiz.user_id
            WHERE (quiz.title LIKE ? OR quiz.description LIKE ?) AND users.username LIKE ?";

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
            $sql->bindValue(1,'%'.$recherche_contenu.'%');
            $sql->bindValue(2,'%'.$recherche_contenu.'%');
            $sql->bindValue(3,'%'.$recherche_auteur.'%');
            $sql->execute();

            $quiz = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $quiz;
        }catch(PDOException $e){
            die("Searching categories failed: " . $e->getMessage());
        }catch(Exception $e){
            die("Error: " . $e->getMessage());
        }
            
    }
    public function getCategoriesFromQuiz( $quiz_id): mixed{
        try{
            $sql = $this->db->prepare("SELECT DISTINCT categories.id, categories.categorieName FROM categories 
            INNER JOIN categorie_quiz ON categorie_quiz.category_id = categories.id 
            WHERE categorie_quiz.quiz_id = ?;");
            $sql->bindParam(1,$quiz_id);
            $sql->execute();

            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        }catch(PDOException $e){
            die("Fetching categories from quiz failed: " . $e->getMessage());
        }catch(Exception $e){
            die("Error: " . $e->getMessage());
        }
    }
}
?>