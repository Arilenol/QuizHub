<?php
require_once ROOT . '/src/models/LessonModel.php';
require_once ROOT . '/config/config.php';


class LessonController
{

    private LessonModel $model;
    private $db;

    public function __construct()
    {
        $this->db = getDbConnection();
        $this->model = new LessonModel($this->db);
    }

    public function index($id)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // récupère les données de la leçon
        $lesson = $this->model->getLesson($id);
        if (!$lesson) {
            echo "Leçon non trouvée";
            return;
        }
        // récupère les parties
        $parties = $this->model->getPart($id) ?: [];
        // récupère les exemples pour chaque partie
        $resultats = [];
        foreach ($parties as $part) {
            $exemples = $this->model->getExemple($part['id']);
            $resultats[] = $exemples;
        }

        require_once ROOT . '/src/models/LikeModel.php';

        $modelLike = new LikeModel($this->db);
        $reactions = $modelLike->getReactionsLecon($id);

        // Like/Dislike en POST
        if (isset($_POST['reaction'])) {
            if ($_POST['reaction'] === "like") {
                if ($modelLike->hasLikedLecon($id, $_SESSION['id'])) {
                    $modelLike->removeLikeLecon($id, $_SESSION['id']);
                } else {
                    $modelLike->sendLikeLecon($id, $_SESSION['id']);
                }
            } elseif ($_POST['reaction'] === "dislike") {
                if ($modelLike->hasDislikedLecon($id, $_SESSION['id'])) {
                    $modelLike->removeDislikeLecon($id, $_SESSION['id']);
                } else {
                    $modelLike->sendDislikeLecon($id, $_SESSION['id']);
                }
            }
            header("Location: ?page=lesson&categorie=view&id=" . "$id");
            exit;
        }

        $hasLiked = isset($_SESSION['id']) && $modelLike->hasLikedLecon($id, $_SESSION['id']);
        $hasDisliked = isset($_SESSION['id']) && $modelLike->hasDislikedLecon($id, $_SESSION['id']);

