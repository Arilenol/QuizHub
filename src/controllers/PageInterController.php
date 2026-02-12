<?php
require_once ROOT . '/src/models/PageInterModel.php';
require_once ROOT . '/config/config.php';

class PageInterController
{

    private PageInterModel $model;
    private $db;

    public function __construct()
    {
        $this->db = getDbConnection();
        $this->model = new PageInterModel($this->db);
    }

    public function index($quizId,$type)
    {
        session_start();

        if (isset($_SESSION['id'])) {
            $userId = $_SESSION['id'];
        } else {
            $userId = null;
        }
        if ($type != "standard" && $type != "test"){
            header('Location: index.php?page=home');
            exit;
        }
        $quizInfo = $this->model->getQuizInfo($quizId);
        if ($userId !== null){
            $friendsLeaderboard = $this->model->getFriendsLeaderboard($quizId, $userId);
            $reactions = $this->model->getQuizReactions($quizId);
        }
        
        
        $hasLiked = false;
        $hasDisliked = false;
        if ($userId !== null) {
            $userReaction = $this->model->getUserReaction($userId, $quizId);
            $hasLiked = $userReaction['hasLiked'];
            $hasDisliked = $userReaction['hasDisliked'];
        }

       
        require ROOT . '/src/views/quiz/pageInterQuiz.php';
    }
}
?>
