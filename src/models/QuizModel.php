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

    public function getMaxNbQuestion(int $quizId): int
    {
        $stmt = $this->db->prepare("
        SELECT MAX(numeroQuiz) AS maxi
        FROM question
        WHERE quiz_id = ?
    ");
        $stmt->execute([$quizId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return intval($row['maxi']);
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
    public function getQuestion(int $idQuiz, ?int $idQuestion = null): array|false
    {
        if ($idQuestion === null) {
            $idQuestion = $this->getMiniQuestionId($idQuiz);
        }
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


    /**
     * Récupère toutes les réponses justes associées à une question.
     *
     * Cette méthode retourne l'ensemble des réponses justes liées à une question
     * spécifique, identifiée par son ID. Chaque réponse est renvoyée sous
     * forme de tableau associatif contenant ses informations (texte,
     * validité, identifiant, etc.).
     *
     * @param int $idQuestion  Identifiant de la question dont on veut obtenir les réponses.
     *
     * @return array|false  Retourne un tableau contenant toutes les réponses justes sous forme
     *                      de tableaux associatifs, ou false si aucune réponse n'est trouvée.
     */
    public function getCorrectAnswers(int $quizId, int $idQuestion): array
    {
        $stmt = $this->db->prepare("
        SELECT r.id 
        FROM reponse r
        JOIN question q ON r.question_id = q.id
        WHERE q.quiz_id = ?
        AND q.numeroQuiz = ?
        AND r.estCorrecte = 1
    ");

        $stmt->execute([$quizId, $idQuestion]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map('intval', array_column($results, 'id'));
    }

    /**
     * Récupère le numéro minimum d'une question d'un quiz.
     *
     * Cette méthode retourne le numéro de la première question (le plus petit)
     * associée à un quiz. Cela est utile pour déterminer le point de départ
     * lors du parcours des questions d'un quiz.
     *
     * @param int $quizId  Identifiant du quiz dont on veut obtenir le minimum de question.
     *
     * @return int  Numéro minimum de question du quiz (numeroQuiz).
     */
    public function getMiniQuestionId(int $quizId): int
    {
        $stmt = $this->db->prepare("
        SELECT MIN(numeroQuiz) AS mini
        FROM question
        WHERE quiz_id = ?
    ");
        $stmt->execute([$quizId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return intval($row['mini']);
    }

    /**
     * Crée un quiz complet avec toutes ses questions, réponses et paramètres.
     *
     * Cette méthode effectue une transaction complète qui crée un quiz, insère
     * toutes ses questions, réponses, paramètres optionnels et restrictions d'amis.
     * En cas d'erreur, la transaction est annulée et aucune donnée n'est conservée.
     *
     * @param int    $user_id          Identifiant de l'utilisateur créateur du quiz.
     * @param array  $params           Tableau des paramètres du quiz (minuterie, rejeu erreurs, ordre aléatoire, etc.).
     * @param int    $timer            Durée de la minuterie en minutes (si activée).
     * @param array  $TAB_CONTENU      Tableau contenant les questions et leurs réponses.
     * @param array  $TAB_AMI_CHOISI   Tableau des IDs d'amis autorisés à accéder au quiz (si dispo='ami').
     * @param string $disponibilite    Type de disponibilité du quiz ('public', 'ami', etc.).
     * @param string $desc             Description du quiz.
     * @param string $title            Titre du quiz.
     * @param int    $nbQuestion       Nombre total de questions dans le quiz.
     * @param array  $nbReponse        Tableau contenant le nombre de réponses pour chaque question.
     *
     * @return bool  true si la création est réussie, false en cas d'erreur.
     */
    public function createQuiz(int $user_id, array $params, int $timer, array $TAB_CONTENU,array $TAB_AMI_CHOISI, array $TAB_CATEGORIE_CHOISI, string $disponibilite,  string $desc, string $title, int $nbQuestion, array $nbReponse)
    {
        try {
            $this->db->beginTransaction();
            $genre = !empty($params[0]) ? 'test' : 'standard';
            $newQuiz = $this->insertQuiz($user_id, $title, $desc, $disponibilite, date('Y-m-d'),$genre);
            if (!$newQuiz) {
                throw new PDOException("erreur dans l\'insertion du Quiz dans QuizModel.php/createQuiz");
            }
            for ($i = 0; $i < $nbQuestion; $i++) {
                $newQuestion = $this->insertQuestion($i+1, $newQuiz, $TAB_CONTENU[$i]['name']);
                if (!$newQuestion) {
                    throw new PDOException("erreur dans l\'insertion de question dans QuizModel.php/createQuiz");
                }
                for ($k = 0; $k < $nbReponse[$i]; $k++) {
                    $newReponse = $this->insertReponse($newQuestion, $TAB_CONTENU[$i]['reponses'][$k]['texte'], $TAB_CONTENU[$i]['reponses'][$k]['valide']);
                    if (!$newReponse) {
                        throw new PDOException("erreur dans l\'insertion de reponse dans QuizModel.php/createQuiz");
                    }
                }
            }
            foreach($TAB_CATEGORIE_CHOISI as $categorie){
                $newCategorie = $this->insertQuizCategorie($newQuiz, (int)$categorie);
                if (!$newCategorie) {
                    throw new PDOException("erreur dans l\'insertion des catégories dans QuizModel.php/createQuiz");
                }
            }
            $hasParam = false;
            foreach($params as $p){
                if (!empty($p)){
                    $hasParam = true;
                }
            }
            if ($hasParam){
                $newParams = $this->insertQuizParams($newQuiz, $params, (int)$timer);
                if (!$newParams) {
                    throw new PDOException("erreur dans l\'insertion des paramètres dans QuizModel.php/createQuiz");
                }
            }
            if ($disponibilite == 'ami'){
                foreach($TAB_AMI_CHOISI as $ami){
                    $newAmiDispo = $this->insertAmiDispo($newQuiz, (int)$ami);
                    if (!$newAmiDispo) {
                        throw new PDOException("erreur dans l\'insertion des amis dans QuizModel.php/createQuiz");
                    }
                }
            }
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            error_log("Erreur création de quiz entier : " . $e->getMessage());
            $this->db->rollBack();
            return false;
        }
    }


    /**
     * Insère un nouveau quiz dans la base de données.
     *
     * Cette méthode crée un enregistrement quiz avec les informations fournies.
     * Les valeurs par défaut incluent : difficulté = 1, nbjaime = 0, nbjaimepas = 0.
     *
     * @param int    $user_id  Identifiant de l'utilisateur créateur du quiz.
     * @param string $title    Titre du quiz.
     * @param string $desc     Description du quiz.
     * @param string $dispo    Disponibilité du quiz ('public', 'ami', etc.).
     * @param string $date     Date de création du quiz (format 'Y-m-d').
     * @param string $genre    Genre ou type du quiz ('standard', 'test', etc.).
     *
     * @return int|false  Retourne l'ID du quiz inséré, ou false en cas d'erreur.
     */
    public function insertQuiz(int $user_id, string $title, string $desc,string $dispo,  string $date, string $genre)
    {
        try {
            $newQuiz = $this->db->prepare("INSERT INTO Quiz(user_id, title, description, difficulty, disponibilite, date, genre)
            VALUES (?, ?, ?, ?, ?, ?, ?);");
            $newQuiz->bindValue(1, $user_id);
            $newQuiz->bindValue(2, $title);
            $newQuiz->bindValue(3, $desc);
            $newQuiz->bindValue(4, 1);
            $newQuiz->bindValue(5, $dispo);
            $newQuiz->bindValue(6, $date);
            $newQuiz->bindValue(7, $genre);

            $reussite = $newQuiz->execute();
            if ($reussite === false) {
                return false;
            } else {
                return $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion de quiz : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Insère une association entre un quiz et une catégorie.
     *
     * Cette méthode crée un lien dans la table `categorie_quiz` pour indiquer
     * qu'une catégorie est associée à un quiz spécifique.
     *
     * @param int $quiz_id      Identifiant du quiz.
     * @param int $categorie_id Identifiant de la catégorie à associer.
     *
     * @return int|false  Retourne l'ID de la catégorie insérée, ou false en cas d'erreur.
     */
    public function insertQuizCategorie(int $quiz_id, int $categorie_id)
    {
        try {
            $newQuizCategorie = $this->db->prepare("INSERT INTO categorie_quiz(category_id, quiz_id) VALUES (?, ?);");
            $newQuizCategorie->bindValue(1, (int)$categorie_id);
            $newQuizCategorie->bindValue(2, $quiz_id);

            $reussite = $newQuizCategorie->execute();
            if ($reussite === false) {
                return false;
            } else {
                return $categorie_id;
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion de quiz categorie : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Insère les paramètres optionnels d'un quiz.
     *
     * Cette méthode crée un enregistrement de paramètres pour un quiz donné.
     * Les paramètres incluent : minuterie, rejeu des erreurs, ordre aléatoire,
     * affichage du score, avancement, et récapitulatif de fin.
     *
     * @param int   $quiz_id  Identifiant du quiz pour lequel insérer les paramètres.
     * @param array $params   Tableau des paramètres (indices 1-6 pour chaque paramètre booléen).
     * @param int   $timer    Durée de la minuterie en minutes (utilisé si params[1] est actif).
     *
     * @return int|false  Retourne l'ID du paramètre inséré, ou false en cas d'erreur.
     */
    public function insertQuizParams(int $quiz_id, array $params, int $timer)
    {
        try {
            $_SESSION['longueurParams'] = count($params);
            $newParams = $this->db->prepare("INSERT INTO parametreQuiz(quiz_id, minuterie , repasserErreurs, ordreAleatoire, afficherScore, afficherAvancement, recapitulatifFin)
            VALUES (?, ?, ?, ?, ?, ?, ?);");
            $newParams->bindValue(1, $quiz_id);

            if (!empty($params[1])){
                $newParams->bindValue(2, $timer);
            }
            else{
                $newParams->bindValue(2, 0);
            }
            $val = !empty($params[2]) ? 1 : 0;
            $newParams->bindValue(3, $val);
            $val = !empty($params[3]) ? 1 : 0;
            $newParams->bindValue(4, $val);
            $val = !empty($params[4]) ? 1 : 0;
            $newParams->bindValue(5, $val);
            $val = !empty($params[5]) ? 1 : 0;
            $newParams->bindValue(6, $val);
            $val = !empty($params[6]) ? 1 : 0;
            $newParams->bindValue(7, $val);

            $reussite = $newParams->execute();
            if ($reussite === false) {
                return false;
            } else {
                return $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion de paramètres de quiz : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Insère une restriction d'accès au quiz pour un ami spécifique.
     *
     * Cette méthode crée une association entre un quiz et un ami,
     * indiquant que l'ami peut accéder au quiz.
     *
     * @param int $quiz_id  Identifiant du quiz.
     * @param int $ami_id   Identifiant de l'ami autorisé à accéder au quiz.
     *
     * @return int|false  Retourne l'ID de l'ami inséré, ou false en cas d'erreur.
     */
    public function insertAmiDispo(int $quiz_id, int $ami_id)
    {
        try {
            $newAmiDispo = $this->db->prepare("INSERT INTO amiDisponibilite(quiz_id, ami_id) VALUES (?, ?);");
            $newAmiDispo->bindValue(1, $quiz_id);
            $newAmiDispo->bindValue(2, $ami_id);

            $reussite = $newAmiDispo->execute();
            if ($reussite === false) {
                return false;
            } else {
                return $ami_id;
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion d'ami dispo : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Insère une nouvelle question associée à un quiz.
     *
     * Cette méthode crée un enregistrement question avec un numéro d'ordre
     * et le contenu textuel de la question.
     *
     * @param int    $numero    Numéro d'ordre de la question au sein du quiz.
     * @param int    $quiz_id   Identifiant du quiz auquel appartient la question.
     * @param string $question  Texte de la question.
     *
     * @return int|false  Retourne l'ID de la question insérée, ou false en cas d'erreur.
     */
    public function insertQuestion(int $numero, int $quiz_id, string $question)
    {
        try {
            $newQuestion = $this->db->prepare("INSERT INTO Question (numeroQuiz, quiz_id, question) VALUES (?, ?, ?);");
            $newQuestion->bindValue(1, $numero);
            $newQuestion->bindValue(2, $quiz_id);
            $newQuestion->bindValue(3, $question);

            $reussite = $newQuestion->execute();
            if ($reussite === false) {
                return false;
            } else {
                return $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion de question : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Insère une réponse associée à une question.
     *
     * Cette méthode crée un enregistrement réponse avec le contenu textuel
     * et indique si la réponse est correcte ou non.
     *
     * @param int    $question_id  Identifiant de la question à laquelle appartient la réponse.
     * @param string $contenu      Texte de la réponse.
     * @param string $valide       État de validité de la réponse ('on' = correcte, autre = incorrecte).
     *
     * @return int|false  Retourne l'ID de la réponse insérée, ou false en cas d'erreur.
     */
    public function insertReponse(int $question_id, string $contenu, string $valide)
    {
        try {
            $newReponse = $this->db->prepare("INSERT INTO Reponse(question_id, reponse, estCorrecte) VALUES (?, ?, ?);");
            $newReponse->bindValue(1, $question_id);
            $newReponse->bindValue(2, $contenu);
            $validite = $valide == "on" ? 1 : 0;
            $newReponse->bindValue(3, $validite);

            $reussite = $newReponse->execute();
            if ($reussite === false) {
                return false;
            } else {
                return $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion de reponse : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les amis d'un utilisateur.
     *
     * Cette méthode retourne une liste de tous les amis connectés à l'utilisateur
     * spécifié, en incluant leur ID et leur nom d'utilisateur.
     *
     * @param int $user_id  Identifiant de l'utilisateur pour lequel récupérer les amis.
     *
     * @return array  Tableau de tableaux associatifs contenant 'ami_id' et 'username' pour chaque ami.
     */
    public function getAmis(int $user_id){
        $amis = $this->db->prepare("SELECT 
                                CASE 
                                WHEN user1_id = ? THEN user2_id
                                ELSE user1_id
                                END AS ami_id , username
                                FROM amis JOIN users ON ami_id = users.id 
                                WHERE ? = user1_id OR ? = user2_id;");
        $amis->bindvalue(1,$user_id);
        $amis->bindvalue(2,$user_id);
        $amis->bindvalue(3,$user_id);

        $amis->execute();

        $result = $amis->fetchAll(PDO::FETCH_ASSOC);
        return $result;
        
    }

    /**
     * Récupère la liste des catégories disponibles.
     *
     * Cette méthode retourne un tableau associatif contenant les catégories
     * (id et CategorieName) présentes dans la table `categories`.
     *
     * @return array|false  Tableau associatif des catégories, ou false en cas d'erreur.
     */
    public function getAllCategories(): mixed{
        try{
            $sql = $this->db->prepare("SELECT DISTINCT id,categorieName FROM categories;");
            $sql->execute();
            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        }catch(PDOException $e){
            die("Fetching categories failed: " . $e->getMessage());
        }catch(Exception $e){
            die("Error: " . $e->getMessage());
        }
    }
    /**
     * Retourne le nombre de questions pour un quiz.
     *
     * @param int $quizId Identifiant du quiz.
     * @return int Nombre total de questions (0 si aucune).
     */
    public function getQuizSize(int $quizId): int{
        try{
            $stmt = $this->db->prepare("SELECT COUNT(*) AS totalQuestions FROM question WHERE quiz_id = ?;");
            $stmt->execute([$quizId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($row['totalQuestions']);
        } catch (PDOException $e) {
            die("Fetching quiz size failed: " . $e->getMessage());
        }
        
    }
    
    /**
     * Récupère les informations principales d'un quiz.
     *
     * Retourne le titre, la description, la disponibilité et le genre.
     *
     * @param int $quizId Identifiant du quiz.
     * @return array|false Tableau associatif des informations ou false en cas d'erreur.
     */
    public function getQuizInfos(int $quizId){
        try{
            $quiz = $this->db->prepare("SELECT title, description, disponibilite, genre FROM quiz WHERE id = ?;");
            $quiz->bindvalue(1,$quizId);
            $quiz->execute();
            return $quiz->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Fetching quiz infos failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère toutes les questions d'un quiz avec leurs réponses.
     *
     * Le format retourné est un tableau de questions, chaque question
     * contenant un sous-tableau `reponses` avec les réponses associées
     * et `nbReponse` avec le nombre de réponses.
     *
     * @param int $idQuiz Identifiant du quiz.
     * @return array Tableau des questions et réponses.
     */
    public function getQuestionsRepFromQuiz(int $idQuiz){
        try{
            $questions = $this->db->prepare("SELECT id, question FROM Question WHERE quiz_id = ? ORDER BY numeroQuiz ASC;");
            $questions->bindValue(1,$idQuiz);
            $questions->execute();
            $TAB_QUESTIONS = $questions->fetchAll(PDO::FETCH_ASSOC);
            foreach($TAB_QUESTIONS as $index => $question){
                $reponses = $this->db->prepare("SELECT id, reponse, estCorrecte FROM Reponse WHERE question_id = ? ;");
                $reponses->bindValue(1,$question['id']);
                $reponses->execute();
                $TAB_QUESTIONS[$index]['reponses'] = $reponses->fetchAll(PDO::FETCH_ASSOC);
                $TAB_QUESTIONS[$index]['nbReponse'] = count( $TAB_QUESTIONS[$index]['reponses']);
            }
            return $TAB_QUESTIONS;
        } catch(PDOException $e){
            die("Fetching questions and answers from quiz failed: " . $e->getMessage());
        }
        
    }

    /**
     * Récupère les catégories associées à un quiz.
     *
     * @param int $idQuiz Identifiant du quiz.
     * @return array Tableau associatif contenant les catégories (id, categorieName).
     */
    public function getCategoriesFromQuiz(int $idQuiz){
        try{
            $categories = $this->db->prepare("SELECT categories.id, categories.categorieName FROM categories 
            INNER JOIN categorie_quiz ON categories.id = categorie_quiz.category_id WHERE categorie_quiz.quiz_id = ?;");
            $categories->bindValue(1,$idQuiz);
            $categories->execute();
            return $categories->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e){
            die("Fetching categories from quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère les paramètres d'un quiz.
     *
     * Retourne un tableau indexé représentant les paramètres
     * (minuterie, repasserErreurs, ordreAleatoire, afficherScore,
     * afficherAvancement, recapitulatifFin). Si aucun paramètre n'est
     * défini, retourne un tableau de six zéros.
     *
     * @param int $idQuiz Identifiant du quiz.
     * @return array Tableau de paramètres.
     */
    public function getQuizParametres(int $idQuiz){
        try{
            $params = $this->db->prepare("SELECT minuterie, repasserErreurs, ordreAleatoire, afficherScore, afficherAvancement, recapitulatifFin FROM parametreQuiz WHERE quiz_id = ?;");
            $params->bindValue(1,$idQuiz);
            $params->execute();
            $parametres = $params->fetch(PDO::FETCH_ASSOC);
            
            if (!$parametres){
                $TAB_PARAMS = array(0,0,0,0,0,0);
            }
            else{
                $TAB_PARAMS = array();
                $TAB_PARAMS[0] = $parametres['minuterie'];
                $TAB_PARAMS[1] = $parametres['repasserErreurs'];
                $TAB_PARAMS[2] = $parametres['ordreAleatoire'];
                $TAB_PARAMS[3] = $parametres['afficherScore'];
                $TAB_PARAMS[4] = $parametres['afficherAvancement'];
                $TAB_PARAMS[5] = $parametres['recapitulatifFin'];
            }
            return $TAB_PARAMS;
        } catch(PDOException $e){
            die("Fetching quiz parameters failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère la liste des amis autorisés pour un quiz.
     *
     * Retourne un tableau d'identifiants d'amis (entiers).
     *
     * @param int $quiz_id Identifiant du quiz.
     * @return array Tableau d'IDs d'amis.
     */
    public function getAmisSelection(int $quiz_id){
        try{
            $amis = $this->db->prepare("SELECT ami_id FROM amiDisponibilite WHERE quiz_id = ?;");
            $amis->bindvalue(1,$quiz_id);
            $amis->execute();
            $result = $amis->fetchAll(PDO::FETCH_ASSOC);
            $TAB_AMIS = array();
            foreach($result as $ami){
                $TAB_AMIS[] = $ami['ami_id'];
            }
            return $TAB_AMIS;
        } catch (PDOException $e) {
            die("Fetching selected friends failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère l'identifiant de l'utilisateur propriétaire d'un quiz.
     *
     * @param int $quiz_id Identifiant du quiz.
     * @return int|false ID de l'utilisateur ou false si non trouvé.
     */
    public function getUserIdFromQuiz(int $quiz_id): int{
        try{
            $quiz = $this->db->prepare("SELECT user_id FROM quiz WHERE id = ?;");
            $quiz->bindvalue(1,$quiz_id);
            $quiz->execute();
            $result = $quiz->fetch(PDO::FETCH_ASSOC);
            if(!empty($result)){
                return (int)$result['user_id'];
            }
            else{
                return false;
            }
        } catch (PDOException $e) {
            die("Fetching user ID from quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour les catégories associées à un quiz.
     *
     * Supprime d'abord les associations existantes puis insère
     * les nouvelles catégories fournies.
     *
     * @param int   $quiz_id    Identifiant du quiz.
     * @param array $categories Tableau d'IDs de catégories.
     * @return bool True si la mise à jour réussit.
     */
    public function updateCategoriesQuiz(int $quiz_id, array $categories){
        try{
            $delete = $this->db->prepare("DELETE FROM categorie_quiz WHERE quiz_id = ?;");
            $delete->bindValue(1,$quiz_id);
            $delete->execute();
            foreach($categories as $categorie){
                $this->insertQuizCategorie($quiz_id, (int)$categorie);
            }
            return true;
        } catch(PDOException $e){
            die("Updating categories for quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour la disponibilité d'un quiz et les amis autorisés.
     *
     * Si la disponibilité est 'ami', insère les enregistrements dans
     * `amiDisponibilite` pour chaque ami fourni.
     *
     * @param int    $quiz_id       Identifiant du quiz.
     * @param string $disponibilite Nouvelle disponibilité ('public','ami',...).
     * @param array  $amis          Tableau d'IDs d'amis (entiers).
     * @return bool True si la mise à jour réussit.
     */
    public function updateDisponibiliteQuiz(int $quiz_id, string $disponibilite, array $amis){
        try{
            $delete = $this->db->prepare("DELETE FROM amiDisponibilite WHERE quiz_id = ?;");
            $delete->bindValue(1,$quiz_id);
            $delete->execute();
            $update = $this->db->prepare("UPDATE quiz SET disponibilite = ? WHERE id = ?;");
            $update->bindValue(1,$disponibilite);
            $update->bindValue(2,$quiz_id);
            $update->execute();
            if ($disponibilite == 'ami'){
                foreach($amis as $ami){
                    $this->insertAmiDispo($quiz_id, (int)$ami);
                }
            }
            return true;
        } catch(PDOException $e){
            die("Updating disponibilite for quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour le contenu d'une question d'un quiz et ses réponses.
     *
     * Met à jour le texte de la question identifié par `quiz_id` et
     * `numeroQuiz` puis délègue la mise à jour des réponses à
     * `updateReponses`.
     *
     * @param int   $quizId          ID du quiz.
     * @param int   $numeroQuiz      Numéro de la question dans le quiz.
     * @param string $questionContent Nouveau texte de la question.
     * @param array $reponsesContent  Tableau des textes de réponses.
     * @param array $checksContent    Tableau des indicateurs de validité (0/1).
     * @return bool True si la mise à jour réussit.
     */
    public function updateQuestionQuiz(int $quizId, int $numeroQuiz, string $questionContent, array $reponsesContent, array $checksContent){
        try{
            $updateQuestion = $this->db->prepare("UPDATE question SET question = ? WHERE quiz_id = ? AND numeroQuiz = ? RETURNING id;");
            $updateQuestion->bindValue(1,$questionContent);
            $updateQuestion->bindValue(2,$quizId);
            $updateQuestion->bindValue(3,$numeroQuiz);
            $updateQuestion->execute();

            $idQuestion = $updateQuestion->fetchColumn();

            $this->updateReponses($idQuestion, $reponsesContent, $checksContent);
            return true;

            
        } catch(PDOException $e){
            die("Updating question in quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour les réponses existantes d'une question et en ajoute de nouvelles.
     *
     * Pour chaque réponse existante, met à jour ou supprime si le nouveau
     * tableau ne contient plus d'entrée. Ajoute ensuite les réponses
     * supplémentaires présentes dans `$reponsesContent`.
     *
     * @param int   $questionId      ID de la question.
     * @param array $reponsesContent Tableau des textes de réponses.
     * @param array $checksContent   Tableau des indicateurs de validité (0/1 ou 'on').
     * @return bool True si la mise à jour réussit.
     */
    public function updateReponses(int $questionId, array $reponsesContent, array $checksContent){
        try{
            $getReponses = $this->db->prepare("SELECT id FROM reponse WHERE question_id = ? ORDER BY id ASC;");
            $getReponses->bindValue(1,$questionId);
            $getReponses->execute();
            $existingReponses = $getReponses->fetchAll(PDO::FETCH_ASSOC);
            $nbRep = count($existingReponses);
            foreach($existingReponses as $index => $reponse){
                if(count($reponsesContent) <= $index){
                    $deleteReponse = $this->db->prepare("DELETE FROM reponse WHERE id = ?;");
                    $deleteReponse->bindValue(1,$reponse['id']);
                    $deleteReponse->execute();
                    continue;
                }
                $updateReponse = $this->db->prepare("UPDATE reponse SET reponse = ?, estCorrecte = ? WHERE id = ?;");
                $updateReponse->bindValue(1,$reponsesContent[$index]);
                $updateReponse->bindValue(2,(int)$checksContent[$index]);
                $updateReponse->bindValue(3,$reponse['id']);
                $updateReponse->execute();
            }
            foreach(array_slice($reponsesContent, count($existingReponses)) as $index => $reponse){
                $valid = (int)$checksContent[count($existingReponses)+$index] == 1 ? "on" : "";
                $this->insertReponse($questionId, $reponse, $valid);
            }
            return true;
        } catch(PDOException $e){
            die("Updating responses in quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Ajoute une nouvelle question au quiz avec ses réponses.
     *
     * Insère la question avec `numeroQuiz` fourni puis insère
     * chacune des réponses associées.
     *
     * @param int $quizId ID du quiz.
     * @param int $numQuestion Numéro de la question à ajouter.
     * @param string $questionContent Texte de la question.
     * @param array $reponsesContent Textes des réponses.
     * @param array $checksContent Indicateurs de validité (0/1).
     * @return bool True si l'ajout réussit.
     */
    public function addQuestionToQuiz(int $quizId,int $numQuestion, string $questionContent, array $reponsesContent, array $checksContent){
        try{
            $newQuestionId = $this->insertQuestion($numQuestion, $quizId, $questionContent);
            foreach($reponsesContent as $index => $reponse){
                $valid = (int)$checksContent[$index] == 1 ? "on" : "";
                $this->insertReponse($newQuestionId, $reponse, $valid);
            }
            return true;
        } catch(PDOException $e){
            die("Adding question to quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour les paramètres d'un quiz existant.
     *
     * Met à jour l'enregistrement `parametreQuiz` correspondant au quiz
     * avec les valeurs fournies.
     *
     * @param int $quizId Identifiant du quiz.
     * @param array $params Tableau des paramètres (indices 1-6 pour chaque flag).
     * @param int $timer Durée de la minuterie (minutes).
     * @return bool True si la mise à jour réussit.
     */
    public function updateParametresQuiz(int $quizId, array $params, int $timer){
        try{
            $updateParams = $this->db->prepare("UPDATE parametreQuiz SET minuterie = ?, repasserErreurs = ?, ordreAleatoire = ?, afficherScore = ?, afficherAvancement = ?, recapitulatifFin = ? WHERE quiz_id = ?;");
            if (!empty($params[0])){
                $updateParams->bindValue(1, $timer);
            }
            else{
                $updateParams->bindValue(1, 0);
            }
            $val = !empty($params[1]) ? 1 : 0;
            $updateParams->bindValue(2, $val);
            $val = !empty($params[2]) ? 1 : 0;
            $updateParams->bindValue(3, $val);
            $val = !empty($params[3]) ? 1 : 0;
            $updateParams->bindValue(4, $val);
            $val = !empty($params[4]) ? 1 : 0;
            $updateParams->bindValue(5, $val);
            $val = !empty($params[5]) ? 1 : 0;
            $updateParams->bindValue(6, $val);
            $updateParams->bindValue(7, $quizId);

            $updateParams->execute();
            return true;
        } catch(PDOException $e){
            die("Updating quiz parameters failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour le genre d'un quiz.
     *
     * @param int $quizId ID du quiz.
     * @param string $genre Nouveau genre.
     * @return bool True si la mise à jour réussit.
     */
    public function updateGenreQuiz(int $quizId, string $genre){
        try{
            $updateGenre = $this->db->prepare("UPDATE quiz SET genre = ? WHERE id = ?;");
            $updateGenre->bindValue(1,$genre);
            $updateGenre->bindValue(2,$quizId);
            $updateGenre->execute();
            return true;
        } catch(PDOException $e){
            die("Updating quiz genre failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour le titre et la description (résumé) d'un quiz.
     *
     * @param int $quizId ID du quiz.
     * @param string $title Nouveau titre.
     * @param string $description Nouvelle description.
     * @return bool True si la mise à jour réussit.
     */
    public function updateResumQuiz(int $quizId, string $title, string $description){
        try{
            $updateResum = $this->db->prepare("UPDATE quiz SET title = ?, description = ? WHERE id = ?;");
            $updateResum->bindValue(1,$title);
            $updateResum->bindValue(2,$description);
            $updateResum->bindValue(3,$quizId);
            $updateResum->execute();
            return true;
        } catch(PDOException $e){
            die("Updating quiz resum failed: " . $e->getMessage());
        }
    }

    /**
     * Supprime une question d'un quiz identifiée par son numéro.
     *
     * Recherche l'ID de la question correspondant à `quiz_id` et
     * `numeroQuiz` puis supprime l'enregistrement si trouvé.
     *
     * @param int $quizId ID du quiz.
     * @param int $numeroQuiz Numéro de la question à supprimer.
     * @return bool True si l'opération réussit.
     */
    public function deleteQuestionFromQuiz(int $quizId, int $numeroQuiz){
        try{
            $getQuestion = $this->db->prepare("SELECT id FROM question WHERE quiz_id = ? AND numeroQuiz = ?;");
            $getQuestion->bindValue(1,$quizId);
            $getQuestion->bindValue(2,$numeroQuiz);
            $getQuestion->execute();
            $question = $getQuestion->fetch(PDO::FETCH_ASSOC);
            if ($question){
                $deleteQuestion = $this->db->prepare("DELETE FROM question WHERE id = ?;");
                $deleteQuestion->bindValue(1,$question['id']);
                $deleteQuestion->execute();
      
            }
            return true;
        } catch(PDOException $e){
            die("Deleting question from quiz failed: " . $e->getMessage());
        }
    }
}
