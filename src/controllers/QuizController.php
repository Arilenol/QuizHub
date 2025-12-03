<?php
require_once ROOT . '/src/models/QuizModel.php';
require_once ROOT . '/config/config.php';

class QuizController
{

    private QuizModel $model;

    public function __construct()
    {
        $db = getDbConnection();
        $this->model = new QuizModel($db);
    }

    public function createQuiz()
    {
        // show all errors while developing this flow
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $db = getDbConnection();
        $model = new QuizModel($db);

        session_start();

        //unset($_POST);
        //unset($_SESSION);



        if (isset($_SESSION['id'])) {
            $id = $_SESSION['id'];
        } else {
            header('Location: index.php?page=home');
            exit;
        }
        if (!isset($_SESSION['POST'])) {
            $_SESSION['POST'] = [];
        }
        if (!isset($_SESSION['bouton'])) {
            $_SESSION['bouton'] = false;
        }

        

        if (!isset($_SESSION['nbQuestions']) || empty($_SESSION['nbQuestions'])) {
            $_SESSION['nbQuestions'] = 1;
        }
        if (!isset($_SESSION['nbReponse']) || empty($_SESSION['nbReponse'])) {
            // array where each index is number of answers for that question
            $_SESSION['nbReponse'] = array(0 => 2);
        }
        //var_dump($_POST);


        // Handle form actions: Retour, addQuestion, addReponse, DelQuestion, delReponseX
        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes") {
            unset($_SESSION['bouton']);
            unset($_SESSION['nbReponse']);
            unset($_SESSION['nbQuestions']);
            unset($_SESSION['POST']);
            unset($_POST);
            // redirect back to content creation or other appropriate page
            header('Location: index.php?page=createContent');
            exit;
        }

