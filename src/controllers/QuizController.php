<?php
require_once ROOT . '/src/models/QuizModel.php';
require_once ROOT . '/config/config.php';

class QuizController {
    public function createQuiz(){
        // show all errors while developing this flow
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $db = getDbConnection();
        $model = new QuizModel($db);

        session_start();


        //------------------------à changer-------------------------
        $_SESSION['id'] = 1;
        //------------------------à changer-------------------------


        if (isset($_SESSION['id'])){
            $id = $_SESSION['id'];
        }
        else{
            header('Location: index.php?page=home');
        }

        $title = isset($_POST['QuizTitle']) ? $_POST['QuizTitle'] : '';
        $desc = isset($_POST['QuizDescription']) ? $_POST['QuizDescription'] : '';
        
        if (!isset($_SESSION['nbQuestions']) || empty($_SESSION['nbQuestions'])){
            $_SESSION['nbQuestions'] = 1;
        }
        if (!isset($_SESSION['nbReponse']) || empty($_SESSION['nbReponse'])){
            // array where each index is number of answers for that question
            $_SESSION['nbReponse'] = array(0 => 2);
        }
        var_dump($_SESSION);
        var_dump($_POST);
        // Handle form actions: Retour, addQuestion, addReponse, DelQuestion, delReponseX
        if (isset($_POST['Retour']) && $_POST['Retour'] === "yes"){
            unset($_SESSION['nbReponse']);
            unset($_SESSION['nbQuestions']);
            // redirect back to content creation or other appropriate page
            header('Location: index.php?page=createContent');
            exit;
        }

        if (isset($_POST['addQuestion']) && $_POST['addQuestion'] === 'yes'){
            $_SESSION['nbQuestions']++;
            // new question starts with minimum allowed responses
            $_SESSION['nbReponse'][$_SESSION['nbQuestions']-1] = 2;
        }

        if (isset($_POST['addReponse']) && $_POST['addReponse'] !== ''){
            $qIdx = (int)$_POST['addReponse'];
            if (!isset($_SESSION['nbReponse'][$qIdx])){
                $_SESSION['nbReponse'][$qIdx] = 2;
            }
            // cap responses at 6
            if ($_SESSION['nbReponse'][$qIdx] < 6) {
                $_SESSION['nbReponse'][$qIdx]++;
            }
        }

        // Delete a question (shift subsequent questions up)
        if (isset($_POST['DelQuestion']) && $_POST['DelQuestion'] !== '') {
            if ($_SESSION['nbQuestions'] > 1){
                $idx = (int)$_POST['DelQuestion'];
                $oldNb = $_SESSION['nbQuestions'];
                for ($i = $idx; $i < $oldNb - 1; $i++) {
                    $_SESSION['nbReponse'][$i] = $_SESSION['nbReponse'][$i + 1];
                    // shift posted fields so the view re-populates correctly
                    if (isset($_POST['question'.($i+1)])){
                        $_POST['question'.$i] = $_POST['question'.($i+1)];
                    }
                    for ($k = 0; $k < ($_SESSION['nbReponse'][$i] ?? 0); $k++){
                        if (isset($_POST['reponse'.$k.'-question'.($i+1)])){
                            $_POST['reponse'.$k.'-question'.$i] = $_POST['reponse'.$k.'-question'.($i+1)];
                        }
                        if (isset($_POST['reponse'.$k.'-question'.($i+1)])){
                            $_POST['reponse'.$k.'-question'.$i] = $_POST['reponse'.$k.'-question'.($i+1)];
                        }
                    }
                }
                $last = $oldNb - 1;
                unset($_SESSION['nbReponse'][$last]);
                for ($k = 0; $k < 20; $k++){
                    unset($_POST['reponse'.$k.'-question'.$last]);
                    unset($_POST['checkbox'.$k.'-question'.$last]);
                }
                unset($_POST['question'.$last]);
                $_SESSION['nbQuestions']--;
            }
            
        }

        // Delete a response for a specific question: view posts a button named like 'delReponse{qIndex}'
        for ($i = 0; $i < $_SESSION['nbQuestions']; $i++){
            if (isset($_POST['delReponse'.$i]) && $_POST['delReponse'.$i] === 'yes'){
                if ($_SESSION['nbReponse'][$i] > 2){
                    $idxToRemove = $_SESSION['nbReponse'][$i] - 1; // remove last by default
                    for ($e = $idxToRemove; $e < $_SESSION['nbReponse'][$i] - 1; $e++){
                        $_POST['reponse'.$e.'-question'.$i] = $_POST['reponse'.($e+1).'-question'.$i] ?? '';
                        $_POST['reponse'.$e.'-question'.$i] = $_POST['reponse'.($e+1).'-question'.$i] ?? '';
                    }
                    unset($_POST['reponse'.($_SESSION['nbReponse'][$i]-1).'-question'.$i]);
                    unset($_POST['checkbox'.($_SESSION['nbReponse'][$i]-1).'-question'.$i]);
                    $_SESSION['nbReponse'][$i]--;
                }
            }
        }

        // Build TAB_CONTENU structure from POST so view can re-populate fields
        $TAB_CONTENU = array();
        for ($i = 0; $i < $_SESSION['nbQuestions']; $i++) {
            $question = array(
                'name' => isset($_POST['question'.$i]) ? $_POST['question'.$i] : '',
                'reponses' => array()
            );
            $nbEx = isset($_SESSION['nbReponse'][$i]) ? intval($_SESSION['nbReponse'][$i]) : 0;

            
            if ($nbEx < 2) $nbEx = 2;
            if ($nbEx > 6) $nbEx = 6;
            for ($k = 0; $k < $nbEx; $k++) {
                
                $reponse = isset($_POST['reponse'.$k.'-question'.$i])
                    ? $_POST['reponse'.$k.'-question'.$i]
                    : '';
                $isValid = isset($_POST['checkbox'.$k.'-question'.$i]) ? 1 : 0;

                $question['reponses'][$k] = array(
                    'texte' => $reponse,
                    'valide' => $isValid
                );
            }

            $TAB_CONTENU[$i] = $question;
        }

        // Placeholder for parameters presented as checkboxes in the view
        //------------informations à remplir---------------------
        $tabParametres = array();
        //------------informations à remplir---------------------
        // Handle final creation: validate and call model methods to persist
        if (isset($_POST['create']) && $_POST['create'] === 'yes'){
            // basic validation: ensure a title is provided
            if (isset($_POST['QuizTitle']) && !empty($_POST['QuizTitle'])){
                $title = $_POST['QuizTitle'];
                $desc = isset($_POST['QuizDescription']) ? $_POST['QuizDescription'] : '';

                // --- MODEL INTERACTION POINTS ---
                // 1) Create the quiz record and obtain its ID
                //    Example (to implement in QuizModel): $quizId = $model->createQuiz($authorId, $title, $desc, ...);

                // 2) Insert each question for the quiz
                //    Example: foreach ($TAB_CONTENU as $qIndex => $q) { $model->createQuestion($quizId, $qIndex, $q['name']); }

                // 3) Insert each answer/card for each question
                //    Example: foreach ($q['reponses'] as $aIndex => $ans) { $model->createAnswer($quizId, $qIndex, $aIndex, $ans['consigne'], $ans['reponse']); }

                // 4) Insert any quiz-category relationships if applicable
                //    Example: if categories are posted, call $model->attachCategory($quizId, $categoryId)

                // Note: I intentionally do NOT call these model methods here because your QuizModel currently
                //       only contains flashcard helpers. Please implement the persistence methods in
                //       `src/models/QuizModel.php` (createQuiz, createQuestion, createAnswer, attachCategory, etc.)
                //       Then uncomment and adapt the calls above to persist the submitted quiz.

                // after successful creation, clear session and redirect
                unset($_SESSION['nbReponse']);
                unset($_SESSION['nbQuestions']);
                header('Location: index.php?page=home');
                exit;
            }
        }

        // Provide any auxiliary data the view might need (e.g., existing quizzes by author)
        // $quizzes = $model->getQuizByAuthor($authorId); // implement in QuizModel if useful

        require ROOT . '/src/views/Quiz/createQuiz.php';
    }

}
?>