<?php
require_once ROOT . '/src/models/CRUDModel.php';
require_once ROOT . '/config/config.php';

class CRUDAuteurController
{
    private $model;

    public function index()
    {
        // Vérifier les droits d'accès admin
        requireAdmin();
        
        $db = getDbConnection();
        $this->model = new CRUDModel($db);

        // Récupérer l'ID de l'auteur depuis les paramètres GET
        $author_id = isset($_GET['id']) && !empty($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($author_id === 0) {
            die("ID auteur invalide");
        }

        // Gérer les actions POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                if ($_POST['action'] === 'update') {
                    $username = $_POST['username'];
                    $email = $_POST['email'];
                    $description = $_POST['description'];
                    $this->model->updateAuthor($author_id, $username, $email, $description);
                    // Rediriger pour éviter re-soumission
                    header("Location: ?page=CRUDauteur&id=$author_id");
                    exit;
                } elseif ($_POST['action'] === 'delete') {
                    $this->model->deleteAuthor($author_id);
                    // Rediriger vers la recherche
                    header("Location: ?page=CRUD");
                    exit;
                }
            }
        }

        // Récupérer les infos complètes de l'auteur
        $author_info = $this->model->getAuthorInfo($author_id);
        if (!$author_info) {
            die("Auteur non trouvé");
        }

        $author_name = $author_info['username'];

        // Récupérer tous les quizzes de cet auteur
        $quizzes = $this->model->getQuizzesByAuthor($author_id);

        // Enrichir chaque quiz avec ses catégories et autres infos
        foreach ($quizzes as $index => $quiz) {
            $quizzes[$index]['categories'] = $this->model->getCategoriesFromQuiz($quiz['id']);
            $quizzes[$index]['nom_auteur'] = $author_name;
            $quizzes[$index]['likes'] = isset($quiz['nbjaime']) ? (int)$quiz['nbjaime'] : 0;
            $quizzes[$index]['dislikes'] = isset($quiz['nbjaimepas']) ? (int)$quiz['nbjaimepas'] : 0;
        }

        // Passer les données à la vue
        require ROOT . '/src/views/CRUD/CRUDauteur.php';
    }
}
