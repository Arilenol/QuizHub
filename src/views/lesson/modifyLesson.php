<?php
$title = 'modification de leçon';
$style = './assets/style/modifylesson.css';
error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . '/../partials/header.php';
?>

<main class="modify-lesson-page">
    <form method="post" data-id="<?php echo htmlspecialchars($idLesson) ?>" action="index.php?page=lesson&categorie=modify&id=<?php echo htmlspecialchars($idLesson) ?>">
        <input type="hidden" name="idLesson" id="idLesson" value="<?php echo htmlspecialchars($idLesson) ?>">
        <button class="button" type="submit" name="Retour" value="yes"><span></span>
            <p>
                < Retour</p>
        </button>
        <h2>Résumé de la leçon
            <button class="modifResum" id="modifResum">Modifier</button>
        </h2>
        <p class="name">Nom de la leçon</p>
        <div class="input">
            <span></span>
            <input type="text" name="LessonTitle" id="LessonTitle" form="no" value="<?php echo htmlspecialchars($lessonInfos['title']) ?>" disabled>
        </div>
        <p class="description">Description</p>
        <div class="input">
            <span></span>
            <input type="text" name="LessonDescription" id="LessonDescription" form="no" value="<?php echo htmlspecialchars($lessonInfos['description']) ?>" disabled>
        </div>

        <h2>Catégories
            <button id="modifCategories" type="button">Modifier</button>
        </h2>
        <div class="categoriesList">
            <?php
            foreach ($ALL_CATEGORIES as $categorie) {
                if (in_array($categorie, $TAB_CATEGORIES)) {
                    $checked = 'checked';
                } else {
                    $checked = '';
                }
                echo '<label ' . (!empty($checked) ? '' : 'hidden') . '><input  class="category" name = "categories[]" type = "checkbox" value="' . htmlspecialchars($categorie['id']) . '" ' . $checked . ' ' . (!empty($checked) ? '' : 'hidden') . ' disabled>' . htmlspecialchars($categorie['categorieName']) . '</label>';
            }
            ?>

        </div>

        <div id="parts" style="display: flex; flex-direction: column; gap: 20px">
            <?php
            for ($i = 0; $i < $taille; $i = $i + 1) {
                echo '<div class="newPart" id = "LessonPart' . $i . '" value="' . $i . '">';
                echo '<div class="partContent question" style="grid-row-start: 1; grid-row-end: ' . $TAB_PART[$i]['nbExemple'] + 1 . ';">
                <p class="section-title">Partie ' . $i + 1 . '
                <button class="button modifPart" name = "modifPart" id="modifPart' . $i . '" type="submit" value=' . $i . '>Modifier</button>';
                if ($i != 0 || $taille > 1) {
                    echo '<button class="button delPartButton" name = "DelPart" id="DelPart' . $i . '" type="submit" value=' . $i . '>Supprimer</button>';
                }
                echo '</p>';
                echo '<p>Titre</p>
                <div class="textarea" id = "partTitle' . ($i + 1) . '" style="width: calc(100% - 40px); height: calc(100% - 90px)">
                    <span></span>
                    <textarea type = "text" name ="title' . $i . '" id="title' . $i . '" placeholder="titre de la partie" disabled>' . htmlspecialchars($TAB_PART[$i]['title']) . '</textarea>
                </div>
                <p>Contenu</p>
                <div class="textarea" id = "partContent' . ($i + 1) . '" style="width: calc(100% - 40px); height: calc(100% - 90px)">
                    <span></span>
                    <textarea type = "text" name ="partContent' . $i . '" id="content' . $i . '" placeholder="contenu de la partie" disabled>' . htmlspecialchars($TAB_PART[$i]['partContent']) . '</textarea>
                </div>
                </div>';


                for ($k = 0; $k < $TAB_PART[$i]['nbExemple']; $k = $k + 1) {
                    echo '<div class="reponse example" style="grid-row-start: ' . 1 + $k . '; grid-row-end: ' . 2 + $k . '; grid-column-start: 2; grid-column-end: 3;">
                <p class="section-title">Exemple ' . ($k + 1) . ' :</p>
                <div class="textarea" style="width: calc(100% - 40px);">
                    <span></span>
                    <textarea name="consigne' . $i . '-ex' . $k . '" id= "consigne' . $i . '-ex' . $k . '" disabled>' . htmlspecialchars($TAB_PART[$i]['exemples'][$k]['consigne']) . '</textarea>
                </div>
                <div class="textarea" style="width: calc(100% - 40px);">
                    <span></span>
                    <textarea name ="reponse' . $i . '-ex' . $k . '" id ="reponse' . $i . '-ex' . $k . '"disabled>' . htmlspecialchars($TAB_PART[$i]['exemples'][$k]['reponse']) . '</textarea>
                </div>
                <div class="exampleBtns">
                <button class="button modifierEx" type = "submit" id ="modifier' . $i . '-ex' . $k . '" name="modifierEx" value=' . $k . '>
                    <span></span>
                    <p>Modifier</p>
                </button>
                <button class="button supprimerEx" type = "submit" id ="supprimer' . $i . '-ex' . $k . '" name="supprimerEx" value=' . $k . '>
                    <span></span>
                    <p>Supprimer l\'exemple</p>
                </button>
                </div>
                </div>
                ';
                }
                echo '<button class="button addEx" type = "submit" name = "addEx" id="addEx' . $i . '" value ="' . $i . '" >
                    <span></span>
                    <p>Ajouter un Exemple</p>
                </button>';
                echo '</div>';
            }
            ?>
        </div>
        <button class="button" type="submit" name="addPart" id="addPart" value="yes">
            <span></span>
            <p>Ajouter une partie</p>
        </button>
        </div>
        <div class="associationQuiz">
            <p class="section-title">Quiz associé <button id="modifQuizAssoc">Modifier</button></p>
            <select name="quizUser" id="quizUser" disabled>
                <?php
                $sel = '';
                if (empty($lessonInfos['quiz_id'])) {
                    $sel = 'selected';
                }
                echo '<option value="Aucun" ' . $sel . '>Aucun</option>';
                $sel = '';
                foreach ($quizzes as $quiz) {
                    if ($lessonInfos['quiz_id'] === $quiz['id']) {
                        $sel = 'selected';
                    }
                    echo '<option value="' . htmlspecialchars($quiz['id']) . '" ' . $sel . '>' . htmlspecialchars($quiz['title']) . '</option>';
                    $sel = '';
                }
            ?>
        </select>
    </div>
    <div class="form-group disponibilite">
        <h2 class="section-title">Mode de publication <button id="modifDispo">Modifier</button></h2>
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
        <div class="ami-list" <?= $lessonInfos['disponibilite'] != "ami" ? 'hidden' : '' ?>>
        <?php
        if ($lessonInfos['disponibilite'] == "ami"){
            $hidden2 = '';
        } 
        else{
            $hidden2 = 'hidden';
        }
        if (empty($ALL_AMIS)) {
            echo '<p class="no-content" '.$hidden2.'>Vous n\'avez aucun ami.</p>';
        } else {
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
        }
        
        ?>
        </div>
        
        <?php
        if ($erreur) {
            echo '<p class="erreur">Chaque champ doit être rempli<br>Au moins une catégorie doit être sélectionnée</p>';
        }
        ?>
    </form>
</main>
<script src="./assets/js/popups.js"></script>
<script src="./assets/js/modifyLesson.js"></script>
<script src="./assets/js/sauvegardeScroll.js"></script>
<script src="./assets/js/selectDispo.js"></script>