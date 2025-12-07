<?php
require_once ROOT . '/src/models/FlashCardModel.php';
require_once ROOT . '/config/config.php';

class FlashCardController
{

    private FlashCardModel $model;

    public function __construct()
    {
        session_start();
        $db = getDbConnection();
        $this->model = new FlashCardModel($db);
    }

    // Charge la première question du quiz
    public function preload(int $quizId)
    {
        $_SESSION['remainingQuestions'] = $this->model->getFlashCardById($quizId) ?: [];
        if (empty($_SESSION['remainingQuestions'])) {
            echo "Aucune question disponible";
            return;
        }
        $firstId = $_SESSION['remainingQuestions'][0];
        $this->showQuestion($firstId);
    }

    // Affiche une question spécifique
    public function questionById(int $id)
    {
        $remaining = $_SESSION['remainingQuestions'] ?? [];
        if (!in_array($id, $remaining)) {
            echo "Question invalide";
            return;
        }

        $this->showQuestion($id);
    }
    // Méthode privée pour centraliser l’affichage
    private function showQuestion(int $id)
    {
        $question = $this->model->getInfoFlashCardById($id);
        $viewData = $this->prepareViewData($question);
        require ROOT . '/src/views/quiz/flashcard.php';
    }

    private function prepareViewData(array $question): array
    {
        $remaining = $_SESSION['remainingQuestions'] ?? [];
        $currentIndex = array_search($question['id'], $remaining);

        return [
            'question'   => $question,
            'quizId'     => $question['quiz_id'],
            'showAnswer' => ($_GET['reponse'] ?? '') === 'visible',
            'prevId'     => $remaining[$currentIndex - 1] ?? null,
            'nextId'     => $remaining[$currentIndex + 1] ?? null,
        ];
    }

    public function createFlashcard(){
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

        if (!isset($_SESSION['nbCartes']) || empty($_SESSION['nbCartes'])){
            $_SESSION['nbCartes'] = 1;
        }

        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes"){
            unset($_SESSION['nbCartes']);
            unset($_SESSION['bouton']);
            unset($_SESSION['POST']);
            unset($_SESSION['erreur']);
            unset($_POST);
            header('Location: index.php?page=createContent');
            exit;
        }

