<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
define('ROOT', dirname(__DIR__));
require_once ROOT . '/src/models/FlashcardModel.php';
require_once ROOT . '/src/models/HomeModel.php';
require_once ROOT . '/config/config.php';

$id = $_GET["id"];
$db = getDbConnection();
$model = new FlashcardModel($db);
$modelHome = new HomeModel($db);
if (isset($id) && !empty($id)) {
    $flashcardData = array_column($modelHome->getAllInfo(), null, 'id')[$id];
    $listQuestion = $model->getFlashcardById($id);

    if (isset($flashcardData) && $flashcardData["genre"] == "flashcard") {
        $export = [
            "id"          => $flashcardData["id"],
            "name"        => $flashcardData['title'],
            "description" => $flashcardData['description'],
            "type"        => $flashcardData['genre'],
            "categories"  => $flashcardData['categories'],
            "creator"     => $flashcardData['user_name'],
            "data"        => []
        ];

        for ($i = 0; $i < sizeof($listQuestion); $i++) {
            $carte = $model->getInfoFlashcardById($listQuestion[$i]);

            $export['data'][$i] = [
                "question" => $carte['question'],
                "answer"   => $carte['reponse']
            ];
        }

        echo json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
