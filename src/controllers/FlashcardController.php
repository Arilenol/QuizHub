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
        //-----------------------------test--------------------------------
        $_SESSION['id'] = 1;
        //-----------------------------test--------------------------------
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
        if (!isset($_SESSION['bouton'])){
            $_SESSION['bouton'] = false;
        }

        $title = isset($_POST['FlashcardTitle']) ? $_POST['FlashcardTitle'] : '';
        $desc = isset($_POST['FlashcardDescription']) ? $_POST['FlashcardDescription'] : '';

        if (!isset($_SESSION['nbCartes']) || empty($_SESSION['nbCartes'])){
            $_SESSION['nbCartes'] = 1;
        }

        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes"){
            unset($_SESSION['nbCartes']);

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

        $TAB_CONTENU = array();
        for ($i = 0; $i < $_SESSION['nbCartes'] ; $i++){
            $partContent = array(
                'question' => isset($_SESSION['POST']['cardQuestion'.$i]) ? $_SESSION['POST']['cardQuestion'.$i] : '',
                'reponse' => isset($_SESSION['POST']['cardReponse'.$i]) ? $_SESSION['POST']['cardReponse'.$i] : ''
            );
            $TAB_CONTENU[] = $partContent;
        }

        if (isset($_POST['create']) && $_POST['create'] == "yes"){
            if (isset($_POST['FlashcardTitle']) && !empty($_POST['FlashcardTitle'])  && isset($_POST['FlashcardDescription']) && !empty($_POST['FlashcardDescription'])){
                $title = $_POST['FlashcardTitle'];
                $desc = $_POST['FlashcardDescription'];
                $this->model->createFlashcard($_SESSION['nbCartes'], $id, $title, $desc, $TAB_CONTENU);
                //je mets une redirecion pour être sûr qu'on ne l'oublie pas après
                unset($_SESSION['nbCartes']);
                unset($_POST);
                header('Location: index.php?page=home');
                exit;
            }
            
        }

        $_SESSION['bouton'] = false;

        require ROOT . '/src/views/Quiz/createFlashcard.php';

    }

    public function contentFusionSessionPost(){
        for ($i = 0; $i < $_SESSION['nbCartes'] ; $i ++){
            if (isset($_POST['cardQuestion'.$i])){
                $_SESSION['POST']['cardQuestion'.$i] = $_POST['cardQuestion'.$i];
            }
            if (isset($_POST['cardReponse'.$i])){
                $_SESSION['POST']['cardReponse'.$i] = $_POST['cardReponse'.$i];
            }
        }
    }
}
