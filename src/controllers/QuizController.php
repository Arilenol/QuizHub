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

    public function showQuiz(int $quizId, int $idQuestion = 1, bool $showAnswer = false)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        $max = $this->model->getMaxNbQuestion($quizId);

        // Initialisation au début du quiz (juste une fois)
        if (!isset($_SESSION['answers'])) {
            $_SESSION['answers'] = [];
        }

        // Stockage de la réponse envoyée par GET
        if (!empty($_GET['answer'])) {

            // Stocker la réponse associée à la question actuelle
            // answer[] -> checkbox → on garde le tableau tel quel
            $_SESSION['answers'][$idQuestion] = [
                $this->isCorrect($idQuestion, $_GET['answer']),
                $_GET['answer']
            ];
        }

        // Fin du quiz
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

        // Question + réponses
        $question = $this->model->getQuestion($quizId, $idQuestion);
        $reponse  = $this->model->getReponses($question['id']);

        require ROOT . '/src/views/quiz/show.php';
    }

    public function isCorrect(int $idQuestion, array $reponses): bool
    {
        // Récupère les bonnes réponses (tableaux associatifs)
        $correctAnswers = $this->model->getCorrectAnswers($idQuestion);

        // Extraire uniquement les IDs et normaliser en entiers
        $correctIds = array_map('intval', array_column($correctAnswers, 'id'));

        // Normaliser les réponses utilisateurs en entiers (au cas où)
        $userIds = array_map('intval', $reponses);

        // Trier les deux tableaux pour comparer l'ordre indépendamment
        sort($correctIds);
        sort($userIds);

        return $correctIds === $userIds;
    }
}