        if (isset($_POST['addCard']) && !empty($_POST['addCard'])){
            $_SESSION['nbCartes']++;
            $this->contentFusionSessionPost();
            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        if (isset($_POST['DelCard']) && $_POST['DelCard'] !== '') {
            if ($_SESSION['nbCartes'] > 1){
                $idx = (int)$_POST['DelCard'];
                $oldNbParts = $_SESSION['nbCartes']; 

                
                for ($i = $idx; $i < $oldNbParts - 1; $i++) {
                    $_POST['cardQuestion'.$i] = $_POST['cardQuestion'.($i + 1)];
                    $_POST['cardReponse'.$i] = $_POST['cardReponse'.($i + 1)];
                    
                }
                
                $last = $oldNbParts - 1;
            }
            
            unset($_POST['cardQuestion'.$last]);
            unset($_POST['cardReponse'.$last]);
 
            $_SESSION['nbCartes']--;
            $this->contentFusionSessionPost();
            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        if ($_SESSION['bouton'] === false){
            $this->contentFusionSessionPost();
        }

        $CardsTitle = isset($_SESSION['POST']['FlashcardTitle']) ? $_SESSION['POST']['FlashcardTitle'] : '';
        $desc = isset($_SESSION['POST']['FlashcardDescription']) ? $_SESSION['POST']['FlashcardDescription'] : '';

        $TAB_CATEGORIE = $this->model->getAllCategories();
        $TAB_CATEGORIE_CHOISI = array();
        if (isset($_SESSION['POST']['categories'])){
            $TAB_CATEGORIE_CHOISI = $_SESSION['POST']['categories'];
        }

        $TAB_CONTENU = array();
        for ($i = 0; $i < $_SESSION['nbCartes'] ; $i++){
            $partContent = array(
                'question' => isset($_SESSION['POST']['cardQuestion'.$i]) ? $_SESSION['POST']['cardQuestion'.$i] : '',
                'reponse' => isset($_SESSION['POST']['cardReponse'.$i]) ? $_SESSION['POST']['cardReponse'.$i] : ''
            );
            $TAB_CONTENU[] = $partContent;
        }
        $TAB_AMI = $this->model->getAmis($_SESSION['id']);

        $TAB_AMI_CHOISI = array();
        if (isset($_SESSION['POST']['amiDispo'])){
            $TAB_AMI_CHOISI = $_SESSION['POST']['amiDispo'];
        }

        if (isset($_POST['create']) && $_POST['create'] == "yes"){
            $CardsTitle = $_POST['FlashcardTitle'];
            $desc = $_POST['FlashcardDescription'];
            $this->contentFusionSessionPost();
            if ($this->verifValidite()){

                $reussi = $this->model->createFlashcard($_SESSION['nbCartes'], $id, $CardsTitle, $desc, $TAB_CONTENU, $TAB_AMI_CHOISI, $TAB_CATEGORIE_CHOISI, $_SESSION['POST']['disponibilite']);
                if ($reussi){
                    unset($_SESSION['nbCartes']);
                    unset($_SESSION['bouton']);
                    unset($_SESSION['POST']);
                    unset($_SESSION['erreur']);
                    unset($_POST);
                    header('Location: index.php?page=home');
                    exit; 
                }
                else{
                    unset($_POST['create']);
                    $_SESSION['bouton'] = true;
                    header('Location: '.$_SERVER['REQUEST_URI']);
                    exit;
                }
                
            }
            else{
                $_SESSION['erreur'] = true;
                unset($_POST['create']);
                $_SESSION['bouton'] = true;
                header('Location: '.$_SERVER['REQUEST_URI']);
                exit;
            }
            
                   
        }

        $_SESSION['bouton'] = false;

        require ROOT . '/src/views/Quiz/createFlashcard.php';

    }

    public function contentFusionSessionPost(){
        if(isset($_POST['FlashcardTitle'])){
            $_SESSION['POST']['FlashcardTitle'] = $_POST['FlashcardTitle'];
        }
        if (isset($_POST['FlashcardDescription'])){
            $_SESSION['POST']['FlashcardDescription'] = $_POST['FlashcardDescription'];
        }
        for ($i = 0; $i < $_SESSION['nbCartes'] ; $i ++){
            if (isset($_POST['cardQuestion'.$i])){
                $_SESSION['POST']['cardQuestion'.$i] = $_POST['cardQuestion'.$i];
            }
            if (isset($_POST['cardReponse'.$i])){
                $_SESSION['POST']['cardReponse'.$i] = $_POST['cardReponse'.$i];
            }
        }
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
        if (!isset($_SESSION['POST']['FlashcardTitle']) || empty($_SESSION['POST']['FlashcardTitle'])){
            return false;
        }
        if(!isset($_SESSION['POST']['FlashcardDescription']) || empty($_SESSION['POST']['FlashcardDescription'])){
            return false;
        }
        for ($i = 0; $i < $_SESSION['nbCartes'] ; $i++){
            if(!isset($_SESSION['POST']['cardQuestion'.$i]) || empty($_SESSION['POST']['cardQuestion'.$i])){
                return false;
            }
            if(!isset($_SESSION['POST']['cardReponse'.$i]) || empty($_SESSION['POST']['cardReponse'.$i])){
                return false;
            }
        }
        if(!isset($_SESSION['POST']['categories']) || count($_SESSION['POST']['categories']) === 0){
            return false;
        }
        return true;
    }

    public function modifyFlashcard($id){
        require ROOT . '/src/views/Quiz/createFlashcard.php';
    }
    public function endFlashCard(){
        $viewData = null;
        $quizId = $_GET['id'];
        require ROOT . '/src/views/quiz/flashcard.php';
    }
}
