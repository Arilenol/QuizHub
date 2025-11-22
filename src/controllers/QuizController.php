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

    public function showQuiz(int $id, int $idQuestion = 1, bool $showAnswer = false)
    {
        $id = htmlspecialchars($id);
        $idQuestion = htmlspecialchars($idQuestion);

        $max = $this->model->getMaxNbQuestion($id);

        // Fin du quiz
        if ($idQuestion > $max) {
            $question = null;
            $reponse = [];
            require ROOT . '/src/views/quiz/show.php';
            return;
        }

        // Récupère question + réponses
        $showAnswer = $showAnswer;
        $question = $this->model->getQuestion($id, $idQuestion);
        $reponse  = $this->model->getReponses($question['id']);

        require ROOT . '/src/views/quiz/show.php';
    }
}
