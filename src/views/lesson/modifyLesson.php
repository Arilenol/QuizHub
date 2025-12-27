<?php
    $title = 'création de quiz';
    $style = './assets/style/modifylesson.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>



<form style="display: flex; flex-direction: column; padding: 25px; gap: 20px" method = "post" data-id="<?php echo htmlspecialchars($idLesson) ?>" action = "index.php?page=lesson&categorie=modify&id=<?php echo htmlspecialchars($idLesson) ?>">
    <input type="hidden" name="idLesson" id="idLesson" value="<?php echo htmlspecialchars($idLesson) ?>">
    <button class="button" type = "submit" name = "Retour" value = "yes"><span></span><p> < Retour</p></button>
    <h2>Résumé de la leçon
        <button class="modifResum" id ="modifResum" >Modifier</button>
    </h2>
    <p class="name">Nom de la leçon</p>
    <div class="input">
        <span></span>
        <input type="text" name ="QuizTitle" id="QuizTitle" value = "<?php echo htmlspecialchars($lessonInfos['title']) ?>" disabled>
    </div>
    <p class="description">Description</p>
    <div class="input">
        <span></span>
        <input type="text" name ="QuizDescription" id="QuizDescription" value = "<?php echo htmlspecialchars($lessonInfos['description']) ?>" disabled>
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
            echo '<div class="newPart" id = "LessonPart'.$i.'">';
            echo '<div class="question" style="grid-row-start: 1; grid-row-end: '. $TAB_PART[$i]['nbExemple'] + 1 .';">
                <p>Partie '. $i+1 .'</p>
                <p>Titre</p>
                <div class="textarea" id = "question'.($i+1).'" style="width: calc(100% - 40px); height: calc(100% - 90px)">
                    <span></span>
                    <textarea type = "text" name ="title'.$i.'" id="textarea'.$i.'" placeholder="titre de la partie" disabled>'.htmlspecialchars($TAB_PART[$i]['title']).'</textarea>
                </div>
                <p>Contenu</p>
                <div class="textarea" id = "question'.($i+1).'" style="width: calc(100% - 40px); height: calc(100% - 90px)">
                    <span></span>
                    <textarea type = "text" name ="partContent'.$i.'" id="textarea'.$i.'" placeholder="contenu de la partie" disabled>'.htmlspecialchars($TAB_PART[$i]['partContent']).'</textarea>
                </div>
                </div>';
            
            
            for ($k = 0; $k < $TAB_PART[$i]['nbExemple'];$k = $k +1){
                echo '<div class="reponse" style="grid-row-start: '. 1 + $k .'; grid-row-end: '. 2 + $k .'; grid-column-start: 2; grid-column-end: 3;">
                <p>Exemples '.($k+1).' :</p>
                <div class="input" style="width: calc(100% - 40px);">
                    <span></span>
                    <input name ="consigne'.$i.'[]" value = "'.htmlspecialchars($TAB_PART[$i]['exemples'][$k]['consigne']).'" disabled></input>
                </div>
                <div class="input" style="width: calc(100% - 40px);">
                    <span></span>
                    <input name ="reponse'.$i.'[]" value = "'.htmlspecialchars($TAB_PART[$i]['exemples'][$k]['reponse']).'" disabled></input>
                </div>
                </div>
                ';
            }
            echo '<div id="partFooter'.$i.'" class="partFooter" style="display: flex; flex-direction: row;gap:10px; grid-column-start: 2; grid-column-end: 3;">
                        <button class="button modifierPart" type = "submit" id ="modifier'.$i.'" name="modifierPart" value='.$i.'>
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

    <?php //-----------------------------------------------------ici---------------------------------------------------------?>
    <div class = "disponibilite">
        <p>Mode de publication <button id="modifDispo" >Modifier</button></p>
        <select name="disponibilite" id="disponibilite" disabled>
            <?php $dispo = '';
            $dispo = $lessonInfos['disponibilite'] == 'public' ? 'selected' : '';
             ?>
            <option value="public" <?= $dispo ?> >publique</option>
            <?php $dispo = $lessonInfos['disponibilite'] == 'ami' ? 'selected' : ''; ?>
            <option value="ami" <?= $dispo ?> >Seulement les amis</option>
            <?php $dispo = $lessonInfos['disponibilite'] == 'private' ? 'selected' : ''; ?>
            <option value="private" <?= $dispo ?> >seulement vous</option>
        </select>
        <?php
        //-----------------------------------------------------ici---------------------------------------------------------
        if ($lessonInfos['disponibilite'] == "ami"){
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
            echo '<p class="erreur">Chaque champ doit être rempli<br>Au moins une catégorie doit être sélectionnée</p>';
        } 
    ?>
</form>
<script src = "./assets/js/popups.js"></script>
<script src = "./assets/js/modifyLesson.js"></script>
<script src = "./assets/js/sauvegardeScroll.js"></script>
<script src = "./assets/js/selectDispo.js"></script>
