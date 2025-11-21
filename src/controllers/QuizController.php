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

    public function showQuiz(int $id, ?int $idQuestion = 1)
    {
        if ($idQuestion <= $this->model->getMaxNbQuestion(htmlspecialchars($id))) {
            $question = $this->model->getQuestion(htmlspecialchars($id), htmlspecialchars($idQuestion));
            $reponse = $this->model->getReponses($question['id']);
        }
        require ROOT . '/src/views/quiz/show.php';
    }
}
