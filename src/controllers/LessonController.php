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
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $db = getDbConnection();
        $model = new LessonModel($db);

        session_start();

        if (isset($_SESSION['id'])){
            $id = $_SESSION['id'];
        }
        else{
            header('Location: index.php?page=home');
            exit;
        }
        if (!isset($_SESSION['POST'])){
            $_SESSION['POST'] = [];
        }
        if(!isset($_SESSION['POST']['disponibilite'])){
            $_SESSION['POST']['disponibilite'] = 'public';
        }
        if (!isset($_SESSION['bouton'])){
            $_SESSION['bouton'] = false;
        }
        if (!isset($_SESSION['erreur'])){
            $_SESSION['erreur'] = false;
        }
        //var_dump($_SESSION);
        //var_dump($_POST);
        
        // afficher la vue

        if (!isset($_SESSION['nbParts']) || empty($_SESSION['nbParts'])){
            $_SESSION['nbParts'] = 1;
        }
        //$nbParts = isset($_POST['nbPart'])&& !empty($_POST['nbPart']) ? $_POST['nbPart'] : 1;
        
        if (!isset($_SESSION['nbExemple']) || empty($_SESSION['nbExemple'])){
            $_SESSION['nbExemple'] =  array(0 => 0);
        }
        
        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes"){
            unset($_SESSION['nbExemple']);
            unset($_SESSION['nbParts']);
            unset($_SESSION['POST']);
            unset($_SESSION['bouton']);
            unset($_SESSION['erreur']);
            unset($_POST);
            header('Location: index.php?page=createContent');
            exit;
        }
        if (isset($_POST['addPart']) && !empty($_POST['addPart'])){
            $_SESSION['nbParts']++;
            $_SESSION['nbExemple'][$_SESSION['nbParts']-1] = 0;
            $this->contentFusionSessionPost();
            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['addExemple']) && $_POST['addExemple'] != ''){
            
            $_SESSION['nbExemple'][(int)$_POST['addExemple']]++;
            $this->contentFusionSessionPost();
            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
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
            $this->contentFusionSessionPost();
            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
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
                    $this->contentFusionSessionPost();
                    $_SESSION['bouton'] = true;
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit;
                }
            }
        }

        $quizSelected = (isset($_POST['linkedQuiz']) && !empty($_POST['linkedQuiz']) && $_POST['linkedQuiz'] != 'Aucun') ? (int)$_POST['linkedQuiz'] : null;
        
        if ($_SESSION['bouton'] === false){
            $this->contentFusionSessionPost();
        }

        $LessonTitle = isset($_SESSION['POST']['LessonTitle']) ? $_SESSION['POST']['LessonTitle'] : '';
        $desc = isset($_SESSION['POST']['LessonDescription']) ? $_SESSION['POST']['LessonDescription'] : '';
        //var_dump($quizSelected);
        $TAB_CONTENU = array();
        for ($i = 0; $i < $_SESSION['nbParts'] ; $i++){
            $partContent = array(
                'name' => isset($_SESSION['POST']['namePart'.$i]) ? $_SESSION['POST']['namePart'.$i] : '',
                'content' => isset($_SESSION['POST']['contentPart'.$i]) ? $_SESSION['POST']['contentPart'.$i] : '',
                'exemples' => array()
            );
            for ($k = 0; $k < $_SESSION['nbExemple'][$i] ; $k++){
                $exampleContent = array(
                    'consigne' => isset($_SESSION['POST']['exemple'.$k.'-part'.$i]) ? $_SESSION['POST']['exemple'.$k.'-part'.$i] : '',
                    'reponse' => isset($_SESSION['POST']['reponse'.$k.'-part'.$i]) ? $_SESSION['POST']['reponse'.$k.'-part'.$i] : ''
                );
                $partContent['exemples'][] = $exampleContent;
            }
            $TAB_CONTENU[] = $partContent;
        }
        $TAB_AMI = $model->getAmis($_SESSION['id']);

        $TAB_AMI_CHOISI = array();
        if (isset($_SESSION['POST']['amiDispo'])){
            $TAB_AMI_CHOISI = $_SESSION['POST']['amiDispo'];
        }

        if (isset($_POST['create']) && $_POST['create'] == "yes"){
            $LessonTitle = $_POST['LessonTitle'];
            $desc = $_POST['LessonDescription'];
            $this->contentFusionSessionPost();
            if ($this->verifValidite()){
                $reussi = $model->createLesson($id, $LessonTitle, $desc, $_SESSION['nbParts'],$_SESSION['nbExemple'],$TAB_CONTENU,$TAB_AMI_CHOISI, $_SESSION['POST']['disponibilite'],$quizSelected);
                //je mets une redirecion pour être sûr qu'on ne l'oublie pas après
                if($reussi){
                    unset($_SESSION['nbExemple']);
                    unset($_SESSION['nbParts']);
                    unset($_SESSION['POST']);
                    unset($_SESSION['bouton']);
                    unset($_SESSION['erreur']);
                    unset($_POST);
                    header('Location: index.php?page=home');
                    exit;
                }
                else{
                    unset($_POST['create']);
                    $_SESSION['bouton'] = true;
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit;
                }
                
            }
            else{
                $_SESSION['erreur'] = true;
                unset($_POST['create']);
                $_SESSION['bouton'] = true;
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
        }


        $quizzes = $model->getQuizByAuthor($id);

        $_SESSION['bouton'] = false;

        require ROOT . '/src/views/lesson/createLesson.php';
    }

    public function verifValidite(){
        if (!isset($_SESSION['POST']['LessonTitle']) || empty($_SESSION['POST']['LessonDescription'])){
            return false;
        }
        if (!isset($_SESSION['POST']['LessonDescription']) || empty($_SESSION['POST']['LessonDescription'])){
            return false;
        }
        for ($i = 0; $i < $_SESSION['nbParts'] ; $i++){
            if (!isset($_SESSION['POST']['namePart'.$i]) || empty($_SESSION['POST']['namePart'.$i])){
                return false;
            }
            if (!isset($_SESSION['POST']['contentPart'.$i]) || empty($_SESSION['POST']['contentPart'.$i])){
                return false;
            }
            for ($k = 0; $k < $_SESSION['nbExemple'][$i] ; $k++){
                if (!isset($_SESSION['POST']['exemple'.$k.'-part'.$i]) || empty($_SESSION['POST']['exemple'.$k.'-part'.$i])){
                    return false;
                }
                if (!isset($_SESSION['POST']['reponse'.$k.'-part'.$i]) || empty($_SESSION['POST']['reponse'.$k.'-part'.$i])){
                    return false;
                }
            }
        }
        return true;
    }

    public function contentFusionSessionPost(){
        if (isset($_POST['LessonTitle'])){
            $_SESSION['POST']['LessonTitle'] = $_POST['LessonTitle'];
        }
        if (isset($_POST['LessonDescription'])){
            $_SESSION['POST']['LessonDescription'] = $_POST['LessonDescription'];
        }
        for ($i = 0; $i < $_SESSION['nbParts'] ; $i ++){
            if (isset($_POST['namePart'.$i])){
                $_SESSION['POST']['namePart'.$i] = $_POST['namePart'.$i];
            }
            if (isset($_POST['contentPart'.$i])){
                $_SESSION['POST']['contentPart'.$i] = $_POST['contentPart'.$i];
            }
            for ($k = 0; $k < $_SESSION['nbExemple'][$i] ; $k++){
                if (isset($_POST['exemple'.$k.'-part'.$i])){
                    $_SESSION['POST']['exemple'.$k.'-part'.$i] = $_POST['exemple'.$k.'-part'.$i];
                }
                if (isset($_POST['reponse'.$k.'-part'.$i])){
                    $_SESSION['POST']['reponse'.$k.'-part'.$i] = $_POST['reponse'.$k.'-part'.$i];
                }
            }
        }
        if(isset($_POST['disponibilite'])){
            $_SESSION['POST']['disponibilite'] = $_POST['disponibilite'];
        }
        if(isset($_POST['amiDispo'])){
            $_SESSION['POST']['amiDispo'] = $_POST['amiDispo'];
        }
    }

}

?>