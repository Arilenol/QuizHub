<?php
require_once ROOT . '/src/models/QuizModel.php';
require_once ROOT . '/config/config.php';

class QuizController
{

    private QuizModel $model;

    public function __construct()
    {
        $db = getDbConnection();
        $this->model = new QuizModel($db);
    }

    public function createQuiz()
    {
        
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        session_start();

        //unset($_POST);
        //unset($_SESSION);



        if (isset($_SESSION['id'])) {
            $id = $_SESSION['id'];
        } else {
            header('Location: index.php?page=home');
            exit;
        }
        if (!isset($_SESSION['POST'])) {
            $_SESSION['POST'] = [];
        }
        //-----------------------------------------------------ici---------------------------------------------------------
        if(!isset($_SESSION['POST']['disponibilite'])){
            $_SESSION['POST']['disponibilite'] = 'public';
        }
        if (!isset($_SESSION['bouton'])) {
            $_SESSION['bouton'] = false;
        }
        if (!isset($_SESSION['erreur'])){
            $_SESSION['erreur'] = false;
        }
        

        if (!isset($_SESSION['nbQuestions']) || empty($_SESSION['nbQuestions'])) {
            $_SESSION['nbQuestions'] = 1;
        }
        if (!isset($_SESSION['nbReponse']) || empty($_SESSION['nbReponse'])) {
            // array where each index is number of answers for that question
            $_SESSION['nbReponse'] = array(0 => 2);
        }
        //var_dump($_POST);



        //------------informations à remplir---------------------
        $tabParametres = array(
            array('name' => 'test', 'desc' => 'Un test permet de voir un récapitulatif à la fin du quiz<br>Ce mode permet de simuler une évaluation'),
            array('name' => 'timer', 'desc' => 'minuterie'),
            array('name' => 'retryError','desc' => 'rééssayer les questions échouées'),
            array('name' => 'noOrder','desc' => 'faire dans le désordre par défaut'),
            array('name' => 'score','desc' => 'afficher le score'),
            array('name' => 'avancement', 'desc' => 'afficher l\'avancement'),
            array('name' => 'recap', 'desc' => 'afficher un recapitulatif à la fin')
        );

        //------------informations à remplir---------------------
        
        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes") {
            unset($_SESSION['bouton']);
            unset($_SESSION['nbReponse']);
            unset($_SESSION['nbQuestions']);
            unset($_SESSION['POST']);
            unset($_SESSION['erreur']);
            unset($_POST);
            
            header('Location: index.php?page=createContent');
            exit;
        }

