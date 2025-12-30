<?php
require_once ROOT . '/src/models/CRUDModel.php';
require_once ROOT . '/config/config.php';

class CRUDAuteurController
{
    private $model;

    public function index()
    {
        $db = getDbConnection();
        $this->model = new CRUDModel($db);

        // Récupérer l'ID de l'auteur depuis les paramètres GET
        $author_id = isset($_GET['id']) && !empty($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($author_id === 0) {
            die("ID auteur invalide");
        }

        // Récupérer les infos de l'auteur (nom)
        $author_name = $this->model->getNomAuteur($author_id);
        if (!$author_name) {
            die("Auteur non trouvé");
        }

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
