<?php
require_once ROOT . '/src/models/FlashCardModel.php';
require_once ROOT . '/config/config.php';

class FlashCardController
{
    private FlashCardModel $model;

    public function __construct()
    {
        session_start();
        $db = getDbConnection();
        $this->model = new FlashCardModel($db);
    }

    // Charge la première question du quiz
    public function preload(int $quizId)
    {
        $_SESSION['remainingQuestions'] = $this->model->getFlashCardById($quizId) ?: [];
        if (empty($_SESSION['remainingQuestions'])) {
            echo "Aucune question disponible";
            return;
        }
        $firstId = $_SESSION['remainingQuestions'][0];
        $this->showQuestion($firstId);
    }

    // Affiche une question spécifique
    public function questionById(int $id)
    {
        $remaining = $_SESSION['remainingQuestions'] ?? [];
        if (!in_array($id, $remaining)) {
            echo "Question invalide";
            return;
        }

        $this->showQuestion($id);
    }
    // Méthode privée pour centraliser l’affichage
    private function showQuestion(int $id)
    {
        $question = $this->model->getInfoFlashCardById($id);
        $viewData = $this->prepareViewData($question);
        require ROOT . '/src/views/quiz/flashcard.php';
    }

    private function prepareViewData(array $question): array
    {
        $remaining = $_SESSION['remainingQuestions'] ?? [];
        $currentIndex = array_search($question['id'], $remaining);

        return [
            'question'   => $question,
            'quizId'     => $question['quiz_id'],
            'showAnswer' => ($_GET['reponse'] ?? '') === 'visible',
            'prevId'     => $remaining[$currentIndex - 1] ?? null,
            'nextId'     => $remaining[$currentIndex + 1] ?? null,
        ];
    }
}
