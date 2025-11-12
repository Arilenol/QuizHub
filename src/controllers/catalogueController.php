<?php
    require_once ROOT . '/src/models/CatalogueModel.php';
    require_once ROOT . '/config/config.php';


    class CatalogueController {
        private $model;

        public function index(){
            $db = getDbConnection();
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
            }
            require ROOT . '/src/views/catalogue.php';
        }
    }


?>