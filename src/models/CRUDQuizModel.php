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
                                              quiz.user_id, quiz.date, quiz.genre, COALESCE(l.nbjaime, 0) as nbjaime, COALESCE(d.nbjaimepas, 0) as nbjaimepas 
                                       FROM quiz 
                                       LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaime FROM likes GROUP BY quiz_id) l ON l.quiz_id = quiz.id
                                       LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaimepas FROM dislikes GROUP BY quiz_id) d ON d.quiz_id = quiz.id
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
     * Met à jour une question
     */
    public function updateQuestion($question_id, $enonce): bool {
        try {
            $sql = $this->db->prepare("UPDATE question SET question = ? WHERE id = ?");
            $sql->bindParam(1, $enonce);
            $sql->bindParam(2, $question_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch(PDOException $e) {
            die("Updating question failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Supprime une question
     */
    public function deleteQuestion($question_id): bool {
        try {
            $sql = $this->db->prepare("DELETE FROM question WHERE id = ?");
            $sql->bindParam(1, $question_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch(PDOException $e) {
            die("Deleting question failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Met à jour une réponse
     */
    public function updateAnswer($answer_id, $texte, $est_correct): bool {
        try {
            $sql = $this->db->prepare("UPDATE reponse SET reponse = ?, estCorrecte = ? WHERE id = ?");
            $sql->bindParam(1, $texte);
            $sql->bindParam(2, $est_correct, PDO::PARAM_INT);
            $sql->bindParam(3, $answer_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch(PDOException $e) {
            die("Updating answer failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    /**
     * Supprime un quiz
     */
    public function deleteQuiz($quiz_id): bool {
        try {
            $sql = $this->db->prepare("DELETE FROM quiz WHERE id = ?");
            $sql->bindParam(1, $quiz_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch(PDOException $e) {
            die("Deleting quiz failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Met à jour les informations d'un quiz
     */
    public function updateQuiz($quiz_id, $title, $description, $difficulty, $genre): bool {
        try {
            $sql = $this->db->prepare("UPDATE quiz SET title = ?, description = ?, difficulty = ?, genre = ? WHERE id = ?");
            $sql->bindParam(1, $title);
            $sql->bindParam(2, $description);
            $sql->bindParam(3, $difficulty, PDO::PARAM_INT);
            $sql->bindParam(4, $genre);
            $sql->bindParam(5, $quiz_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch(PDOException $e) {
            die("Updating quiz failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Supprime une réponse
     */
    public function deleteAnswer($answer_id): bool {
        try {
            $sql = $this->db->prepare("DELETE FROM reponse WHERE id = ?");
            $sql->bindParam(1, $answer_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch(PDOException $e) {
            die("Deleting answer failed: " . $e->getMessage());
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