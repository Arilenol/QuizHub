<?php
    require_once ROOT . '/src/models/CatalogueModel.php';
    require_once ROOT . '/config/config.php';
    require_once ROOT . '/config/construction.php';


    class CatalogueController {
        private $model;

        public function index(){
            $db = getDbConnection();
            //constructionBD($db);
            $this->model = new CatalogueModel($db);
            // afficher la vue
            

            $cats = $this->model->getCategories();


            $recherche_cat = isset($_GET['categorie'])&& !empty($_GET['categorie'])&& htmlspecialchars($_GET['categorie'])!='Toutes les catégories' ? $_GET['categorie'] : '';
            $recherche_contenu = isset($_GET['contenu'])&& !empty($_GET['contenu']) ? htmlspecialchars($_GET['contenu']) : '';
            $recherche_auteur = isset($_GET['searchAuthor']) && !empty($_GET['searchAuthor']) ? htmlspecialchars($_GET['searchAuthor']) : '';
            $tri = isset($_GET['tri']) && !empty($_GET['tri']) ? $_GET['tri'] : null;
            if($recherche_cat != ''){
                $quiz_correspondants = $this->model->searchQuizByAll($recherche_cat,$recherche_contenu,$recherche_auteur,$tri);
            }else{
                $quiz_correspondants = $this->model->searchQuizByContentAndAuthor($recherche_contenu,$recherche_auteur,$tri);
            }

            $quizzes = $quiz_correspondants;
            foreach ($quizzes as &$quiz){
                $quiz['categories'] = $this->model->getCategoriesFromQuiz($quiz['id']);
                $quiz['nom_auteur'] = $this->model->getNomAuteur($quiz['user_id']);
                // likes/dislikes are now selected directly by the search queries (likes/dislikes)
                $quiz['likes'] = isset($quiz['likes']) ? (int)$quiz['likes'] : 0;
                $quiz['dislikes'] = isset($quiz['dislikes']) ? (int)$quiz['dislikes'] : 0;
            }
            require ROOT . '/src/views/catalogue.php';
        }
    }


?>