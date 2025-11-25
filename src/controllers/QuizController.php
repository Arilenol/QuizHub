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
        $test = $this->envoie($quizId, $idQuestion);
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

    public function envoie(int $quizId, int $idQuestion): array
    {
        // Récupère les bonnes réponses sous forme de tableau d'IDs
        $correctAnswers = $this->model->getCorrectAnswers($quizId, $idQuestion);

        return $correctAnswers;
    }
}
