<?php
require_once ROOT . '/src/models/SignalementModel.php';
require_once ROOT . '/config/config.php';

class SignalementController
{

    private SignalementModel $model;

    public function __construct()
    {
        $db = getDbConnection();
        $this->model = new SignalementModel($db);
    }

    public function index(){
        require_once ROOT. '/src/views/quiz/signalement.php';
    }
}

?>