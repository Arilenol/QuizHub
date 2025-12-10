<?php
    $title = 'création de quiz';
    $style = './assets/style/createQuiz.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>



<form style="display: flex; flex-direction: column; padding: 25px; gap: 20px" method = "post" data-id="<?php echo htmlspecialchars($idQuiz) ?>" action = "index.php?page=standard&categorie=modify&id=<?php echo htmlspecialchars($idQuiz) ?>">
    <input type="hidden" name="idQuiz" id="idQuiz" value="<?php echo htmlspecialchars($idQuiz) ?>">
    <button class="button" type = "submit" name = "Retour" value = "yes"><span></span><p> < Retour</p></button>
    <h2>Résumé du quiz
        <button class="modifResum" id ="modifResum" >Modifier</button>
    </h2>
    <p class="name">Nom du quiz</p>
    <div class="input">
        <span></span>
        <input type="text" name ="QuizTitle" id="QuizTitle" value = "<?php echo htmlspecialchars($quizInfos['title']) ?>" disabled>
    </div>
    <p class="description">Description</p>
    <div class="input">
        <span></span>
        <input type="text" name ="QuizDescription" id="QuizDescription" value = "<?php echo htmlspecialchars($quizInfos['description']) ?>" disabled>
    </div>

    <h2 style="display : inline;">Catégories
        <button id="modifCategories" type = "button ">Modifier</button>
    </h2>
    <div class="categoriesList">
        <?php
        foreach($ALL_CATEGORIES as $categorie){
            if (in_array($categorie, $TAB_CATEGORIES)) {
                $checked = 'checked';
            } else {
                $checked = '';
            }
            echo '<label '.(!empty($checked) ? '' : 'hidden').'><input  class="category" name = "categories[]" type = "checkbox" value="'.htmlspecialchars($categorie['id']).'" '.$checked.' '.(!empty($checked) ? '' : 'hidden').' disabled>'.htmlspecialchars($categorie['categorieName']).'</label>';
        }
        ?>

    </div>

    <div style="display: flex; flex-direction: column; gap: 20px">
        <?php
        for($i = 0; $i < $taille ; $i = $i +1){
            echo '<div class="newQuiz" id = "quizQuestion'.$i.'">';
            echo '<p class="validite">réponse valide ?</p>';
            echo '<div class="question" style="grid-row-start: 1; grid-row-end: '. $TAB_QUESTIONS[$i]['nbReponse'] + 1 .';">
                <p>Question '. $i+1 .'</p>
                <div class="textarea" id = "question'.($i+1).'" style="width: calc(100% - 40px); height: calc(100% - 90px)">
                    <span></span>
                    <textarea type = "text" name ="question'.$i.'" id="textarea'.$i.'" placeholder="nom de la question" disabled>'.htmlspecialchars($TAB_QUESTIONS[$i]['question']).'</textarea>
                </div>
                </div>';
            
            
            for ($k = 0; $k < $TAB_QUESTIONS[$i]['nbReponse'];$k = $k +1){
                echo '<div class="reponse" style="grid-row-start: '. 1 + $k .'; grid-row-end: '. 2 + $k .'; grid-column-start: 2; grid-column-end: 3;">
                <p>Réponse '.($k+1).' :</p>
                <div class="input" style="width: calc(100% - 40px);">
                    <span></span>
                    <input name ="reponse'.$i.'[]" value = "'.htmlspecialchars($TAB_QUESTIONS[$i]['reponses'][$k]['reponse']).'" disabled></input>
                </div>
                </div>
                <div class="checkbox" style="grid-row-start: '. 1 + $k .'; grid-row-end: '. 2 + $k .'; grid-column-start: 3; grid-column-end: 4;align-self: end;">
                    <input type="checkbox" name="checkbox'.$i.'[]" '.($TAB_QUESTIONS[$i]['reponses'][$k]['estCorrecte'] ? 'checked':'').' disabled hidden>
                </div>
                ';
            }
            echo '<div id="questionFooter'.$i.'" class="questionFooter" style="display: flex; flex-direction: row;gap:10px; grid-column-start: 2; grid-column-end: 3;">
                        <button class="button modifierQuestion" type = "submit" id ="modifier'.$i.'" name="modifierQuestion" value='.$i.'>
                            <span></span>
                            <p>Modifier</p>
                        </button>
                </div>';
            if ($i != 0 || $taille > 1){
                echo '<button class="button delQuestionButton" name = "DelQuestion" id="DelQuestion'.$i.'" type="submit" value='.$i.'><span></span><p>Supprimer cette question</p></button>';
            }
            echo '</div>';
        }
        ?>
        </div>
        <button class="button" type = "submit" name = "addQuestion" id="addQuestion" value = "yes">
            <span></span>
            <p>Ajouter une question</p>
        </button>
    </div>
    
    <h2 ><?= $quizInfos['genre'] == 'test' ? 'test ': 'Quiz standard' ?>
        <button id = "modifTest">Changer</button>
        <div class="checkbox"  id = "Test">
            <?php
            if($quizInfos['genre'] == 'test'){
                $check = 'checked';
            }
            else{
                $check = '';
            }

            ?>
            <input type="checkbox" id = "genreTest" name="genreTest" <?= $check ?> disabled hidden/>
        </div>
    </h2>
    <h2>Paramètres
        <button class="modifParam" id ="modifParam" >Modifier</button>
    </h2>
    <div class = "parametres">
        <?php
            foreach ($tabParametres as $indice => $param){
                
                echo 
                '
                <div style="display: flex; flex-direction: row; align-items: center; gap: 10px">
                    <p style="font-size: 20px">'.$param['desc'].' : </p>
                    <div class="checkbox param"  id = "'.$param['name'].'" value="'.$indice.'">
                        <input type="checkbox" id = "param'.$param['name'].'" name="param'.$param['name'].'"  '.($TAB_PARAMS[$indice]!= 0 ? 'checked' : '').' disabled hidden/>
                    </div>';
                    if ($param['name'] == 'timer'){
                        if (!empty($TAB_PARAMS[0]) && $TAB_PARAMS[0] != 0){
                            $hidden = '';
                        }
                        else{
                            $hidden = 'hidden';
                        }
                        echo '<p class = "timerP" id="timerP" '. $hidden .'>Temps en minutes entre 0 et 120<br>(0 ne sera pas compté) :</p>';
                        echo '<input type="number" name="timerValue" id="timerV" value="'.htmlspecialchars($TAB_PARAMS[$indice]).'" min="0" max="120" '. $hidden . ' disabled/>';
                    }
                echo '</div>';
            }

        ?>
    </div>
    <?php //-----------------------------------------------------ici---------------------------------------------------------?>
    <div class = "disponibilite">
        <p>Mode de publication <button id="modifDispo" >Modifier</button></p>
        <select name="disponibilite" id="disponibilite" disabled>
            <?php $dispo = '';
            $dispo = $quizInfos['disponibilite'] == 'public' ? 'selected' : '';
             ?>
            <option value="public" <?= $dispo ?> >publique</option>
            <?php $dispo = $quizInfos['disponibilite'] == 'ami' ? 'selected' : ''; ?>
            <option value="ami" <?= $dispo ?> >Seulement les amis</option>
            <?php $dispo = $quizInfos['disponibilite'] == 'private' ? 'selected' : ''; ?>
            <option value="private" <?= $dispo ?> >seulement vous</option>
        </select>
        <?php
        //-----------------------------------------------------ici---------------------------------------------------------
        if ($quizInfos['disponibilite'] == "ami"){
            $hidden2 = '';
        } 
        else{
            $hidden2 = 'hidden';
        }
        if (in_array('tous', $TAB_AMIS)) {
            $checkedTous = 'checked';
        } else {
            $checkedTous = '';
        }
        echo '<label class="friends" '.$hidden2.'><input name = "amiDispo[]" type = "checkbox" value="tous" '.$checkedTous.' disabled>Tous les amis</label>';
        foreach($ALL_AMIS as $ami){
            if (in_array($ami['ami_id'], $TAB_AMIS)) {
                $checked = 'checked';
            } else {
                $checked = '';
            }
            echo '<label class="friends" '.$hidden2.'><input name = "amiDispo[]" type = "checkbox" value="'.htmlspecialchars($ami['ami_id']).'" '.$checked.' disabled>'.htmlspecialchars($ami['username']).'</label>';
        }
        
        ?>
    </div>
    <?php
    //-----------------------------------------------------ici---------------------------------------------------------
        if ($erreur){
            echo '<p class="erreur">Chaque champ doit être rempli<br>Chaque question doit avoir au moins une réponse juste et une réponse fausse<br>Au moins une catégorie doit être sélectionnée</p>';
        } 
    ?>
</form>
<script src="./assets/js/script.js"></script>
<script src = "./assets/js/modifyQuiz.js"></script>
<script src = "./assets/js/sauvegardeScroll.js"></script>
<script src = "./assets/js/selectDispo.js"></script>
