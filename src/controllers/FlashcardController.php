<?php
require_once ROOT . '/src/models/FlashcardModel.php';
require_once ROOT . '/config/config.php';

class FlashcardController
{

    private FlashcardModel $model;
    private $db;

    public function __construct()
    {
        session_start();
        $this->db = getDbConnection();
        $this->model = new FlashcardModel($this->db);
    }

    // Charge la première question du quiz
    public function preload(int $quizId)
    {
        $_SESSION['remainingQuestions'] = $this->model->getFlashcardById($quizId) ?: [];
        if (empty($_SESSION['remainingQuestions'])) {
            echo "Aucune question disponible";
            return;
        }
        $firstId = $_SESSION['remainingQuestions'][0];
        require ROOT . '/src/models/HistoricModel.php';
        $modelHistoric = new HistoricModel($this->db);
        if (isset($_SESSION['id'])) {
            $modelHistoric->saveHistoric($quizId, $_SESSION['id']);
        }
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
        $question = $this->model->getInfoFlashcardById($id);
        $viewData = $this->prepareViewData($question);
        $current = array_keys($_SESSION['remainingQuestions'], $id)[0] + 1;
        $total = count($_SESSION['remainingQuestions']);
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

    public function createFlashcard()
    {
        if (isset($_SESSION['id'])) {
            $id = $_SESSION['id'];
        } else {
            header('Location: index.php?page=home');
            exit;
        }
        if (!isset($_SESSION['POST'])) {
            $_SESSION['POST'] = [];
        }
        if (!isset($_SESSION['POST']['disponibilite'])) {
            $_SESSION['POST']['disponibilite'] = 'public';
        }
        if (!isset($_SESSION['bouton'])) {
            $_SESSION['bouton'] = false;
        }
        if (!isset($_SESSION['erreur'])) {
            $_SESSION['erreur'] = false;
        }

        if (!isset($_SESSION['nbCartes']) || empty($_SESSION['nbCartes'])) {
            $_SESSION['nbCartes'] = 1;
        }

        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes") {
            unset($_SESSION['nbCartes']);
            unset($_SESSION['bouton']);
            unset($_SESSION['POST']);
            unset($_SESSION['erreur']);
            unset($_POST);
            header('Location: index.php?page=createContent');
            exit;
        }

        if (isset($_POST['addCard']) && !empty($_POST['addCard'])) {
            $_SESSION['nbCartes']++;
            $this->contentFusionSessionPost();
            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        if (isset($_POST['DelCard']) && $_POST['DelCard'] !== '') {
            if ($_SESSION['nbCartes'] > 1) {
                $idx = (int)$_POST['DelCard'];
                $oldNbParts = $_SESSION['nbCartes'];


                for ($i = $idx; $i < $oldNbParts - 1; $i++) {
                    $_POST['cardQuestion' . $i] = $_POST['cardQuestion' . ($i + 1)];
                    $_POST['cardReponse' . $i] = $_POST['cardReponse' . ($i + 1)];
                }

                $last = $oldNbParts - 1;
            }

            unset($_POST['cardQuestion' . $last]);
            unset($_POST['cardReponse' . $last]);

            $_SESSION['nbCartes']--;
            $this->contentFusionSessionPost();
            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        if ($_SESSION['bouton'] === false) {
            $this->contentFusionSessionPost();
        }

        $CardsTitle = isset($_SESSION['POST']['FlashcardTitle']) ? $_SESSION['POST']['FlashcardTitle'] : '';
        $desc = isset($_SESSION['POST']['FlashcardDescription']) ? $_SESSION['POST']['FlashcardDescription'] : '';

        $TAB_CATEGORIE = $this->model->getAllCategories();
        $TAB_CATEGORIE_CHOISI = array();
        if (isset($_SESSION['POST']['categories'])) {
            $TAB_CATEGORIE_CHOISI = $_SESSION['POST']['categories'];
        }

        $TAB_CONTENU = array();
        for ($i = 0; $i < $_SESSION['nbCartes']; $i++) {
            $partContent = array(
                'question' => isset($_SESSION['POST']['cardQuestion' . $i]) ? $_SESSION['POST']['cardQuestion' . $i] : '',
                'reponse' => isset($_SESSION['POST']['cardReponse' . $i]) ? $_SESSION['POST']['cardReponse' . $i] : ''
            );
            $TAB_CONTENU[] = $partContent;
        }
        $TAB_AMI = $this->model->getAmis($_SESSION['id']);

        $TAB_AMI_CHOISI = array();
        if (isset($_SESSION['POST']['amiDispo'])) {
            $TAB_AMI_CHOISI = $_SESSION['POST']['amiDispo'];
        }

        if (isset($_POST['create']) && $_POST['create'] == "yes") {
            $CardsTitle = $_POST['FlashcardTitle'];
            $desc = $_POST['FlashcardDescription'];
            $this->contentFusionSessionPost();
            if ($this->verifValidite()) {

                $reussi = $this->model->createFlashcard($_SESSION['nbCartes'], $id, $CardsTitle, $desc, $TAB_CONTENU, $TAB_AMI_CHOISI, $TAB_CATEGORIE_CHOISI, $_SESSION['POST']['disponibilite']);
                if ($reussi) {
                    unset($_SESSION['nbCartes']);
                    unset($_SESSION['bouton']);
                    unset($_SESSION['POST']);
                    unset($_SESSION['erreur']);
                    unset($_POST);
                    header('Location: index.php?page=home');
                    exit;
                } else {
                    unset($_POST['create']);
                    $_SESSION['bouton'] = true;
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit;
                }
            } else {
                $_SESSION['erreur'] = true;
                unset($_POST['create']);
                $_SESSION['bouton'] = true;
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
        }

        $_SESSION['bouton'] = false;

        require ROOT . '/src/views/quiz/createFlashcard.php';
    }

    public function contentFusionSessionPost()
    {
        if (isset($_POST['FlashcardTitle'])) {
            $_SESSION['POST']['FlashcardTitle'] = $_POST['FlashcardTitle'];
        }
        if (isset($_POST['FlashcardDescription'])) {
            $_SESSION['POST']['FlashcardDescription'] = $_POST['FlashcardDescription'];
        }
        for ($i = 0; $i < $_SESSION['nbCartes']; $i++) {
            if (isset($_POST['cardQuestion' . $i])) {
                $_SESSION['POST']['cardQuestion' . $i] = $_POST['cardQuestion' . $i];
            }
            if (isset($_POST['cardReponse' . $i])) {
                $_SESSION['POST']['cardReponse' . $i] = $_POST['cardReponse' . $i];
            }
        }
        if (isset($_POST['disponibilite'])) {
            $_SESSION['POST']['disponibilite'] = $_POST['disponibilite'];
        }
        if (isset($_POST['amiDispo'])) {
            $_SESSION['POST']['amiDispo'] = $_POST['amiDispo'];
        }
        if (isset($_POST['categories'])) {
            $_SESSION['POST']['categories'] = $_POST['categories'];
        }
    }

    public function verifValidite()
    {
        if (!isset($_SESSION['POST']['FlashcardTitle']) || empty($_SESSION['POST']['FlashcardTitle'])) {
            return false;
        }
        if (!isset($_SESSION['POST']['FlashcardDescription']) || empty($_SESSION['POST']['FlashcardDescription'])) {
            return false;
        }
        for ($i = 0; $i < $_SESSION['nbCartes']; $i++) {
            if (!isset($_SESSION['POST']['cardQuestion' . $i]) || empty($_SESSION['POST']['cardQuestion' . $i])) {
                return false;
            }
            if (!isset($_SESSION['POST']['cardReponse' . $i]) || empty($_SESSION['POST']['cardReponse' . $i])) {
                return false;
            }
        }
        if (!isset($_SESSION['POST']['categories']) || count($_SESSION['POST']['categories']) === 0) {
            return false;
        }
        return true;
    }

    public function modifyFlashcard($id)
    {
        $idFlashcard = (int)$id;
        //die("erreur :".$idQuiz);
        $taille = $this->model->getFlashcardSize($idFlashcard);
        $user_id = $this->model->getUserIdFromFlashcard($idFlashcard);
        if (!isset($_SESSION['id']) || $user_id != $_SESSION['id']) {
            header('Location: index.php?page=home');
            exit;
        }
        if ($taille === 0) {
            header('Location: index.php?page=home');
            exit;
        }
        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes") {
            unset($_POST);
            header('Location: index.php?page=createContent');
            exit;
        }
        if (isset($_POST['categories']) && !empty($_POST['categories'])) {
            $this->model->updateCategoriesFlashcard($idFlashcard, $_POST['categories']);
            unset($_POST['categories']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['appliquerDispo'])) {
            $disponibilite = isset($_POST['disponibilite']) ? $_POST['disponibilite'] : 'public';
            $amiDispo = isset($_POST['amiDispo']) && is_array($_POST['amiDispo']) ? $_POST['amiDispo'] : [];
            $this->model->updateDisponibiliteFlashcard($idFlashcard, $disponibilite, $amiDispo);
            unset($_POST['appliquerDispo']);
            unset($_POST['disponibilite']);
            unset($_POST['amiDispo']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['appliquerCard'])) {
            $iCard = (int)$_POST['appliquerCard'];
            $cardQuestion = (isset($_POST['cardQuestion']) && !empty($_POST['cardQuestion'])) ? $_POST['cardQuestion'] : '';
            $cardResponse = (isset($_POST['cardResponse']) && !empty($_POST['cardResponse'])) ? $_POST['cardResponse'] : '';

            if ($this->modifCardValidite($cardQuestion, $cardResponse)) {
                if ($taille < $iCard + 1) {
                    $this->model->addCardToFlashcard($idFlashcard, $iCard + 1, $cardQuestion, $cardResponse);
                }
                $this->model->updateCardFromFlashcard($idFlashcard, $iCard + 1, $cardQuestion, $cardResponse);
            } else {
                die('erreur de validation du contenu d\'une partie');
            }
            unset($_POST['appliquerCard']);
            unset($_POST['cardQuestion']);
            unset($_POST['cardResponse']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['appliquerResum'])) {
            $title = isset($_POST['FlashcardTitle']) ? $_POST['FlashcardTitle'] : '';
            $description = isset($_POST['FlashcardDescription']) ? $_POST['FlashcardDescription'] : '';
            if ($this->modifResumValidite($title, $description)) {
                $this->model->updateResumFlashcard($idFlashcard, $title, $description);
            } else {
                die("Erreur de validation du résumé");
            }
            unset($_POST['appliquerResum']);
            unset($_POST['FlashcardTitle']);
            unset($_POST['FlashcardDescription']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['DelCard'])) {
            $iCard = (int)$_POST['DelCard'];
            $this->model->deleteCardFromFlashcard($idFlashcard, $iCard + 1);
            unset($_POST['DelCard']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['Annuler'])) {
            unset($_POST);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }




        $flashcardInfos = $this->model->getFlashcardInfos($idFlashcard);
        $TAB_CARD = $this->model->getCardsFromFlashcard($idFlashcard);
        $TAB_CATEGORIES = $this->model->getCategoriesFromFlashcard($idFlashcard);
        $ALL_CATEGORIES = $this->model->getAllCategories();
        $ALL_AMIS = $this->model->getAmis($user_id);
        $TAB_AMIS = $this->model->getAmisSelection($idFlashcard);




        //var_dump($_POST);
        //var_dump($_SESSION);
        $erreur = false;

        require ROOT . '/src/views/Quiz/modifyFlashcard.php';
    }

    public function modifCardValidite(string $cardQuestion, string $cardResponse)
    {
        if (empty($cardQuestion) || empty($cardResponse)) {
            return false;
        }
        return true;
    }

    public function modifResumValidite(string $title, string $description)
    {
        if (empty($title) || empty($description)) {
            return false;
        }
        return true;
    }

    public function endFlashcard()
    {
        $viewData = null;
        $quizId = $_GET['id'];
        require_once ROOT . '/src/models/LikeModel.php';
        $modelLike = new LikeModel($this->db);
        if (isset($_POST['reaction'])) {
            if ($_POST['reaction'] === "like") {
                if ($modelLike->hasLiked($quizId, $_SESSION['id'])) {
                    $modelLike->removeLike($quizId, $_SESSION['id']);
                } else {
                    $modelLike->sendLike($quizId, $_SESSION['id']);
                }
            } elseif ($_POST['reaction'] === "dislike") {
                if ($modelLike->hasDisliked($quizId, $_SESSION['id'])) {
                    $modelLike->removeDislike($quizId, $_SESSION['id']);
                } else {
                    $modelLike->sendDislike($quizId, $_SESSION['id']);
                }
            }
            // Évite double envoi en cas de F5
            header("Location: ?page=flashcard&action=end&id=$quizId");
            exit;
        }
        $reactions = $modelLike->getReactions($quizId);
        $hasLiked = isset($_SESSION['id']) && $modelLike->hasLiked($quizId, $_SESSION['id']);
        $hasDisliked = isset($_SESSION['id']) && $modelLike->hasDisliked($quizId, $_SESSION['id']);
        require ROOT . '/src/views/quiz/flashcard.php';
    }
}
