<?php

class CRUDQuizModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère les informations complètes d'un quiz
     */
    public function getQuizInfo($quiz_id): mixed {
        try {
            $sql = $this->db->prepare("SELECT quiz.id, quiz.title, quiz.description, quiz.difficulty, 
                                              quiz.user_id, quiz.date, quiz.genre, quiz.nbjaime, quiz.nbjaimepas 
                                       FROM quiz 
                                       WHERE quiz.id = ?");
            $sql->bindParam(1, $quiz_id);
            $sql->execute();
            
            $quiz = $sql->fetch(PDO::FETCH_ASSOC);
            return $quiz;
        } catch(PDOException $e) {
            die("Fetching quiz info failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère toutes les questions d'un quiz
     *
     * NOTE: la colonne de texte de la question s'appelle `question` dans la BDD,
     * la fonction renvoie la colonne aliasée en `enonce` pour être compatible avec la vue.
     */
    public function getQuizQuestions($quiz_id): mixed {
        try {
            $sql = $this->db->prepare("SELECT id, quiz_id, numeroQuiz, question AS enonce
                                       FROM question 
                                       WHERE quiz_id = ? 
                                       ORDER BY numeroQuiz ASC");
            $sql->bindParam(1, $quiz_id);
            $sql->execute();
            
            $questions = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $questions;
        } catch(PDOException $e) {
            die("Fetching quiz questions failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère les réponses d'une question
     *
     * NOTE: la table `reponse` utilise la colonne `reponse` pour le texte et `estCorrecte`
     * pour l'indicateur de validité. On aliasera ces colonnes en `texte` et `est_correct`
     * pour correspondre à la vue actuelle.
     */
    public function getQuestionAnswers($question_id): mixed {
        try {
            $sql = $this->db->prepare("SELECT id, question_id, reponse AS texte, estCorrecte AS est_correct
                                       FROM reponse 
                                       WHERE question_id = ? 
                                       ORDER BY id ASC");
            $sql->bindParam(1, $question_id);
            $sql->execute();
            
            $answers = $sql->fetchAll(PDO::FETCH_ASSOC);

            // Normaliser les valeurs booléennes en 0/1 ou true/false si besoin
            foreach ($answers as &$a) {
                
                $a['est_correct'] = isset($a['est_correct']) ? (int)$a['est_correct'] : 0;
            }
            unset($a);

            return $answers;
        } catch(PDOException $e) {
            die("Fetching question answers failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère les catégories d'un quiz
     */
    public function getCategoriesFromQuiz($quiz_id): mixed {
        try {
            $sql = $this->db->prepare("SELECT DISTINCT categories.id, categories.categorieName FROM categories 
                                       INNER JOIN categorie_quiz ON categorie_quiz.category_id = categories.id 
                                       WHERE categorie_quiz.quiz_id = ?");
            $sql->bindParam(1, $quiz_id);
            $sql->execute();
            
            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        } catch(PDOException $e) {
            die("Fetching categories from quiz failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère le nom de l'auteur
     */
    public function getNomAuteur($user_id): mixed {
        try {
            $sql = $this->db->prepare("SELECT username FROM users WHERE id = ?");
            $sql->bindParam(1, $user_id);
            $sql->execute();
            
            $auteur = $sql->fetch(PDO::FETCH_ASSOC);
            return $auteur['username'] ?? '';
        } catch(PDOException $e) {
            die("Fetching author name failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
?>