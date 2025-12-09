<?php
require_once ROOT . '/src/models/CatalogueModel.php';
require_once ROOT . '/config/config.php';


class CatalogueController
{
    private $model;

    public function index()
    {
        $db = getDbConnection();
        //constructionBD($db);
        $this->model = new CatalogueModel($db);
        // afficher la vue


        $cats = $this->model->getCategories();

        $options = [
            'date_desc' => 'Date (nouveau → ancien)',
            'date_asc' => 'Date (ancien → nouveau)',
            'title_asc' => 'Titre (A → Z)',
            'title_desc' => 'Titre (Z → A)',
            'difficulty_asc' => 'Difficulté (faible → élevé)',
            'difficulty_desc' => 'Difficulté (élevé → faible)',
            'author_asc' => "Auteur (A → Z)",
            'author_desc' => "Auteur (Z → A)",
            'genre_asc' => 'Genre (A → Z)',
            'genre_desc' => 'Genre (Z → A)'
        ];

        $recherche_cat = isset($_GET['categorie']) && !empty($_GET['categorie']) && htmlspecialchars($_GET['categorie']) != 'Toutes les catégories' ? $_GET['categorie'] : '';
        $recherche_contenu = isset($_GET['contenu']) && !empty($_GET['contenu']) ? htmlspecialchars($_GET['contenu']) : '';
        $recherche_auteur = isset($_GET['searchAuthor']) && !empty($_GET['searchAuthor']) ? htmlspecialchars($_GET['searchAuthor']) : '';
        $tri = isset($_GET['tri']) && !empty($_GET['tri']) ? $_GET['tri'] : null;
        if ($recherche_cat != '') {
            $quiz_correspondants = $this->model->searchQuizByAll($recherche_cat, $recherche_contenu, $recherche_auteur, $tri);
        } else {
            $quiz_correspondants = $this->model->searchQuizByContentAndAuthor($recherche_contenu, $recherche_auteur, $tri);
        }

        $quizzes = $quiz_correspondants;
        foreach ($quizzes as $index => $quiz) {
            $quizzes[$index]['categories'] = $this->model->getCategoriesFromQuiz($quiz['id']);
            $quizzes[$index]['nom_auteur'] = $this->model->getNomAuteur($quiz['user_id']);

            $quizzes[$index]['likes'] = isset($quiz['likes']) ? (int)$quiz['likes'] : 0;
            $quizzes[$index]['dislikes'] = isset($quiz['dislikes']) ? (int)$quiz['dislikes'] : 0;
        }
        require ROOT . '/src/views/catalogue.php';
    }
}