        // afficher la vue
        require ROOT . '/src/views/lesson/show.php';
    }

    //A DEVELOPPER
    public function createLesson()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        session_start();

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
        //var_dump($_SESSION);
        //var_dump($_POST);

        // afficher la vue

        if (!isset($_SESSION['nbParts']) || empty($_SESSION['nbParts'])) {
            $_SESSION['nbParts'] = 1;
        }
        //$nbParts = isset($_POST['nbPart'])&& !empty($_POST['nbPart']) ? $_POST['nbPart'] : 1;

        if (!isset($_SESSION['nbExemple']) || empty($_SESSION['nbExemple'])) {
            $_SESSION['nbExemple'] =  array(0 => 0);
        }

        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes") {
            unset($_SESSION['nbExemple']);
            unset($_SESSION['nbParts']);
            unset($_SESSION['POST']);
            unset($_SESSION['bouton']);
            unset($_SESSION['erreur']);
            unset($_POST);
            header('Location: index.php?page=createContent');
            exit;
        }
        if (isset($_POST['addPart']) && !empty($_POST['addPart'])) {
            $_SESSION['nbParts']++;
            $_SESSION['nbExemple'][$_SESSION['nbParts'] - 1] = 0;
            $this->contentFusionSessionPost();
            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['addExemple']) && $_POST['addExemple'] != '') {

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
                $_POST['namePart' . $i] = $_POST['namePart' . ($i + 1)];
                $_POST['contentPart' . $i] = $_POST['contentPart' . ($i + 1)];

                for ($k = 0; $k < $_SESSION['nbExemple'][$i]; $k++) {
                    $_POST['exemple' . $k . '-part' . $i] = $_POST['exemple' . $k . '-part' . ($i + 1)];
                    $_POST['reponse' . $k . '-part' . $i] = $_POST['reponse' . $k . '-part' . ($i + 1)];
                }
            }

            $last = $oldNbParts - 1;
            unset($_SESSION['nbExemple'][$last]);
            unset($_POST['namePart' . $last]);
            unset($_POST['contentPart' . $last]);
            for ($k = 0; $k < 10; $k++) {
                unset($_POST['exemple' . $k . '-part' . $last]);
                unset($_POST['reponse' . $k . '-part' . $last]);
            }


            $_SESSION['nbParts']--;
            $this->contentFusionSessionPost();
            $_SESSION['bouton'] = true;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }


        for ($i = 0; $i < $_SESSION['nbParts']; $i++) {
            for ($k = 0; $k < $_SESSION['nbExemple'][$i]; $k++) {
                if (isset($_POST['delExemple' . $k . '-part' . $i]) && $_POST['delExemple' . $k . '-part' . $i] == "yes") {
                    $_SESSION['nbExemple'][$i]--;
                    for ($e = $k; $e < $_SESSION['nbExemple'][$i]; $e++) {
                        $_POST['exemple' . $e . '-part' . $i] = $_POST['exemple' . ($e + 1) . '-part' . $i];
                        $_POST['reponse' . $e . '-part' . $i] = $_POST['reponse' . ($e + 1) . '-part' . $i];
                    }
                    unset($_POST['exemple' . $_SESSION['nbExemple'][$i] . '-part' . $i]);
                    unset($_POST['reponse' . $_SESSION['nbExemple'][$i] . '-part' . $i]);
                    $this->contentFusionSessionPost();
                    $_SESSION['bouton'] = true;
                    header('Location: ' . $_SERVER['REQUEST_URI']);
                    exit;
                }
            }
        }

        $quizSelected = (isset($_POST['linkedQuiz']) && !empty($_POST['linkedQuiz']) && $_POST['linkedQuiz'] != 'Aucun') ? (int)$_POST['linkedQuiz'] : null;

        if ($_SESSION['bouton'] === false) {
            $this->contentFusionSessionPost();
        }

        $LessonTitle = isset($_SESSION['POST']['LessonTitle']) ? $_SESSION['POST']['LessonTitle'] : '';
        $desc = isset($_SESSION['POST']['LessonDescription']) ? $_SESSION['POST']['LessonDescription'] : '';

        $TAB_CATEGORIE = $this->model->getAllCategories();
        $TAB_CATEGORIE_CHOISI = array();
        if (isset($_SESSION['POST']['categories'])) {
            $TAB_CATEGORIE_CHOISI = $_SESSION['POST']['categories'];
        }

        //var_dump($quizSelected);
        $TAB_CONTENU = array();
        for ($i = 0; $i < $_SESSION['nbParts']; $i++) {
            $partContent = array(
                'name' => isset($_SESSION['POST']['namePart' . $i]) ? $_SESSION['POST']['namePart' . $i] : '',
                'content' => isset($_SESSION['POST']['contentPart' . $i]) ? $_SESSION['POST']['contentPart' . $i] : '',
                'exemples' => array()
            );
            for ($k = 0; $k < $_SESSION['nbExemple'][$i]; $k++) {
                $exampleContent = array(
                    'consigne' => isset($_SESSION['POST']['exemple' . $k . '-part' . $i]) ? $_SESSION['POST']['exemple' . $k . '-part' . $i] : '',
                    'reponse' => isset($_SESSION['POST']['reponse' . $k . '-part' . $i]) ? $_SESSION['POST']['reponse' . $k . '-part' . $i] : ''
                );
                $partContent['exemples'][] = $exampleContent;
            }
            $TAB_CONTENU[] = $partContent;
        }
        $TAB_AMI = $this->model->getAmis($_SESSION['id']);

        $TAB_AMI_CHOISI = array();
        if (isset($_SESSION['POST']['amiDispo'])) {
            $TAB_AMI_CHOISI = $_SESSION['POST']['amiDispo'];
        }

        if (isset($_POST['create']) && $_POST['create'] == "yes") {
            $LessonTitle = $_POST['LessonTitle'];
            $desc = $_POST['LessonDescription'];
            $this->contentFusionSessionPost();
            if ($this->verifValidite()) {
                $reussi = $this->model->createLesson($id, $LessonTitle, $desc, $_SESSION['nbParts'], $_SESSION['nbExemple'], $TAB_CONTENU, $TAB_AMI_CHOISI, $TAB_CATEGORIE_CHOISI, $_SESSION['POST']['disponibilite'], $quizSelected);
                //je mets une redirecion pour être sûr qu'on ne l'oublie pas après
                if ($reussi) {
                    unset($_SESSION['nbExemple']);
                    unset($_SESSION['nbParts']);
                    unset($_SESSION['POST']);
                    unset($_SESSION['bouton']);
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


        $quizzes = $this->model->getQuizByAuthor($id);

        $_SESSION['bouton'] = false;

        require ROOT . '/src/views/lesson/createLesson.php';
    }

    public function verifValidite()
    {
        if (!isset($_SESSION['POST']['LessonTitle']) || empty($_SESSION['POST']['LessonDescription'])) {
            return false;
        }
        if (!isset($_SESSION['POST']['LessonDescription']) || empty($_SESSION['POST']['LessonDescription'])) {
            return false;
        }
        for ($i = 0; $i < $_SESSION['nbParts']; $i++) {
            if (!isset($_SESSION['POST']['namePart' . $i]) || empty($_SESSION['POST']['namePart' . $i])) {
                return false;
            }
            if (!isset($_SESSION['POST']['contentPart' . $i]) || empty($_SESSION['POST']['contentPart' . $i])) {
                return false;
            }
            for ($k = 0; $k < $_SESSION['nbExemple'][$i]; $k++) {
                if (!isset($_SESSION['POST']['exemple' . $k . '-part' . $i]) || empty($_SESSION['POST']['exemple' . $k . '-part' . $i])) {
                    return false;
                }
                if (!isset($_SESSION['POST']['reponse' . $k . '-part' . $i]) || empty($_SESSION['POST']['reponse' . $k . '-part' . $i])) {
                    return false;
                }
            }
        }
        if (!isset($_SESSION['POST']['categories']) || count($_SESSION['POST']['categories']) === 0) {
            return false;
        }
        return true;
    }

    public function contentFusionSessionPost()
    {
        if (isset($_POST['LessonTitle'])) {
            $_SESSION['POST']['LessonTitle'] = $_POST['LessonTitle'];
        }
        if (isset($_POST['LessonDescription'])) {
            $_SESSION['POST']['LessonDescription'] = $_POST['LessonDescription'];
        }
        for ($i = 0; $i < $_SESSION['nbParts']; $i++) {
            if (isset($_POST['namePart' . $i])) {
                $_SESSION['POST']['namePart' . $i] = $_POST['namePart' . $i];
            }
            if (isset($_POST['contentPart' . $i])) {
                $_SESSION['POST']['contentPart' . $i] = $_POST['contentPart' . $i];
            }
            for ($k = 0; $k < $_SESSION['nbExemple'][$i]; $k++) {
                if (isset($_POST['exemple' . $k . '-part' . $i])) {
                    $_SESSION['POST']['exemple' . $k . '-part' . $i] = $_POST['exemple' . $k . '-part' . $i];
                }
                if (isset($_POST['reponse' . $k . '-part' . $i])) {
                    $_SESSION['POST']['reponse' . $k . '-part' . $i] = $_POST['reponse' . $k . '-part' . $i];
                }
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

    public function modifyLesson($id)
    {
        session_start();
        $idLesson = (int)$id;
        //die("erreur :".$idQuiz);
        $taille = $this->model->getLessonSize($idLesson);
        $user_id = $this->model->getUserIdFromLesson($idLesson);
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
            $this->model->updateCategoriesLesson($idLesson, $_POST['categories']);
            unset($_POST['categories']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['appliquerDispo'])) {
            $disponibilite = isset($_POST['disponibilite']) ? $_POST['disponibilite'] : 'public';
            $amiDispo = isset($_POST['amiDispo']) && is_array($_POST['amiDispo']) ? $_POST['amiDispo'] : [];
            $this->model->updateDisponibiliteLesson($idLesson, $disponibilite, $amiDispo);
            unset($_POST['appliquerDispo']);
            unset($_POST['disponibilite']);
            unset($_POST['amiDispo']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['appliquerAssoc'])) {
            $quizAssoc = $_POST['appliquerAssoc'];
            $this->model->updateQuizAssociated($idLesson, $quizAssoc);
            unset($_POST['appliquerAssoc']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['appliquerPart'])) {
            $iPart = (int)$_POST['appliquerPart'];
            $partTitle = (isset($_POST['title']) && !empty($_POST['title'])) ? $_POST['title'] : '';
            $partContent = (isset($_POST['content']) && !empty($_POST['content'])) ? $_POST['content'] : '';

            if ($this->modifPartValidite($partTitle, $partContent)) {
                if ($taille < $iPart + 1) {
                    $this->model->addPartToLesson($idLesson, $iPart + 1, $partTitle, $partContent);
                }
                $this->model->updatePartLesson($idLesson, $iPart + 1, $partTitle, $partContent);
            } else {
                die('erreur de validation du contenu d\'une partie');
            }
            unset($_POST['appliquerPart']);
            unset($_POST['title']);
            unset($_POST['content']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['appliquerEx'])) {
            if (isset($_POST['appliquerEx']) && isset($_POST['numExemple'])) {
                $iPart = (int)$_POST['appliquerEx'];
                $kEx = (int)$_POST['numExemple'];
                $consigne = isset($_POST['textConsigne']) && !empty($_POST['textConsigne']) ? $_POST['textConsigne'] : '';
                $reponse = isset($_POST['textReponse']) && !empty($_POST['textReponse']) ? $_POST['textReponse'] : '';

                if ($this->modifierExValidite($consigne, $reponse)) {
                    $taillek = $this->model->getNumberExFromPart($idLesson, $iPart + 1);
                    if ($taillek < $kEx + 1) {
                        $this->model->addExToPart($idLesson, $iPart + 1, $kEx + 1, $consigne, $reponse);
                    } else {
                        $this->model->updateExFromPart($idLesson, $iPart + 1, $kEx + 1, $consigne, $reponse);
                    }
                } else {
                    die('erreur de validation du contenu d\'un exemple');
                }
                unset($_POST['appliquerEx']);
                unset($_POST['numExemple']);
                unset($_POST['textConsigne']);
                unset($_POST['textReponse']);
                header('Location: ' . $_SERVER['REQUEST_URI']);
                exit;
            }
        }
        if (isset($_POST['delEx'])) {
            if (isset($_POST['delNumEx'])) {
                $iPart = (int)$_POST['delEx'];
                $kEx = (int)$_POST['delNumEx'];
                $this->model->deleteExFromPart($idLesson, $iPart + 1, $kEx + 1);
            }
            unset($_POST['delEx']);
            unset($_POST['delNumEx']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['appliquerResum'])) {
            $title = isset($_POST['LessonTitle']) ? $_POST['LessonTitle'] : '';
            $description = isset($_POST['LessonDescription']) ? $_POST['LessonDescription'] : '';
            if ($this->modifResumValidite($title, $description)) {
                $this->model->updateResumLesson($idLesson, $title, $description);
            } else {
                die("Erreur de validation du résumé");
            }
            unset($_POST['appliquerResum']);
            unset($_POST['LessonTitle']);
            unset($_POST['LessonDescription']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['DelPart'])) {
            $iPart = (int)$_POST['DelPart'];
            $this->model->deletePartFromLesson($idLesson, $iPart + 1);
            unset($_POST['DelPart']);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
        if (isset($_POST['Annuler'])) {
            unset($_POST);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }




        $lessonInfos = $this->model->getLessonInfos($idLesson);
        $quizzes = $this->model->getQuizByAuthor($user_id);
        $TAB_PART = $this->model->getPartsExFromLesson($idLesson);
        $TAB_CATEGORIES = $this->model->getCategoriesFromLesson($idLesson);
        $ALL_CATEGORIES = $this->model->getAllCategories();
        $ALL_AMIS = $this->model->getAmis($user_id);
        $TAB_AMIS = $this->model->getAmisSelection($idLesson);




        //var_dump($_POST);
        //var_dump($_SESSION);
        $erreur = false;

        require ROOT . '/src/views/lesson/modifyLesson.php';
    }

    public function modifResumValidite(string $title, string $description): bool
    {
        if (empty($title) || empty($description)) {
            return false;
        }
        return true;
    }

    public function modifPartValidite(string $title, string $content): bool
    {
        if (empty($content)) {
            return false;
        }
        if (empty($title)) {
            return false;
        }
        return true;
    }

    public function modifierExValidite(string $consigne, string $content)
    {
        if (empty($content)) {
            return false;
        }
        if (empty($consigne)) {
            return false;
        }
        return true;
    }
}
