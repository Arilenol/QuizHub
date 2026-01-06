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

        // Gérer les actions POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                if ($_POST['action'] === 'update_quiz') {
                    $title = $_POST['title'];
                    $description = $_POST['description'];
                    $difficulty = (int)$_POST['difficulty'];
                    $this->model->updateQuiz($quiz_id, $title, $description, $difficulty, $quiz['genre']);
                    header("Location: ?page=CRUDquiz&id=$quiz_id");
                    exit;
                } elseif ($_POST['action'] === 'delete_quiz') {
                    $this->model->deleteQuiz($quiz_id);
                    header("Location: ?page=CRUD");
                    exit;
                } elseif ($_POST['action'] === 'update_question') {
                    $question_id = (int)$_POST['question_id'];
                    $enonce = $_POST['enonce'];
                    $this->model->updateQuestion($question_id, $enonce);
                    header("Location: ?page=CRUDquiz&id=$quiz_id");
                    exit;
                } elseif ($_POST['action'] === 'delete_question') {
                    $question_id = (int)$_POST['question_id'];
                    $this->model->deleteQuestion($question_id);
                    header("Location: ?page=CRUDquiz&id=$quiz_id");
                    exit;
                } elseif ($_POST['action'] === 'update_answer') {
                    $answer_id = (int)$_POST['answer_id'];
                    $texte = $_POST['texte'];
                    $est_correct = isset($_POST['est_correct']) ? 1 : 0;
                    $this->model->updateAnswer($answer_id, $texte, $est_correct);
                    header("Location: ?page=CRUDquiz&id=$quiz_id");
                    exit;
                } elseif ($_POST['action'] === 'delete_answer') {
                    $answer_id = (int)$_POST['answer_id'];
                    $this->model->deleteAnswer($answer_id);
                    header("Location: ?page=CRUDquiz&id=$quiz_id");
                    exit;
                }
            }
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