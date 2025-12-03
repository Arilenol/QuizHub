<?php
    $title = 'création de quiz';
    $style = './assets/style/createQuiz.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>

<script src="./assets/js/script.js"></script>

<form style="display: flex; flex-direction: column; padding: 25px; gap: 20px" method = "post" action = "index.php?page=standard&categorie=create">
    <button class="button" type = "submit" name = "Retour" value = "yes"><span></span><p> < Retour</p></button>
    <h1>Créer un Quiz</h1>
    <input name="page" value="standard" hidden>
    <input name ="categorie" value = "create" hidden>
    <h2>Nom du quiz</h2>
    <div class="input">
        <span></span>
        <input type="text" name ="QuizTitle" value = "<?php echo htmlspecialchars($quizTitle) ?>">
    </div>
    <p class="description">Description</p>
    <div class="input">
        <span></span>
        <input type="text" name ="QuizDescription" value = "<?php echo htmlspecialchars($desc) ?>">
    </div>
    <div style="display: flex; flex-direction: column; gap: 20px">
        <?php
        for($i = 0; $i < $_SESSION['nbQuestions'] ; $i = $i +1){
            echo '<div class="newQuiz">';
            echo '<p class="validite">réponse valide ?</p>';
            echo '<div class="question" style="grid-row-start: 1; grid-row-end: '. $_SESSION['nbReponse'][$i] + 1 .';">
                <p>Question '. $i+1 .'</p>
                <div class="textarea" style="width: calc(100% - 40px); height: calc(100% - 90px)">
                    <span></span>
                    <textarea type = "text" name ="question'.$i.'" placeholder="nom de la question">'.$TAB_CONTENU[$i]['name'].'</textarea>
                </div>
                </div>';
            
            
            for ($k = 0; $k < $_SESSION['nbReponse'][$i];$k = $k +1){
                echo '<div class="reponse" style="grid-row-start: '. 1 + $k .'; grid-row-end: '. 2 + $k .'; grid-column-start: 2; grid-column-end: 3;">
                <p>Réponse '.($k+1).' :</p>
                <div class="input" style="width: calc(100% - 40px);">
                    <span></span>
                    <input name ="reponse'.$k.'-question'.$i.'" value = "'.$TAB_CONTENU[$i]['reponses'][$k]['texte'].'"></input>
                </div>
                </div>
                <div class="checkbox" style="grid-row-start: '. 1 + $k .'; grid-row-end: '. 2 + $k .'; grid-column-start: 3; grid-column-end: 4;align-self: end;">
                    <input type="checkbox" name="checkbox'.$k.'-question'.$i.'" '.($TAB_CONTENU[$i]['reponses'][$k]['valide'] ? 'checked':'').' hidden>
                </div>
                ';
                //----------------------------------------faudra demander à kilian---------------------------------------

            }
            echo '<div style="display: flex; flex-direction: row;gap:10px; grid-column-start: 2; grid-column-end: 3;">
                        <button class="button" type = "submit" name="addReponse" value='.$i.'>
                            <span></span>
                            <p>Ajouter une réponse</p>
                        </button>
                    <button class="button" name= "delReponse'.$i.'" value="yes" type="submit"><span></span> <p>Supprimer une réponse</p></button>
                </div>';
            if ($i != 0){
                echo '<button class="button" name = "DelQuestion" type="submit" value='.$i.'><span></span><p>Supprimer cette question</p></button>';
            }
            echo '</div>';
        }
        ?>
        </div>
        <button class="button" type = "submit" name = "addQuestion" value = "yes">
            <span></span>
            <p>Ajouter une question</p>
        </button>
    </div>
    
    
    
    <h2>Paramètres</h2>
    <div class = "parametres">
        <?php
            foreach ($tabParametres as $indice => $param){
                //----------------------------------------faudra demander à kilian---------------------------------------
                echo 
                '
                <div style="display: flex; flex-direction: row; align-items: center; gap: 10px">
                    <p style="font-size: 20px">'.$param['name'].' : </p>
                    <div class="checkbox">
                        <input type="checkbox" name="param'.$indice.'" hidden>
                    </div>
                </div>';
            }

        ?>
    </div>

    <button class="button" type = "submit" name = "create" value = "yes"><span></span><p>Créer le quiz</p></button>
</form>