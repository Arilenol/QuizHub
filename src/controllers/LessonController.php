<?php
require_once ROOT . '/src/models/LessonModel.php';
require_once ROOT . '/config/config.php';

class LessonController {
    public function index($id) {
        $db = getDbConnection();
        $model = new LessonModel($db);
        // récupère les données de la leçon
        $lesson = $model->getLesson($id);
        if (!$lesson) {
            // Leçon non trouvée
            http_response_code(404);
            echo "Leçon non trouvée";
            return;
        }
        // récupère les parties
        $parties = $model->getPart($id) ?: []; 
        // récupère les exemples pour chaque partie
        $resultats = [];
        foreach ($parties as $part) {
            $exemples = $model->getExemple($part['id']);
            $resultats[] = $exemples;
        }
        // afficher la vue
        require ROOT . '/src/views/lesson/show.php';
    }

    //A DEVELOPPER
    public function createLesson(){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $db = getDbConnection();
        $model = new LessonModel($db);

        session_start();
        if (isset($_SESSION['id'])){
            $id = $_SESSION['id'];
        }
        else{
            header('Location: index.php?page=home');
        }
        //var_dump($_SESSION);
        //var_dump($_POST);
        $title = isset($_POST['LessonTitle']) ? $_POST['LessonTitle'] : '';
        $desc = isset($_POST['LessonDescription']) ? $_POST['LessonDescription'] : '';
        // afficher la vue

        if (!isset($_SESSION['nbParts']) && empty($_SESSION['nbParts'])){
            $_SESSION['nbParts'] = 1;
        }
        //$nbParts = isset($_POST['nbPart'])&& !empty($_POST['nbPart']) ? $_POST['nbPart'] : 1;
        
        if (!isset($_SESSION['nbExemple']) || empty($_SESSION['nbExemple'])){
            $_SESSION['nbExemple'] =  array(0 => 0);
        }
        
        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes"){
            unset($_SESSION['nbExemple']);
            unset($_SESSION['nbParts']);
            header('Location: index.php?page=createContent');
        }
        if (isset($_POST['addPart']) && !empty($_POST['addPart'])){
            $_SESSION['nbParts']++;
            $_SESSION['nbExemple'][$_SESSION['nbParts']-1] = 0;
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }
        if (isset($_POST['addExemple']) && $_POST['addExemple'] != ''){
            
            $_SESSION['nbExemple'][(int)$_POST['addExemple']]++;
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }

        if (isset($_POST['DelPart']) && $_POST['DelPart'] !== '') {
            $idx = (int)$_POST['DelPart'];
            $oldNbParts = $_SESSION['nbParts']; 

            
            for ($i = $idx; $i < $oldNbParts - 1; $i++) {
                $_SESSION['nbExemple'][$i] = $_SESSION['nbExemple'][$i + 1];
                $_POST['namePart'.$i] = $_POST['namePart'.($i + 1)];
                $_POST['contentPart'.$i] = $_POST['contentPart'.($i + 1)];
                
                for ($k = 0; $k < $_SESSION['nbExemple'][$i]; $k++) {
                    $_POST['exemple'.$k.'-part'.$i] = $_POST['exemple'.$k.'-part'.($i + 1)];
                    $_POST['reponse'.$k.'-part'.$i] = $_POST['reponse'.$k.'-part'.($i + 1)];
                }
            }
            
            $last = $oldNbParts - 1;
            unset($_SESSION['nbExemple'][$last]);
            unset($_POST['namePart'.$last]);
            unset($_POST['contentPart'.$last]);
            for ($k = 0; $k < 10; $k++) { 
                unset($_POST['exemple'.$k.'-part'.$last]);
                unset($_POST['reponse'.$k.'-part'.$last]);
            }

            
            $_SESSION['nbParts']--;
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }


        for ($i = 0; $i < $_SESSION['nbParts'] ; $i++){
            for ($k = 0; $k < $_SESSION['nbExemple'][$i] ; $k++){
                if (isset($_POST['delExemple'.$k.'-part'.$i]) && $_POST['delExemple'.$k.'-part'.$i] == "yes"){
                    $_SESSION['nbExemple'][$i]--;
                    for ($e = $k ; $e < $_SESSION['nbExemple'][$i] ; $e++){
                        $_POST['exemple'.$e.'-part'.$i] = $_POST['exemple'.($e+1).'-part'.$i];
                        $_POST['reponse'.$e.'-part'.$i] = $_POST['reponse'.($e+1).'-part'.$i];
                    }
                    unset($_POST['exemple'.$_SESSION['nbExemple'][$i].'-part'.$i]);
                    unset($_POST['reponse'.$_SESSION['nbExemple'][$i].'-part'.$i]);
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                }
            }
        }

        $quizSelected = (isset($_POST['linkedQuiz']) && !empty($_POST['linkedQuiz']) && $_POST['linkedQuiz'] != 'Aucun') ? (int)$_POST['linkedQuiz'] : null;
        //var_dump($quizSelected);
        $TAB_CONTENU = array();
        for ($i = 0; $i < $_SESSION['nbParts'] ; $i++){
            $partContent = array(
                'name' => isset($_POST['namePart'.$i]) ? $_POST['namePart'.$i] : '',
                'content' => isset($_POST['contentPart'.$i]) ? $_POST['contentPart'.$i] : '',
                'exemples' => array()
            );
            for ($k = 0; $k < $_SESSION['nbExemple'][$i] ; $k++){
                $exampleContent = array(
                    'consigne' => isset($_POST['exemple'.$k.'-part'.$i]) ? $_POST['exemple'.$k.'-part'.$i] : '',
                    'reponse' => isset($_POST['reponse'.$k.'-part'.$i]) ? $_POST['reponse'.$k.'-part'.$i] : ''
                );
                $partContent['exemples'][] = $exampleContent;
            }
            $TAB_CONTENU[] = $partContent;
        }

        if (isset($_POST['create']) && $_POST['create'] == "yes"){
            if (isset($_POST['LessonTitle']) && !empty($_POST['LessonTitle'])  && isset($_POST['LessonDescription']) && !empty($_POST['LessonDescription'])){
                $title = $_POST['LessonTitle'];
                $desc = $_POST['LessonDescription'];
                $model->createLesson($id, $title, $desc, $_SESSION['nbParts'],$_SESSION['nbExemple'],$TAB_CONTENU,$quizSelected);
                //je mets une redirecion pour être sûr qu'on ne l'oublie pas après
                unset($_SESSION['nbExemple']);
                unset($_SESSION['nbParts']);
                unset($_POST);
                header('Location: index.php?page=home');
                exit;
            }
            
        }


        $quizzes = $model->getQuizByAuthor($id);

        require ROOT . '/src/views/lesson/createLesson.php';
    }

}

?>