<?php
    require_once ROOT . '/src/models/CRUDModel.php';
    require_once ROOT . '/config/config.php';
    require_once ROOT . '/config/construction.php';


    class CRUDController {
        private $model;

        public function index(){
            // Vérifier les droits d'accès admin
            requireAdmin();
            
            $db = getDbConnection();
            //constructionBD($db);
            $this->model = new CRUDModel($db);
            // afficher la vue
            

            $cats = $this->model->getCategories();


            $recherche_cat = isset($_GET['categorie'])&& !empty($_GET['categorie'])&& htmlspecialchars($_GET['categorie'])!='Toutes les catégories' ? $_GET['categorie'] : '';
            $recherche_contenu = isset($_GET['contenu'])&& !empty($_GET['contenu']) ? htmlspecialchars($_GET['contenu']) : '';
            
            $tri = isset($_GET['tri']) && !empty($_GET['tri']) ? $_GET['tri'] : null;

            $filtre = isset($_GET['filtre']) && !empty($_GET['filtre']) ? htmlspecialchars($_GET['filtre']) : '';
            $genre = isset($_GET['genre']) && !empty($_GET['genre']) && htmlspecialchars($_GET['genre']) != 'Tous les genres' ? htmlspecialchars($_GET['genre']) : '';
            $recherche = isset($_GET['search']) && !empty($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
            $authors = [];
            $quizzes = [];
            $lessons = [];
            
            if($recherche_cat != ''){
                $quiz_correspondants = $this->model->searchQuizByAll($recherche_cat,$recherche_contenu,$recherche,$genre,$tri);
            }else{
                // Si l'utilisateur a choisi de filtrer par auteur explicitement -> rechercher les auteurs
                if ($filtre === 'auteur') {
                    // Cherche les auteurs correspondant à la recherche (liste d'auteurs)
                    $authors = $this->model->searchAuthors($recherche);
                    // Pas de recherche de quiz ici ; on affiche la liste d'auteurs dans la vue
                    $quiz_correspondants = [];
                } elseif ($filtre === 'quiz') {
                    // recherche par titre de quiz uniquement
                    $quiz_correspondants = $this->model->searchQuizByTitle($recherche,$genre,$tri);
                } elseif ($filtre === 'lecon') {
                    // recherche par titre et contenu de leçon
                    $lessons = $this->model->searchLessonByTitle($recherche,$tri);
                    $quiz_correspondants = [];
                } else {
                    // recherche par contenu et auteur si fourni
                    $quiz_correspondants = $this->model->searchQuizByContentAndAuthor($recherche_contenu,$recherche,$genre,$tri);
                }
            }

            $quizzes = $quiz_correspondants;
            foreach ($quizzes as $index => $quiz){
                $quizzes[$index]['categories'] = $this->model->getCategoriesFromQuiz($quiz['id']);
                $quizzes[$index]['nom_auteur'] = $this->model->getNomAuteur($quiz['user_id']);
                
                $quizzes[$index]['likes'] = isset($quiz['likes']) ? (int)$quiz['likes'] : 0;
                $quizzes[$index]['dislikes'] = isset($quiz['dislikes']) ? (int)$quiz['dislikes'] : 0;
            }
            
            foreach ($lessons as $index => $lesson){
                $lessons[$index]['categories'] = $this->model->getCategoriesFromLesson($lesson['id']);
            }
            require ROOT . '/src/views/CRUD/CRUDrecherche.php';
        }
    }


?>