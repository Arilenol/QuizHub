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
        session_start();

        $cats = $this->model->getCategories();

        if (session_status() === PHP_SESSION_NONE || !isset($_SESSION['id']) || $_SESSION['id'] === null) {
            $options = [
                'date_desc' => 'Date (nouveau → ancien)',
                'date_asc' => 'Date (ancien → nouveau)',
                'title_asc' => 'Titre (A → Z)',
                'title_desc' => 'Titre (Z → A)',
                'author_asc' => "Auteur (A → Z)",
                'author_desc' => "Auteur (Z → A)",
                'genre_asc' => 'Genre (A → Z)',
                'genre_desc' => 'Genre (Z → A)',
                'popup_asc' => 'Popularité (faible → élevé)',
                'popup_desc' => 'Popularité (élevé → faible)',
            ];
        } else {
            $options = [
                'date_desc' => 'Date (nouveau → ancien)',
                'date_asc' => 'Date (ancien → nouveau)',
                'title_asc' => 'Titre (A → Z)',
                'title_desc' => 'Titre (Z → A)',
                'author_asc' => "Auteur (A → Z)",
                'author_desc' => "Auteur (Z → A)",
                'genre_asc' => 'Genre (A → Z)',
                'genre_desc' => 'Genre (Z → A)',
                'popup_asc' => 'Popularité (faible → élevé)',
                'popup_desc' => 'Popularité (élevé → faible)',
                'friends' => 'Les créations de mes amis'
            ];
        }





        $genres = [
            'standard' => 'Quiz standard',
            'test' => 'Quiz de type test',
            'flashcard' => 'Flashcards',
            'leçon' => 'Leçons'
        ];

        $recherche_cat = isset($_GET['categorie']) && !empty($_GET['categorie']) && htmlspecialchars($_GET['categorie']) != 'Toutes les catégories' ? (int)$_GET['categorie'] : null;
        $recherche_contenu = isset($_GET['contenu']) && (!empty($_GET['contenu']) || $_GET['contenu'] == '0') ? htmlspecialchars($_GET['contenu']) : '';
        $recherche_auteur = isset($_GET['searchAuthor']) && (!empty($_GET['searchAuthor']) || $_GET['searchAuthor'] == '0') ? htmlspecialchars($_GET['searchAuthor']) : '';
        $tri = isset($_GET['tri']) && !empty($_GET['tri']) ? $_GET['tri'] : null;
        $user = isset($_SESSION['id']) ? (int)$_SESSION['id'] : null;
        $genre = isset($_GET['genre']) && !empty($_GET['genre']) ? $_GET['genre'] : '';
        $quiz_correspondants = $this->model->searchContentByAll($user, $recherche_cat, $recherche_contenu,  $recherche_auteur, $genre, $tri);
        $page = isset($_GET['numPage']) && !empty($_GET['numPage']) && (int)$_GET['numPage'] > 0 ? (int)$_GET['numPage'] : 1;

        $quizzes = $quiz_correspondants;
        foreach ($quizzes as $index => $quiz) {
            if ($quizzes[$index]['genre'] == 'leçon') {
                $quizzes[$index]['categories'] = $this->model->getCategoriesFromLesson($quiz['id']);
            } else {
                $quizzes[$index]['categories'] = $this->model->getCategoriesFromQuiz($quiz['id']);
            }
        }
        $nbPages = ceil(count($quizzes) / 30);

        require ROOT . '/src/views/catalogue.php';
    }
}
