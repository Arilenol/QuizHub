<?php
require_once ROOT . '/src/models/QuizModel.php';
require_once ROOT . '/config/config.php';

class FlashCardController {
    public function index($id) {
        $db = getDbConnection();
        $model = new QuizModel($db);
        // récupère les différents ID des questions de la flashcard et les mets en session
        $_SESSION['remainingQuestion'] = $model->getFlashCardById($id);
        require ROOT . '/src/views/flashcard.php';
    }
}

?>