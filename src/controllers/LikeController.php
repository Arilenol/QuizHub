<?php
require_once ROOT . '/src/models/LikeModel.php';
require_once ROOT . '/config/config.php';

class LikeController
{

    private LikeModel $model;

    public function __construct($db)
    {
        $db = getDbConnection();
        $this->model = new LikeModel($db);
    }


    public function getLikes() {}
}
