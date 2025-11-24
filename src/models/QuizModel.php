<?php
class QuizModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Retourne le nombre total de questions d’un quiz.
     *
     * Cette méthode compte toutes les entrées de la table `question` associées
     * à un quiz donné via son identifiant. Si aucune question n'existe,
     * elle renvoie 0.
     *
     * @param int $idQuiz  Identifiant du quiz dont on souhaite connaître le nombre de questions.
     *
     * @return int  Nombre total de questions du quiz. 0 si aucune question n’est trouvée.
     */

    public function getMaxNbQuestion(int $idQuiz): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(id) AS max_questions FROM question WHERE quiz_id = ?");
        $stmt->execute([$idQuiz]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int) $result['max_questions'] : 0;
    }


    /**
     * Récupère une question d'un quiz selon son numéro d'ordre.
     *
     * Cette méthode permet de récupérer une question spécifique associée
     * à un quiz, en utilisant l'ID du quiz ainsi que le numéro de question
     * (numeroquiz). Si le numéro n'est pas fourni, la méthode récupère par défaut
     * la première question du quiz.
     *
     * @param int      $idQuiz      Identifiant du quiz dont on veut récupérer la question.
     * @param int|null $idQuestion  Numéro de la question dans le quiz (1 par défaut).
     *
     * @return array|false Retourne un tableau associatif contenant la question,
     *                     ou false si aucune question ne correspond.
     */

    public function getQuestion(int $idQuiz, ?int $idQuestion = 1): array|false
    {
        $stmt = $this->db->prepare("
        SELECT *
        FROM question
        WHERE quiz_id = ? AND numeroquiz = ?
    ");
        $stmt->execute([$idQuiz, $idQuestion]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    /**
     * Récupère toutes les réponses associées à une question.
     *
     * Cette méthode retourne l'ensemble des réponses liées à une question
     * spécifique, identifiée par son ID. Chaque réponse est renvoyée sous
     * forme de tableau associatif contenant ses informations (texte,
     * validité, identifiant, etc.).
     *
     * @param int $idQuestion  Identifiant de la question dont on veut obtenir les réponses.
     *
     * @return array|false  Retourne un tableau contenant toutes les réponses sous forme
     *                      de tableaux associatifs, ou false si aucune réponse n'est trouvée.
     */

    public function getReponses(int $idQuestion): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM reponse WHERE question_id = ?");
        $stmt->execute([$idQuestion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function createQuiz(int $user_id, array $params, array $TAB_CONTENU, string $desc, string $title , int $nbQuestion, array $nbReponse){
        try{
            $this->db->beginTransaction();
            $newQuiz = $this->insertQuiz($user_id, $title, $desc, date('Y-m-d'), 'standard');
            if (!$newQuiz){
                throw new PDOException("erreur dans l\'insertion du Quiz dans QuizModel.php/createQuiz");
            }
            for ($i = 0; $i < $nbQuestion; $i++){
                $newQuestion = $this->insertQuestion($i, $newQuiz, $TAB_CONTENU[$i]['name']);
                if (!$newQuestion){
                    throw new PDOException("erreur dans l\'insertion de question dans QuizModel.php/createQuiz");
                }
                for ($k = 0; $k < $nbReponse[$i] ; $k++){
                    $newReponse = $this->insertReponse($newQuestion, $TAB_CONTENU[$i]['reponses'][$k]['texte'], $TAB_CONTENU[$i]['reponses'][$k]['valide']);
                    if (!$newReponse){
                        throw new PDOException("erreur dans l\'insertion de reponse dans QuizModel.php/createQuiz");
                    }
                }
            }
            $this->db->commit();

        }catch (PDOException $e){
            error_log("Erreur création de quiz entier : " . $e->getMessage());
            $this->db->rollBack();
            return false;
        }
    }


    public function insertQuiz(int $user_id, string $title, string $desc, string $date, string $genre){
        try{
            $newQuiz = $this->db->prepare("INSERT INTO Quiz(user_id, title, description, difficulty, disponibilite, nbjaime, nbjaimepas, date, genre)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);");
            $newQuiz->bindValue(1,$user_id);
            $newQuiz->bindValue(2,$title);
            $newQuiz->bindValue(3,$desc);
            $newQuiz->bindValue(4,1);
            $newQuiz->bindValue(5,'public');
            $newQuiz->bindValue(6,0);
            $newQuiz->bindValue(7,0);
            $newQuiz->bindValue(7,$date);
            $newQuiz->bindValue(7,'standard');

            $reussite = $newQuiz->execute();
            if (!$reussite){
                return false;
            }else{
                return $this->db->lastInsertId();
            }
        }catch (PDOException $e){
            error_log("Erreur d'insertion de quiz : ".$e->getMessage());
            return false;
        }
    }

    public function insertQuestion(int $numero, int $quiz_id, string $question){
        try{
            $newQuestion = $this->db->prepare("INSERT INTO Question (numeroQuiz, quiz_id, question) VALUES (?, ?, ?);");
            $newQuestion->bindValue(1, $numero);
            $newQuestion->bindValue(2, $quiz_id);
            $newQuestion->bindValue(3, $question);

            $reussite = $newQuestion->execute();
            if (!$reussite){
                return false;
            }else{
                return $this->db->lastInsertId();
            }
        }catch (PDOException $e){
            error_log("Erreur d'insertion de question : ".$e->getMessage());
            return false;
        }
    }

    public function insertReponse(int $question_id, string $contenu, int $valide){
        try{
            $newReponse = $this->db->prepare("INSERT INTO Reponse(question_id, reponse, estCorrecte) VALUES (?, ?, ?);");
            $newReponse->bindValue(1, $question_id);
            $newReponse->bindValue(2, $contenu);
            $newReponse->bindValue(3, $valide);

            $reussite = $newReponse->execute();
            if (!$reussite){
                return false;
            }else{
                return $this->db->lastInsertId();
            }
        }catch (PDOException $e){
            error_log("Erreur d'insertion de reponse : ".$e->getMessage());
            return false;
        }
    }
}
