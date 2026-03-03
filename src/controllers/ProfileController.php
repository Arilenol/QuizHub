<?php
require_once ROOT . '/src/models/ProfileModel.php';
require_once ROOT . '/config/config.php';

class ProfileController
{

    private ProfileModel $model;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = getDbConnection();
        $this->model = new ProfileModel($this->db);
    }

    public function showProfile(?string $option = null, ?string $optionSuccess = null, ?string $optionError = null)
    {
        $guest = false;
        if (isset($_SESSION['id']) && $option === "creatorProfil" && $_SESSION['id'] == $_POST['creatorId']) {
            header("Location: ?page=profil");
            exit;
        }

        if (isset($_SESSION['id']) && $option !== "creatorProfil") {
            $infosUser = $this->model->getCredentials($_SESSION['id']);
            $creation = $this->model->getQuizCreated($_SESSION['id']);
            $played = $this->model->getGamesNumber($_SESSION['id']);
            $lessonCreated = $this->model->getLessonsCreated($_SESSION['id']);
            if ($option === 'showProfile') {
                $showProfileModal = true;
                $messageSuccess = $optionSuccess;
                $messageError = $optionError;
                $option = null;
            }
            if ($option !== null && $option === "showFriends") {
                $friends = $this->model->getFriends($_SESSION['id']);
            }
            if ($option !== null && $option === "showHistory") {
                $hist = $this->model->getQuizPlayed($_SESSION['id']);
            }
            if ($option === null && ($creation > 0 || $lessonCreated > 0)) {
                require_once ROOT . '/src/models/HomeModel.php';
                require_once ROOT . '/src/models/LessonModel.php';
                $modelHome = new HomeModel($this->db);
                $modelLesson = new LessonModel($this->db);
                $quiz = $modelHome->getAllCreationsByUser($_SESSION['id']);
                $lessons = $modelLesson->getAllInfoLessonsByUser($_SESSION['id']);
                if (!empty($lessons) && $lessons[0] !== null) {
                    for ($i = 0; $i < count($lessons); $i++) {
                        $quiz[] = $lessons[$i];
                    }
                }
            }
            if (isset($_GET['actionType'])) {
                if (isset($_POST['genre'])) {
                    if ($_POST['genre'] === 'leçon') {
                        $this->model->deleteLesson($_POST['idToDelete']);
                    } else {
                        $this->model->deleteQuiz($_POST['idToDelete']);
                    }
                    header("Location: ?page=profil");
                    exit;
                } else {
                    $test = $this->model->deleteFriend($_SESSION['id'], $_POST['idToDelete']);
                    header("Location: ?page=profil&action=displayFriends");
                    exit;
                }
            }
            $activeTab = $_GET['action'] ?? 'creations';
            $activeTab = $activeTab === 'displayFriends' ? 'friends' : ($activeTab === 'showHistory' ? 'history' : 'creations');
            require ROOT . '/src/views/profil.php';
        } else {

            if ($option !== null && $option === "creatorProfil") {
                if (!isset($_POST['creatorId'])) {
                    header("Location: ?page=home");
                    exit;
                }
                $creatorId = $_POST['creatorId'];
                $guest = true;
                $infosUser = $this->model->getCredentials($creatorId);
                $creation = $this->model->getQuizCreated($creatorId);
                $played = $this->model->getGamesNumber($creatorId);
                $lessonCreated = $this->model->getLessonsCreated($creatorId);
                if (isset($_SESSION['id'])) {
                    require_once ROOT . '/src/models/NotificationModel.php';
                    $modelNotification = new NotificationModel($this->db);
                    $alreadyFriend = $modelNotification->hasFriend($creatorId);
                }

                if ($creation > 0 || $lessonCreated > 0) {
                    require_once ROOT . '/src/models/HomeModel.php';
                    require_once ROOT . '/src/models/LessonModel.php';
                    $modelHome = new HomeModel($this->db);
                    $modelLesson = new LessonModel($this->db);
                    $quiz = $modelHome->getAllCreationsByUser($creatorId);
                    $lessons = $modelLesson->getAllInfoLessonsByUser($creatorId);
                    if (!empty($lessons) && $lessons[0] !== null) {
                        for ($i = 0; $i < count($lessons); $i++) {
                            $quiz[] = $lessons[$i];
                        }
                    }
                }


                require ROOT . '/src/views/profil.php';
            } else {
                echo "Problème de chargement";
            }
        }
    }

    public function saveNewInfo()
    {
        $infosUser = $this->model->getCredentials($_SESSION['id']);

        $saver = [];
        $error = [];
        $messageSuccess = '';
        $messageError = '';

        // Nettoyage des données
        $username = htmlspecialchars(trim($_POST['username'] ?? ''), ENT_QUOTES, 'UTF-8');
        $description = htmlspecialchars(trim($_POST['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
        $password = trim($_POST['password'] ?? '');
        $passwordVerif = trim($_POST['passwordVerif'] ?? '');

        if ($infosUser['username'] !== $username) {
            $success = $this->model->saveUsername($username, $_SESSION['id']);
            if ($success) {
                $saver[] = "Nom d'utilisateur";
            } else {
                $error[] = "Nom d'utilisateur";
            }
        }

        if ($infosUser['description'] !== $description) {
            $success = $this->model->saveDescription($description, $_SESSION['id']);
            if ($success) {
                $saver[] = 'Description';
            } else {
                $error[] = "Description";
            }
        }

        if ($infosUser['email'] !== $email) {
            if (empty($email)) {
                $success = false;
            } else {
                $success = $this->model->saveEmail($email, $_SESSION['id']);
            }
            if ($success) {
                $saver[] = 'Email';
            } else {
                $error[] = "Email";
            }
        }
        if (!empty($password)) {
            if ($passwordVerif === $password) {
                $pattern = '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/';

                if (!preg_match($pattern, $password)) {
                    $error[] = "Le mot de passe doit contenir au moins 8 caractères, un caractère spécial, une lettre et un chiffre";
                } else {
                    $success = $this->model->savePassword($password, $_SESSION['id']);
                    if ($success) {
                        $saver[] = 'Mot de passe';
                    } else {
                        $error[] = "Mot de passe";
                    }
                }
            } else {
                $error[] = "Vous avez donné différents mots de passe";
            }
        }
        if (!empty($saver)) {
            $messageSuccess = "Les champs suivants ont été mis à jour : " . implode(',', $saver);
        }
        if (!empty($error)) {
            $messageError = "Erreur lors de la mise à jour de : " . implode(',', $error);
        }
        $this->showProfile("showProfile", $messageSuccess, $messageError);
    }

    public function saveNewPicture()
    {
        if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Erreur lors de l'upload");
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['avatar']['tmp_name']);

        $allowed = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mime, $allowed)) {
            throw new Exception("Format d'image interdit");
        }

        $userId = $_SESSION['id'];
        $extension = $allowed[$mime];

        $filename = "user_{$userId}_" . time() . "." . $extension;
        $destination = ROOT . "/public/assets/uploads/avatars/" . $filename;

        if (!move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
            throw new Exception("Impossible de sauvegarder l'image");
        }

        $pathForDb = "assets/uploads/avatars/" . $filename;

        $this->model->savePicture($pathForDb, $userId);
        header("Location: ?page=profil");
        exit;
    }
}
