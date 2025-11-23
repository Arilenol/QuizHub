<?php
require_once ROOT . '/src/models/QuizModel.php';
require_once ROOT . '/config/config.php';

class QuizController {
    public function createQuiz(){
        // show all errors while developing this flow
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $db = getDbConnection();
        $model = new QuizModel($db);

        session_start();

        //unset($_POST);
        //unset($_SESSION);
        //------------------------à changer-------------------------
        $_SESSION['id'] = 1;
        //------------------------à changer-------------------------


        if (isset($_SESSION['id'])){
            $id = $_SESSION['id'];
        }
        else{
            header('Location: index.php?page=home');
        }
        

        $title = isset($_POST['QuizTitle']) ? $_POST['QuizTitle'] : '';
        $desc = isset($_POST['QuizDescription']) ? $_POST['QuizDescription'] : '';
        
        if (!isset($_SESSION['nbQuestions']) || empty($_SESSION['nbQuestions'])){
            $_SESSION['nbQuestions'] = 1;
        }
        if (!isset($_SESSION['nbReponse']) || empty($_SESSION['nbReponse'])){
            // array where each index is number of answers for that question
            $_SESSION['nbReponse'] = array(0 => 2);
        }

        
        // Handle form actions: Retour, addQuestion, addReponse, DelQuestion, delReponseX
        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes"){
            unset($_SESSION['nbReponse']);
            unset($_SESSION['nbQuestions']);
            unset($_SESSION['POST']);
            unset($_POST);
            // redirect back to content creation or other appropriate page
            header('Location: index.php?page=createContent');
            exit;
        }

        if (isset($_POST['addQuestion']) && $_POST['addQuestion'] === 'yes'){
            $_SESSION['nbQuestions']++;
            // new question starts with minimum allowed responses
            $_SESSION['nbReponse'][$_SESSION['nbQuestions']-1] = 2;
            // Sauvegarde complète du formulaire
            foreach ($_POST as $key => $value) {
                $_SESSION['POST'][$key] = $value;
            }
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        if (isset($_POST['addReponse']) && $_POST['addReponse'] !== ''){
            $_SESSION['nbReponse'][(int)$_POST['addReponse']]++;
            foreach ($_POST as $key => $value) {
                $_SESSION['POST'][$key] = $value;
            }
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        // Delete a question (shift subsequent questions up)
        if (isset($_POST['DelQuestion']) && $_POST['DelQuestion'] !== '') {
            if ($_SESSION['nbQuestions'] > 1){
                $idx = (int)$_POST['DelQuestion'];
                $oldNb = $_SESSION['nbQuestions'];
                for ($i = $idx; $i < $oldNb - 1; $i++) {
                    $_SESSION['nbReponse'][$i] = $_SESSION['nbReponse'][$i + 1];
                    $_POST['question'.$i] = $_POST['question'.($i + 1)];
                    
                    for ($k = 0; $k < $_SESSION['nbReponse'][$i]; $k++) {
                        $_POST['reponse'.$k.'-question'.$i] = $_POST['reponse'.$k.'-question'.($i + 1)];
                        $_POST['checkbox'.$k.'-question'.$i] = $_POST['checkbox'.$k.'-question'.($i + 1)];
                    }
                }
                
                $last = $oldNb - 1;
                unset($_SESSION['nbReponse'][$last]);
                unset($_POST['question'.$last]);
                for ($k = 0; $k < 10; $k++) { 
                    unset($_POST['reponse'.$k.'-question'.$last]);
                    unset($_POST['checkbox'.$k.'-question'.$last]);
                }
                $_SESSION['nbQuestions']--;
            }
            foreach ($_POST as $key => $value) {
                $_SESSION['POST'][$key] = $value;
            }
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        for ($i = 0; $i < $_SESSION['nbQuestions']; $i++){
            if (isset($_POST['delReponse'.$i]) && $_POST['delReponse'.$i] == "yes"){
                if ($_SESSION['nbReponse'][$i] > 2){
                    $_SESSION['nbReponse'][$i]--;
                    unset($_POST['reponse'.$_SESSION['nbReponse'][$i].'-question'.$i]);
                    unset($_POST['nbReponse'][$_SESSION['nbReponse'][$i]]);
                    foreach ($_POST as $key => $value) {
                        $_SESSION['POST'][$key] = $value;
                    }
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit;
                }
                
            }
        }
        
        
        if (!empty($_SESSION['POST'])) {
            foreach ($_SESSION['POST'] as $key => $value) {
                $_POST[$key] = $value;
            }
        }
        $TAB_CONTENU = array();
        for ($i = 0; $i < $_SESSION['nbQuestions'] ; $i++){
            $qContent = array(
                'name' => isset($_POST['question'.$i]) ? $_POST['question'.$i] : '',
                'reponses' => array()
            );
            for ($k = 0; $k < $_SESSION['nbReponse'][$i] ; $k++){
                $reponseContent = array(
                    'texte' => isset($_POST['reponse'.$k.'-question'.$i]) ? $_POST['reponse'.$k.'-question'.$i] : '',
                    'valide' => isset($_POST['checkbox'.$k.'-question'.$i]) ? $_POST['checkbox'.$k.'-question'.$i] : ''
                );
                $qContent['reponses'][] = $reponseContent;
            }
            $TAB_CONTENU[] = $qContent;
        }
        
        
        
        //------------informations à remplir---------------------
        $tabParametres = array(
            array('name' => 'minuterie'),
            array('name' => 'rééssayer les questions échouées'),
            array('name' => 'faire dans le désordre par défaut'),
            array('name' => 'afficher le score')
        );
        //------------informations à remplir---------------------
        
        if (isset($_POST['create']) && $_POST['create'] === 'yes'){
            // basic validation: ensure a title is provided
            if (isset($_POST['QuizTitle']) && !empty($_POST['QuizTitle'])){
                $title = $_POST['QuizTitle'];
                $desc = isset($_POST['QuizDescription']) ? $_POST['QuizDescription'] : '';

                
                unset($_SESSION['nbReponse']);
                unset($_SESSION['nbQuestions']);
                unset($_SESSION['POST']);
                header('Location: index.php?page=home');
                exit;
            }
        }

        
        //var_dump($_SESSION);
        //var_dump($_POST);
        //var_dump($TAB_CONTENU);
        //unset($_SESSION['POST']);
        require ROOT . '/src/views/Quiz/createQuiz.php';
    }

}
?>