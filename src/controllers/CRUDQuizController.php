<?php
require_once ROOT . '/src/models/CRUDQuizModel.php';
require_once ROOT . '/config/config.php';

class CRUDQuizController {
    private $model;

    public function index() {
        $db = getDbConnection();
        $this->model = new CRUDQuizModel($db);

        // Récupérer l'ID du quiz depuis les paramètres GET
        $quiz_id = isset($_GET['id']) && !empty($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($quiz_id === 0) {
            die("ID quiz invalide");
        }

        // Récupérer les infos du quiz
        $quiz = $this->model->getQuizInfo($quiz_id);
        
        if (!$quiz) {
            die("Quiz non trouvé");
        }

        // Récupérer les catégories
        $quiz['categories'] = $this->model->getCategoriesFromQuiz($quiz_id);
        
        // Récupérer le nom de l'auteur
        $quiz['nom_auteur'] = $this->model->getNomAuteur($quiz['user_id']);

        // Récupérer toutes les questions du quiz
        $questions = $this->model->getQuizQuestions($quiz_id);

        // Pour chaque question, récupérer les réponses
        $questionsWithAnswers = [];
        foreach ($questions as $question) {
            $question['answers'] = $this->model->getQuestionAnswers($question['id']);
            $questionsWithAnswers[] = $question;
        }

        // Passer les données à la vue
        require ROOT . '/src/views/CRUD/CRUDquiz.php';
    }
}
?>