        if (isset($_POST['addQuestion']) && $_POST['addQuestion'] === 'yes') {
            $_SESSION['nbQuestions']++;
            // new question starts with minimum allowed responses
            $_SESSION['nbReponse'][$_SESSION['nbQuestions'] - 1] = 2;
            // Sauvegarde complète du formulaire

            unset($_POST['addQuestion']);
            $this->contentFusionSessionPost();

            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        if (isset($_POST['addReponse']) && $_POST['addReponse'] !== '') {
            $_SESSION['nbReponse'][(int)$_POST['addReponse']]++;

            unset($_POST['addReponse']);
            $this->contentFusionSessionPost();

            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        // Delete a question (shift subsequent questions up)
        if (isset($_POST['DelQuestion']) && $_POST['DelQuestion'] !== '') {
            if ($_SESSION['nbQuestions'] > 1) {
                $idx = (int)$_POST['DelQuestion'];
                $oldNb = $_SESSION['nbQuestions'];
                for ($i = $idx; $i < $oldNb - 1; $i++) {
                    $_SESSION['nbReponse'][$i] = $_SESSION['nbReponse'][$i + 1];
                    $_POST['question' . $i] = $_POST['question' . ($i + 1)];

                    for ($k = 0; $k < $_SESSION['nbReponse'][$i]; $k++) {
                        $_POST['reponse' . $k . '-question' . $i] = $_POST['reponse' . $k . '-question' . ($i + 1)];
                        $_POST['checkbox' . $k . '-question' . $i] = $_POST['checkbox' . $k . '-question' . ($i + 1)];
                    }
                }

                $last = $oldNb - 1;
                unset($_SESSION['nbReponse'][$last]);
                unset($_POST['question' . $last]);
                for ($k = 0; $k < 10; $k++) {
                    unset($_POST['reponse' . $k . '-question' . $last]);
                    unset($_POST['checkbox' . $k . '-question' . $last]);
                }
                $_SESSION['nbQuestions']--;
            }
            unset($_POST['DelQuestion']);
            $this->contentFusionSessionPost();

            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        for ($i = 0; $i < $_SESSION['nbQuestions']; $i++) {
            if (isset($_POST['delReponse' . $i]) && $_POST['delReponse' . $i] == "yes") {
                if ($_SESSION['nbReponse'][$i] > 2) {
                    $_SESSION['nbReponse'][$i]--;
                    unset($_POST['reponse' . $_SESSION['nbReponse'][$i] . '-question' . $i]);
                    unset($_POST['nbReponse'][$_SESSION['nbReponse'][$i]]);
                }
                unset($_POST['delReponse' . $i]);
                $this->contentFusionSessionPost();

                $_SESSION['bouton'] = true;
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
        if ($_SESSION['bouton'] === false) {
            $this->contentFusionSessionPost();
        }


        $quizTitle = isset($_SESSION['POST']['QuizTitle']) ? $_SESSION['POST']['QuizTitle'] : '';
        $desc = isset($_SESSION['POST']['QuizDescription']) ? $_SESSION['POST']['QuizDescription'] : '';

        $TAB_CONTENU = array();
        for ($i = 0; $i < $_SESSION['nbQuestions']; $i++) {
            $qContent = array(
                'name' => isset($_SESSION['POST']['question' . $i]) ? $_SESSION['POST']['question' . $i] : '',
                'reponses' => array()
            );
            for ($k = 0; $k < $_SESSION['nbReponse'][$i]; $k++) {
                $reponseContent = array(
                    'texte' => isset($_SESSION['POST']['reponse' . $k . '-question' . $i]) ? $_SESSION['POST']['reponse' . $k . '-question' . $i] : '',
                    'valide' => isset($_SESSION['POST']['checkbox' . $k . '-question' . $i]) ? $_SESSION['POST']['checkbox' . $k . '-question' . $i] : 0
                );
                $qContent['reponses'][] = $reponseContent;
            }
            $TAB_CONTENU[] = $qContent;
        }



        //------------informations à remplir---------------------
        $tabParametres = array(
            array('name' => 'minuterie'),
            array('name' => 'rééssayer les questions échouées'),
            array('name' => 'faire dans le désordre par défaut'),
            array('name' => 'afficher le score')
        );
        //------------informations à remplir---------------------

        if (isset($_POST['create']) && $_POST['create'] === 'yes') {
            // basic validation: ensure a title is provided
            if (isset($_POST['QuizTitle']) && !empty($_POST['QuizTitle'])) {
                $quizTitle = $_POST['QuizTitle'];
                $desc = isset($_POST['QuizDescription']) ? $_POST['QuizDescription'] : '';
                $model->createQuiz($id, $tabParametres, $TAB_CONTENU, $desc, $quizTitle, $_SESSION['nbQuestions'], $_SESSION['nbReponse']);
                unset($_SESSION['bouton']);
                unset($_SESSION['nbReponse']);
                unset($_SESSION['nbQuestions']);
                unset($_SESSION['POST']);
                unset($_POST);
                header('Location: index.php?page=home');
                exit;
            }
        }

        $_SESSION['bouton'] = false;

        //var_dump($_SESSION['POST']);
        //var_dump($_POST);
        //var_dump($TAB_CONTENU);
        //unset($_SESSION['POST']);
        require ROOT . '/src/views/Quiz/createQuiz.php';
    }

    public function contentFusionSessionPost()
    {
        if (isset($_POST['QuizTitle'])){
            $_SESSION['POST']['QuizTitle'] = $_POST['QuizTitle'];
        }
        if (isset($_POST['QuizDescription'])){
            $_SESSION['POST']['QuizDescription'] = $_POST['QuizDescription'];
        }
        for ($i = 0; $i < $_SESSION['nbQuestions']; $i++) {
            for ($k = 0; $k < $_SESSION['nbReponse'][$i]; $k++) {
                if (isset($_POST['question' . $i])) {
                    $_SESSION['POST']['question' . $i] = $_POST['question' . $i];
                }
                if (isset($_POST['reponse' . $k . '-question' . $i])) {
                    $_SESSION['POST']['reponse' . $k . '-question' . $i] = $_POST['reponse' . $k . '-question' . $i];
                }
                if (isset($_POST['checkbox' . $k . '-question' . $i])) {
                    $_SESSION['POST']['checkbox' . $k . '-question' . $i] = 1;
                } else {
                    $_SESSION['POST']['checkbox' . $k . '-question' . $i] = 0;
                }
            }
        }
    }

    public function showQuiz(int $quizId, ?int $idQuestion = 1, bool $showAnswer = false)
    {
        if (session_status() === PHP_SESSION_NONE && ($_GET['page'] === 'test')) {
            session_start();
        }

        $max = $this->model->getMaxNbQuestion($quizId);

        // initialisation des réponses si vide
        if (!isset($_SESSION['answers'])) {
            $_SESSION['answers'] = [];
        }

        // si utilisateur a envoyé une réponse
        if (!empty($_GET['answer'])) {

            $answers = array_map('intval', $_GET['answer']);

            // on stocke pour LA QUESTION COURANTE
            $_SESSION['answers'][$idQuestion] = array($this->isCorrect($quizId, $idQuestion, $answers), $answers);

            // on passe à la question suivante
            $idQuestion++;
        }

        // fin du quiz
        if ($idQuestion > $max) {
            $question = null;
            $reponse = [];

            if ($_GET['page'] === 'standard') {
                require ROOT . '/src/views/quiz/show.php';
            } else {
                ksort($_SESSION['answers']);
                require ROOT . '/src/views/quiz/endTest.php';
            }
            return;
        }
        // récupération de la question actuelle
        $question = $this->model->getQuestion($quizId, $idQuestion);
        $reponse  = $this->model->getReponses($question['id']);

        require ROOT . '/src/views/quiz/show.php';
    }


    public function isCorrect(int $quizId, int $idQuestion, array $reponses): bool
    {
        // Récupère les bonnes réponses sous forme de tableau d'IDs
        $correctAnswers = $this->model->getCorrectAnswers($quizId, $idQuestion);

        // Normaliser les réponses de l'utilisateur en entiers
        $userIds = array_map('intval', $reponses);

        // Trier les deux tableaux pour comparer indépendamment de l'ordre
        sort($correctAnswers);
        sort($userIds);

        // Retourner true si les deux tableaux sont identiques
        return $correctAnswers === $userIds;
    }
}
