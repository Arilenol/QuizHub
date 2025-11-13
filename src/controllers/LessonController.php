<?php
require_once ROOT . '/src/models/LessonModel.php';
require_once ROOT . '/config/config.php';

class LessonController {
    public function index($id) {
        $db = getDbConnection();
        $model = new LessonModel($db);
        // récupère les données de la leçon
        $lesson = $model->getLesson($id);
        if (!$lesson) {
            // Leçon non trouvée
            http_response_code(404);
            echo "Leçon non trouvée";
            return;
        }
        // récupère les parties
        $parties = $model->getPart($id) ?: []; 
        // récupère les exemples pour chaque partie
        $resultats = [];
        foreach ($parties as $part) {
            $exemples = $model->getExemple($part['id']);
            $resultats[] = $exemples;
        }
        // afficher la vue
        require ROOT . '/src/views/lesson/show.php';
    }

    //A DEVELOPPER
    public function createLesson(){
        // afficher la vue
        require ROOT . '/src/views/lesson/createLesson.php';
    }

}

?>