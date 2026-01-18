<?php
require_once ROOT . '/src/models/CRUDModel.php';
require_once ROOT . '/src/models/LessonModel.php';
require_once ROOT . '/config/config.php';

class CRUDLessonController {
    private $crudModel;
    private $lessonModel;

    public function index() {
        $db = getDbConnection();
        $this->crudModel = new CRUDModel($db);
        $this->lessonModel = new LessonModel($db);

        // Récupérer l'ID de la leçon depuis les paramètres GET
        $lesson_id = isset($_GET['id']) && !empty($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($lesson_id === 0) {
            die("ID leçon invalide");
        }

        // Récupérer les infos de la leçon
        $lesson = $this->lessonModel->getLesson($lesson_id);
        
        if (!$lesson) {
            die("Leçon non trouvée");
        }

        // Récupérer les parties de la leçon
        $parties = $this->lessonModel->getPart($lesson_id) ?: []; 
        
        // Récupérer les exemples pour chaque partie
        $resultats = [];
        foreach ($parties as $part) {
            $exemples = $this->lessonModel->getExemple($part['id']);
            $resultats[] = $exemples;
        }

        // Récupérer les catégories
        $lesson['categories'] = $this->crudModel->getCategoriesFromLesson($lesson_id);

        // Passer les données à la vue
        require ROOT . '/src/views/CRUD/CRUDlesson.php';
    }
}
?>
