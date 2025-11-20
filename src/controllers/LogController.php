<?php
require_once ROOT . '/src/models/LessonModel.php';
require_once ROOT . '/config/config.php';
class LogController{

    public function showRegister(){
        require ROOT . '/src/views/log/register.php';
    }

    public function showConnection() {
        require ROOT . '/src/views/log/connection.php';
    }

}
?>