        if (isset($_POST['addQuestion']) && $_POST['addQuestion'] === 'yes') {
            $_SESSION['nbQuestions']++;
            // new question starts with minimum allowed responses
            $_SESSION['nbReponse'][$_SESSION['nbQuestions'] - 1] = 2;
            // Sauvegarde complète du formulaire

            unset($_POST['addQuestion']);
            $this->contentFusionSessionPost();

            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        if (isset($_POST['addReponse']) && $_POST['addReponse'] !== '') {
            $_SESSION['nbReponse'][(int)$_POST['addReponse']]++;
            $i = $_POST['addReponse'];
            unset($_POST['addReponse']);
            $this->contentFusionSessionPost();

            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        
        if (isset($_POST['DelQuestion']) && $_POST['DelQuestion'] !== '') {
            if ($_SESSION['nbQuestions'] > 1) {
                $idx = (int)$_POST['DelQuestion'];
                $oldNb = $_SESSION['nbQuestions'];
                for ($i = $idx; $i < $oldNb - 1; $i++) {
                    $_SESSION['nbReponse'][$i] = $_SESSION['nbReponse'][$i + 1];
                    $_POST['question' . $i] = $_POST['question' . ($i + 1)];

                    for ($k = 0; $k < $_SESSION['nbReponse'][$i]; $k++) {
                        $_POST['reponse' . $k . '-question' . $i] = $_POST['reponse' . $k . '-question' . ($i + 1)];
                        if (isset( $_POST['checkbox' . $k . '-question' . ($i + 1)])){
                            $_POST['checkbox' . $k . '-question' . $i] = $_POST['checkbox' . $k . '-question' . ($i + 1)];
                        }
                    }
                }

                $last = $oldNb - 1;
                unset($_SESSION['nbReponse'][$last]);
                unset($_POST['question' . $last]);
                for ($k = 0; $k < 10; $k++) {
                    unset($_POST['reponse' . $k . '-question' . $last]);
                    unset($_POST['checkbox' . $k . '-question' . $last]);
                }
                $_SESSION['nbQuestions']--;
            }
            unset($_POST['DelQuestion']);
            $this->contentFusionSessionPost();

            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        for ($i = 0; $i < $_SESSION['nbQuestions']; $i++) {
            if (isset($_POST['delReponse' . $i]) && $_POST['delReponse' . $i] == "yes") {
                if ($_SESSION['nbReponse'][$i] > 2) {
                    $_SESSION['nbReponse'][$i]--;
                    unset($_POST['reponse' . $_SESSION['nbReponse'][$i] . '-question' . $i]);
                    unset($_POST['nbReponse'][$_SESSION['nbReponse'][$i]]);
                }
                unset($_POST['delReponse' . $i]);
                $this->contentFusionSessionPost();

                $_SESSION['bouton'] = true;
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
        if ($_SESSION['bouton'] === false) {
            $this->contentFusionSessionPost();
        }
        

        $quizTitle = isset($_SESSION['POST']['QuizTitle']) ? $_SESSION['POST']['QuizTitle'] : '';
        $desc = isset($_SESSION['POST']['QuizDescription']) ? $_SESSION['POST']['QuizDescription'] : '';

        $TAB_CATEGORIE = $this->model->getAllCategories();
        $TAB_CATEGORIE_CHOISI = array();
        if (isset($_SESSION['POST']['categories'])){
            $TAB_CATEGORIE_CHOISI = $_SESSION['POST']['categories'];
        }


        $TAB_CONTENU = array();
        for ($i = 0; $i < $_SESSION['nbQuestions']; $i++) {
            $qContent = array(
                'name' => isset($_SESSION['POST']['question' . $i]) ? $_SESSION['POST']['question' . $i] : '',
                'reponses' => array()
            );
            for ($k = 0; $k < $_SESSION['nbReponse'][$i]; $k++) {
                $reponseContent = array(
                    'texte' => isset($_SESSION['POST']['reponse' . $k . '-question' . $i]) ? $_SESSION['POST']['reponse' . $k . '-question' . $i] : '',
                    'valide' => isset($_SESSION['POST']['checkbox' . $k . '-question' . $i]) ? $_SESSION['POST']['checkbox' . $k . '-question' . $i] : 0
                );
                $qContent['reponses'][] = $reponseContent;
            }
            $TAB_CONTENU[] = $qContent;
        }

        $TAB_PARAM = [];
        foreach ($tabParametres as $param){
            if(isset($_SESSION['POST']['param'.$param['name']]) && $_SESSION['POST']['param'.$param['name']] == 'on'){
                $TAB_PARAM[] = 'checked';
            }
            else{
                $TAB_PARAM[] = '';
            }
        }

        //-----------------------------------------------------ici---------------------------------------------------------
        $TAB_AMI = $this->model->getAmis($_SESSION['id']);
        $timerValue = isset($_SESSION['POST']['timerValue']) ? $_SESSION['POST']['timerValue'] : 0;

        $TAB_AMI_CHOISI = array();
        if (isset($_SESSION['POST']['amiDispo'])){
            $TAB_AMI_CHOISI = $_SESSION['POST']['amiDispo'];
        }
        

        if (isset($_POST['create']) && $_POST['create'] === 'yes') {
            $quizTitle = $_POST['QuizTitle'];
            $desc = isset($_POST['QuizDescription']) ? $_POST['QuizDescription'] : '';
            $this->contentFusionSessionPost();
            $test = $this->verifValidite();
            if($test){
                
                $reussi = $this->model->createQuiz($id, $TAB_PARAM,$timerValue, $TAB_CONTENU,$TAB_AMI_CHOISI, $TAB_CATEGORIE_CHOISI, $_SESSION['POST']['disponibilite'], $desc, $quizTitle, $_SESSION['nbQuestions'], $_SESSION['nbReponse']);
                if ($reussi){
                    unset($_SESSION['bouton']);
                    unset($_SESSION['nbReponse']);
                    unset($_SESSION['nbQuestions']);
                    unset($_SESSION['POST']);
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
        
        $_SESSION['bouton'] = false;
        //var_dump($_SESSION['POST']);
        //var_dump($_POST);
        //var_dump($TAB_CONTENU);
        //var_dump($_SESSION);
        //var_dump($TAB_AMI);
        //var_dump($TAB_PARAM);
        //unset($_SESSION['POST']);
        require ROOT . '/src/views/Quiz/createQuiz.php';
    }

    public function contentFusionSessionPost(){
        $tabParametres = array(
            array('name' => 'test', 'desc' => 'Un test permet de voir un récapitulatif à la fin du quiz<br>Ce mode permet de simuler une évaluation'),
            array('name' => 'timer', 'desc' => 'minuterie'),
            array('name' => 'retryError','desc' => 'rééssayer les questions échouées'),
            array('name' => 'noOrder','desc' => 'faire dans le désordre par défaut'),
            array('name' => 'score','desc' => 'afficher le score'),
            array('name' => 'avancement', 'desc' => 'afficher l\'avancement'),
            array('name' => 'recap', 'desc' => 'afficher un recapitulatif à la fin')
        );
        if (isset($_POST['QuizTitle'])){
            $_SESSION['POST']['QuizTitle'] = $_POST['QuizTitle'];
        }
        if (isset($_POST['QuizDescription'])){
            $_SESSION['POST']['QuizDescription'] = $_POST['QuizDescription'];
        }
        for ($i = 0; $i < $_SESSION['nbQuestions']; $i++) {
            for ($k = 0; $k < $_SESSION['nbReponse'][$i]; $k++) {
                if (isset($_POST['question' . $i])) {
                    $_SESSION['POST']['question' . $i] = $_POST['question' . $i];
                }
                if (isset($_POST['reponse' . $k . '-question' . $i])) {
                    $_SESSION['POST']['reponse' . $k . '-question' . $i] = $_POST['reponse' . $k . '-question' . $i];
                }
                if (isset($_POST['checkbox' . $k . '-question' . $i])) {
                    $_SESSION['POST']['checkbox' . $k . '-question' . $i] = "on";
                } else {
                    $_SESSION['POST']['checkbox' . $k . '-question' . $i] = '';
                }
            }
        }
        foreach($tabParametres as $param){
            if (isset($_POST['param'.$param['name']]) && $_POST['param'.$param['name']] === "on"){
                $_SESSION['POST']['param'.$param['name']] = $_POST['param'.$param['name']];
            }
            else{
                $_SESSION['POST']['param'.$param['name']] = '';
                
            }
        }
        if(isset($_POST['timerValue'])){
            $_SESSION['POST']['timerValue'] = $_POST['timerValue'];
        }
        //-----------------------------------------------------ici---------------------------------------------------------
        if(isset($_POST['disponibilite'])){
            $_SESSION['POST']['disponibilite'] = $_POST['disponibilite'];
        }
        if(isset($_POST['amiDispo'])){
            $_SESSION['POST']['amiDispo'] = $_POST['amiDispo'];
        }
        if(isset($_POST['categories'])){
            $_SESSION['POST']['categories'] = $_POST['categories'];
        }
    }

    public function verifValidite(){
        if(isset($_SESSION['POST']['timerValue']) && (int)$_SESSION['POST']['timerValue'] > 120){
            return false;
        }
        if (!isset($_SESSION['POST']['QuizTitle']) || empty($_SESSION['POST']['QuizTitle'])){
            return false;
        }
        if (!isset($_SESSION['POST']['QuizDescription']) || empty($_SESSION['POST']['QuizDescription'])){
            return false;
        }
        for ($i = 0; $i < $_SESSION['nbQuestions']; $i++){
            $count = 0;
            for ($k = 0; $k < $_SESSION['nbReponse'][$i] ; $k++){
                if (isset($_SESSION['POST']['checkbox'.$k.'-question'.$i]) && $_SESSION['POST']['checkbox'.$k.'-question'.$i] == "on"){
                    $count++;
                }
                if(!isset($_SESSION['POST']['reponse'.$k.'-question'.$i]) || empty($_SESSION['POST']['reponse'.$k.'-question'.$i])){
                    return false;
                }
            }
            if(!isset($_SESSION['POST']['question'.$i]) || empty($_SESSION['POST']['question'.$i])){
                return false;
            }
            if($count === 0 || $count === $_SESSION['nbReponse'][$i]){
                return false;
            }
        }
        if(!isset($_SESSION['POST']['categories']) || count($_SESSION['POST']['categories']) === 0){
            return false;
        }
        return true;
    }

    public function modifyQuiz($id){
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        session_start();
        $idQuiz = (int)$id;
        //die("erreur :".$idQuiz);
        $taille = $this->model->getQuizSize($idQuiz);
        $user_id = $this->model->getUserIdFromQuiz($idQuiz);
        if (!isset($_SESSION['id']) || $user_id != $_SESSION['id']){
            header('Location: index.php?page=home');
            exit;
        }
        if ($taille === 0){
            header('Location: index.php?page=home');
            exit;
        }
        $tabParametres = array(
            array('name' => 'timer', 'desc' => 'minuterie'),
            array('name' => 'retryError','desc' => 'rééssayer les questions échouées'),
            array('name' => 'noOrder','desc' => 'faire dans le désordre par défaut'),
            array('name' => 'score','desc' => 'afficher le score'),
            array('name' => 'avancement', 'desc' => 'afficher l\'avancement'),
            array('name' => 'recap', 'desc' => 'afficher un recapitulatif à la fin')
        );
        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes") {
            unset($_POST);
            header('Location: index.php?page=createContent');
            exit;
        }
        if(isset($_POST['categories']) && !empty($_POST['categories'])){
            $this->model->updateCategoriesQuiz($idQuiz, $_POST['categories']);
            unset($_POST['categories']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if(isset($_POST['appliquerDispo'])){
            $disponibilite = isset($_POST['disponibilite']) ? $_POST['disponibilite'] : 'public';
            $amiDispo = isset($_POST['amiDispo']) && is_array($_POST['amiDispo']) ? $_POST['amiDispo'] : [];
            $this->model->updateDisponibiliteQuiz($idQuiz, $disponibilite, $amiDispo);
            unset($_POST['appliquerDispo']);
            unset($_POST['disponibilite']);
            unset($_POST['amiDispo']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if(isset($_POST['applyModif'])){
            $iQuestion = (int)$_POST['applyModif'];
            $questionContent = ( isset($_POST['question'.$iQuestion]) && !empty($_POST['question'.$iQuestion]) ) ? $_POST['question'.$iQuestion] : '';
            $reponsesContent = isset($_POST['reponse'.$iQuestion]) && is_array($_POST['reponse'.$iQuestion]) && !empty($_POST['reponse'.$iQuestion]) ? $_POST['reponse'.$iQuestion] : [];
            $checksContent = isset($_POST['checkbox'.$iQuestion]) && is_array($_POST['checkbox'.$iQuestion]) ? $_POST['checkbox'.$iQuestion] : [];
            if ($this->modifQuestionValidite($questionContent, $reponsesContent, $checksContent)){
                if ($taille < $iQuestion + 1){
                    $this->model->addQuestionToQuiz($idQuiz, $iQuestion+1, $questionContent, $reponsesContent, $checksContent);
                }
                $this->model->updateQuestionQuiz($idQuiz, $iQuestion+1, $questionContent, $reponsesContent, $checksContent);
            }
            else{
                die("Erreur de validation des données de la question ".$iQuestion);
            }
            unset($_POST['applyModif']);
            unset($_POST['question'.$iQuestion]);
            unset($_POST['reponse'.$iQuestion]);
            unset($_POST['checkbox'.$iQuestion]);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['appliquerParam'])){
            $tabParams = isset($_POST['params']) && is_array($_POST['params']) ? $_POST['params'] : [];
            $timer = isset($_POST['timerValue']) ? (int)$_POST['timerValue'] : 0;
            if ($this->modifParamValidite($tabParams, $timer)){
                $this->model->updateParametresQuiz($idQuiz, $tabParams, $timer);
            }
            else{
                die("Erreur de validation des paramètres");
            }
            unset($_POST['appliquerParam']);
            unset($_POST['params']);
            unset($_POST['timerValue']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if(isset($_POST['changerGenre'])){
            $genre = isset($_POST['genre']) && ($_POST['genre'] === 'test' || $_POST['genre'] === 'standard') ? $_POST['genre'] : 'standard';
            $this->model->updateGenreQuiz($idQuiz, $genre);
            unset($_POST['changerGenre']);
            unset($_POST['genre']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['appliquerResum'])){
            $title = isset($_POST['QuizTitle']) ? $_POST['QuizTitle'] : '';
            $description = isset($_POST['QuizDescription']) ? $_POST['QuizDescription'] : '';
            if ($this->modifResumValidite($title, $description)){
                $this->model->updateResumQuiz($idQuiz, $title, $description);
            }
            else{
                die("Erreur de validation du résumé");
            }
            unset($_POST['appliquerResum']);
            unset($_POST['QuizTitle']);
            unset($_POST['QuizDescription']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if(isset($_POST['DelQuestion'])){
            $iQuestion = (int)$_POST['DelQuestion'];
            $this->model->deleteQuestionFromQuiz($idQuiz, $iQuestion+1);
            unset($_POST['DelQuestion']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if(isset($_POST['Annuler'])){
            unset($_POST);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }



        
        $quizInfos = $this->model->getQuizInfos($idQuiz);
        $TAB_QUESTIONS = $this->model->getQuestionsRepFromQuiz($idQuiz);
        $TAB_PARAMS = $this->model->getQuizParametres($idQuiz);
        $TAB_CATEGORIES = $this->model->getCategoriesFromQuiz($idQuiz);
        $ALL_CATEGORIES = $this->model->getAllCategories();
        $ALL_AMIS = $this->model->getAmis($user_id);
        $TAB_AMIS = $this->model->getAmisSelection($idQuiz);


        

        //var_dump($_POST);
        //var_dump($_SESSION);
        $erreur = false;

        require ROOT . '/src/views/Quiz/modifyQuiz.php';
    }

    public function modifParamValidite(array $paramsContent, int $timer): bool{
        $allowedParams = array('timer', 'retryError', 'noOrder', 'score', 'avancement', 'recap');
        if (count($paramsContent) != count($allowedParams)){
            return false;
        }
        if ($timer < 0 || $timer > 120){
            return false;
        }
        if ($timer == 0){
            $paramsContent[0] = 0;
        }
        return true;
    }

    public function modifResumValidite(string $title, string $description): bool{
        if (empty($title) || empty($description)){
            return false;
        }
        return true;
    }

    public function modifQuestionValidite(string $questionContent, array $reponsesContent, array $cheksContent): bool{
        if (!in_array(0,$cheksContent) || !in_array(1,$cheksContent)){
            return false;
        }
        if(count($reponsesContent) != count($cheksContent)){
            return false;
        }
        if(count($reponsesContent) < 2 || count($cheksContent) < 2){
            return false;
        }
        if( empty($questionContent)){
            return false;
        }
        foreach($reponsesContent as $rep){
            if (empty($rep)){
                return false;
            }
        }
        return true;
    }

    public function showQuiz(int $quizId, ?int $idQuestion = 1, bool $showAnswer = false)
    {
        if (session_status() === PHP_SESSION_NONE && ($_GET['page'] === 'test')) {
            session_start();
        }

        $max = $this->model->getMaxNbQuestion($quizId);

        // initialisation des réponses si vide
        if (!isset($_SESSION['answers'])) {
            $_SESSION['answers'] = [];
        }

        // si utilisateur a envoyé une réponse
        if (!empty($_GET['answer'])) {

            $answers = array_map('intval', $_GET['answer']);

            // on stocke pour LA QUESTION COURANTE
            $_SESSION['answers'][$idQuestion] = array($this->isCorrect($quizId, $idQuestion, $answers), $answers);

            // on passe à la question suivante
            $idQuestion++;
        }

        // fin du quiz
        if ($idQuestion > $max) {
            $question = null;
            $reponse = [];

            if ($_GET['page'] === 'standard') {
                require ROOT . '/src/views/quiz/show.php';
            } else {
                ksort($_SESSION['answers']);
                require ROOT . '/src/views/quiz/endTest.php';
            }
            return;
        }
        // récupération de la question actuelle
        $question = $this->model->getQuestion($quizId, $idQuestion);
        $reponse  = $this->model->getReponses($question['id']);

        require ROOT . '/src/views/quiz/show.php';
    }


    public function isCorrect(int $quizId, int $idQuestion, array $reponses): bool
    {
        // Récupère les bonnes réponses sous forme de tableau d'IDs
        $correctAnswers = $this->model->getCorrectAnswers($quizId, $idQuestion);

        // Normaliser les réponses de l'utilisateur en entiers
        $userIds = array_map('intval', $reponses);

        // Trier les deux tableaux pour comparer indépendamment de l'ordre
        sort($correctAnswers);
        sort($userIds);

        // Retourner true si les deux tableaux sont identiques
        return $correctAnswers === $userIds;
    }
}
