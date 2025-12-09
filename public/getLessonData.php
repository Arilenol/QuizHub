<?php
    header('Content-Type: application/json; charset=utf-8');
    session_start();
    define('ROOT', dirname(__DIR__));
    require_once ROOT . '/src/models/LessonModel.php';
    require_once ROOT . '/src/models/HomeModel.php';
    require_once ROOT . '/config/config.php';
    
    $id = $_GET["id"];
    $db = getDbConnection();
    $model = new LessonModel($db);
    $modelHome = new HomeModel($db);
    if(isset($id) && !empty($id)){
        $lesson = $model->getLesson($id);
        $parts = $model->getPart($id);
        $examples = $model->getExemple($id);
        if(!empty($lesson) && !empty($parts)){
            $jsonFinal = [
                "id"          => $lesson["id"],
                "title"       => $lesson['title'],
                "description" => $lesson['description'],
                "parties"     => []
            ];

            foreach ($parts as $index => $partie) {
                
                $numeroActuel = $index + 1;
                $exempleTrouve = null;
                foreach ($examples as $ex) {
                    if ($ex['numeroExemple'] == $numeroActuel) {
                        $exempleTrouve = $ex;
                        break;
                    }
                }

                $structurePartie = [
                    "id"  => $partie["id"],
                    "title"   => $partie['title'],
                    "content" => $partie['content'],
                    "example" => null
                ];

                if ($exempleTrouve) {
                    $structurePartie['example'] = [
                        "instruction" => $exempleTrouve['consigne'],
                        "response"    => $exempleTrouve['reponse']
                    ];
                }
                $jsonFinal['parties'][] = $structurePartie;
            }

            echo json_encode($jsonFinal, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }
        else{
            echo json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        
    }
    else{
        echo json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
?>