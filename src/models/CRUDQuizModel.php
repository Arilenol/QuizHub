<?php

class CRUDQuizModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère les informations complètes d’un quiz.
     *
     * @param int|string $quiz_id Identifiant du quiz.
     *
     * @return array|false Tableau associatif contenant les informations du quiz,
     *                     ou false si aucun quiz ne correspond.
     */
    public function getQuizInfo($quiz_id): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT quiz.id, quiz.title, quiz.description, quiz.difficulty, 
                                              quiz.user_id, quiz.date, quiz.genre, quiz.disponibilite, COALESCE(l.nbjaime, 0) as nbjaime, COALESCE(d.nbjaimepas, 0) as nbjaimepas 
                                       FROM quiz 
                                       LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaime FROM likes GROUP BY quiz_id) l ON l.quiz_id = quiz.id
                                       LEFT JOIN (SELECT quiz_id, COUNT(*) as nbjaimepas FROM dislikes GROUP BY quiz_id) d ON d.quiz_id = quiz.id
                                       WHERE quiz.id = ?");
            $sql->bindParam(1, $quiz_id);
            $sql->execute();

            $quiz = $sql->fetch(PDO::FETCH_ASSOC);
            return $quiz;
        } catch (PDOException $e) {
            die("Fetching quiz info failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère toutes les questions d’un quiz.
     *
     * @param int|string $quiz_id Identifiant du quiz.
     *
     * @return array Tableau de tableaux associatifs contenant :
     *               - id
     *               - quiz_id
     *               - numeroQuiz
     *               - enonce
     */
    public function getQuizQuestions($quiz_id): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT id, quiz_id, numeroQuiz, question AS enonce
                                       FROM question 
                                       WHERE quiz_id = ? 
                                       ORDER BY numeroQuiz ASC");
            $sql->bindParam(1, $quiz_id);
            $sql->execute();

            $questions = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $questions;
        } catch (PDOException $e) {
            die("Fetching quiz questions failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère toutes les réponses d’une question.
     *
     * @param int|string $question_id Identifiant de la question.
     *
     * @return array Tableau des réponses associées à la question.
     */
    public function getQuestionAnswers($question_id): mixed
    {
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
        } catch (PDOException $e) {
            die("Fetching question answers failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère les catégories associées à un quiz.
     *
     * @param int|string $quiz_id Identifiant du quiz.
     *
     * @return array Tableau des catégories (id, categorieName).
     */
    public function getCategoriesFromQuiz($quiz_id): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT DISTINCT categories.id, categories.categorieName FROM categories 
                                       INNER JOIN categorie_quiz ON categorie_quiz.category_id = categories.id 
                                       WHERE categorie_quiz.quiz_id = ?");
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
     * Met à jour l’énoncé d’une question.
     *
     * @param int $question_id Identifiant de la question.
     * @param string $enonce Nouveau texte de la question.
     *
     * @return bool True si au moins une ligne a été modifiée, false sinon.
     */
    public function updateQuestion($question_id, $enonce): bool
    {
        try {
            $sql = $this->db->prepare("UPDATE question SET question = ? WHERE id = ?");
            $sql->bindParam(1, $enonce);
            $sql->bindParam(2, $question_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Updating question failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Supprime une question.
     *
     * @param int $question_id Identifiant de la question à supprimer.
     *
     * @return bool True si la suppression a affecté une ligne, false sinon.
     */
    public function deleteQuestion($question_id): bool
    {
        try {
            $sql = $this->db->prepare("DELETE FROM question WHERE id = ?");
            $sql->bindParam(1, $question_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Deleting question failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Met à jour une réponse existante.
     *
     * @param int $answer_id Identifiant de la réponse.
     * @param string $texte Nouveau texte de la réponse.
     * @param int $est_correct Indicateur (0 ou 1) précisant si la réponse est correcte.
     *
     * @return bool True si la mise à jour a réussi, false sinon.
     */
    public function updateAnswer($answer_id, $texte, $est_correct): bool
    {
        try {
            $sql = $this->db->prepare("UPDATE reponse SET reponse = ?, estCorrecte = ? WHERE id = ?");
            $sql->bindParam(1, $texte);
            $sql->bindParam(2, $est_correct, PDO::PARAM_INT);
            $sql->bindParam(3, $answer_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Updating answer failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Supprime une réponse.
     *
     * @param int $answer_id Identifiant de la réponse à supprimer.
     *
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function deleteAnswer($answer_id): bool
    {
        try {
            $sql = $this->db->prepare("DELETE FROM reponse WHERE id = ?");
            $sql->bindParam(1, $answer_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Deleting answer failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Supprime un quiz.
     *
     * @param int $quiz_id Identifiant du quiz à supprimer.
     *
     * @return bool True si le quiz a été supprimé, false sinon.
     */
    public function deleteQuiz($quiz_id): bool
    {
        try {
            $sql = $this->db->prepare("DELETE FROM quiz WHERE id = ?");
            $sql->bindParam(1, $quiz_id, PDO::PARAM_INT);
            $sql->execute();

            $sql = $this->db->prepare("UPDATE lecon SET quiz_id = null WHERE quiz_id = ? ");
            $sql->execute([$quiz_id]);

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Deleting quiz failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Met à jour les informations principales d’un quiz.
     *
     * Modifie le titre, la description, la difficulté et le genre.
     *
     * @param int $quiz_id Identifiant du quiz.
     * @param string $title Nouveau titre.
     * @param string $description Nouvelle description.
     * @param int $difficulty Niveau de difficulté.
     * @param string $genre Genre du quiz.
     *
     * @return bool True si la mise à jour a modifié une ligne, false sinon.
     */
    public function updateQuiz($quiz_id, $title, $description, $difficulty, $genre): bool
    {
        try {
            $sql = $this->db->prepare("UPDATE quiz SET title = ?, description = ?, difficulty = ?, genre = ? WHERE id = ?");
            $sql->bindParam(1, $title);
            $sql->bindParam(2, $description);
            $sql->bindParam(3, $difficulty, PDO::PARAM_INT);
            $sql->bindParam(4, $genre);
            $sql->bindParam(5, $quiz_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Updating quiz failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère le nom d’utilisateur (username) d’un auteur.
     *
     * @param int|string $user_id Identifiant de l’utilisateur.
     *
     * @return string Nom d’utilisateur ou chaîne vide si non trouvé.
     */
    public function getNomAuteur($user_id): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT username FROM users WHERE id = ?");
            $sql->bindParam(1, $user_id);
            $sql->execute();

            $auteur = $sql->fetch(PDO::FETCH_ASSOC);
            return $auteur['username'] ?? '';
        } catch (PDOException $e) {
            die("Fetching author name failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère toutes les cartes d’une flashcard (quiz type carte).
     *
     * Les cartes sont triées par leur numéro (numeroCarte ASC).
     *
     * @param int|string $quiz_id Identifiant du quiz.
     *
     * @return array Tableau des cartes contenant :
     *               - id
     *               - quiz_id
     *               - numeroCarte
     *               - question
     *               - reponse
     */
    public function getFlashcardCards($quiz_id): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT id, quiz_id, numeroCarte, question, reponse
                                       FROM carte 
                                       WHERE quiz_id = ? 
                                       ORDER BY numeroCarte ASC");
            $sql->bindParam(1, $quiz_id);
            $sql->execute();

            $cards = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $cards;
        } catch (PDOException $e) {
            die("Fetching flashcard cards failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Met à jour une carte de flashcard.
     *
     * @param int $card_id Identifiant de la carte.
     * @param string $question Nouveau texte de la question.
     * @param string $reponse Nouvelle réponse.
     *
     * @return bool True si la mise à jour a réussi, false sinon.
     */

    public function updateCard($card_id, $question, $reponse): bool
    {
        try {
            $sql = $this->db->prepare("UPDATE carte SET question = ?, reponse = ? WHERE id = ?");
            $sql->bindParam(1, $question);
            $sql->bindParam(2, $reponse);
            $sql->bindParam(3, $card_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Updating card failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Supprime une carte de flashcard.
     *
     * @param int $card_id Identifiant de la carte.
     *
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function deleteCard($card_id): bool
    {
        try {
            $sql = $this->db->prepare("DELETE FROM carte WHERE id = ?");
            $sql->bindParam(1, $card_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Deleting card failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Met à jour la disponibilité d’un quiz.
     *
     * @param int $quiz_id Identifiant du quiz.
     * @param string $disponibilite Nouvelle disponibilité
     *                              (ex: 'public', 'prive', 'ami').
     *
     * @return bool True si la mise à jour a réussi, false sinon.
     */
    public function updateDisponibilite($quiz_id, $disponibilite): bool
    {
        try {
            $sql = $this->db->prepare("UPDATE quiz SET disponibilite = ? WHERE id = ?");
            $sql->bindParam(1, $disponibilite);
            $sql->bindParam(2, $quiz_id, PDO::PARAM_INT);
            $sql->execute();

            return $sql->rowCount() > 0;
        } catch (PDOException $e) {
            die("Updating disponibilite failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
