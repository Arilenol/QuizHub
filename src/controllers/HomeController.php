<?php
require_once ROOT . '/src/models/HomeModel.php';
require_once ROOT . '/config/config.php';
require_once ROOT . '/src/models/LessonModel.php';


class HomeController
{

    private HomeModel $model;
    private $db;


    public function __construct()
    {
        $this->db = getDbConnection();
        $this->model = new HomeModel($this->db);
    }

    public function index()
    {
        $modelLesson = new LessonModel($this->db);

        // récupère les données
        $quiz = $this->model->getAllInfo();

        $lessons = $modelLesson->getAllInfoLessons();
        if (isset($_SESSION['id']) && !empty($_SESSION['id'])) {
            // récupère la streak actuelle
            $streak = $this->myUpdateStreak($_SESSION['id']);
            $_SESSION['streak'] = $streak;
            $_SESSION['highestStreak'] = $this->model->getLongestStreak($_SESSION['id']);

            $friendQuiz = $this->model->getAllCreationsByFriends($_SESSION['id']);
            $quizNextPart = $this->model->getAllCreationsByUser($_SESSION['id']);
            $lessonsByUser = $modelLesson->getAllInfoLessonsByUser($_SESSION['id']);
            if ($lessonsByUser !== false && !empty($lessonsByUser)) {
                for ($i = 0; $i < count($lessonsByUser); $i++) {
                    $quizNextPart[] = $lessonsByUser[$i];
                }
            }
        } else {
            $quizNextPart = $this->model->getAllNewCreations();
        }
        // afficher la vue
        require ROOT . '/src/views/home.php';
    }


    public function myUpdateStreak(int|string $id): int
    {
        $lastDate = $this->model->getLastDateQuizPlayed($id);
        $today = new DateTime('today');
        // jamais joué
        if ($lastDate === null && $this->model->checkIfNotInstance($id)) {
            $this->model->createInstance($id);
            return 0;
        }

        if ($lastDate === null) {
            return 0;
        }

        $lastActivity = $this->model->getLastActivity($id);

        if ($lastActivity === null || $lastActivity < $lastDate) {
            $lastActivity = $this->model->getLastActivity($id);
            $this->model->updateLastActivity($id, $lastDate);
            if ($lastActivity !== null) {
                $diffDays = $lastActivity->diff($lastDate)->days;
                if ($diffDays === 1) {
                    $this->model->incrementStreak($id);
                } else if ($diffDays > 1) {
                    $this->model->setCurrentStreak($id, 1);
                }
                $this->model->updateLongestIfNeeded($id, $this->model->getCurrentStreak($id));
            }
        }

        if ($this->model->checkDateIsNull($id)) {
            return 0;
        }

        $lastActivity = $this->model->getLastActivity($id);        
        // Vérifier que $lastActivity n'est pas null avant d'appeler diff()
        if ($lastActivity === null) {
            return 0;
        }
                $diffDays = $lastActivity->diff($today)->days;

        // déjà joué aujourd’hui → rien
        if ($diffDays === 0 || $diffDays === 1) {
            return $this->model->getCurrentStreak($id);
        } else {
            $this->model->setCurrentStreak($id, 0);
            return $this->model->getCurrentStreak($id);
        }
    }
}
