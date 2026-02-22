<?php
    $title = 'création de leçons';
    $style = './assets/style/createLesson.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>

<main class="create-lesson-page">
    <form method="post" action="index.php?page=lesson&categorie=create">
        <button type="submit" name="Retour" value="yes" class="button"><span></span><p>< Retour</p></button>
        <h1>Créer une leçon</h1>
        <input type="hidden" name="page" value="lesson">
        <input type="hidden" name="categorie" value="create">

        <div class="form-group">
            <h2>Nom de la leçon</h2>
            <div class="input">
                <span></span>
                <input type="text" name="LessonTitle" value="<?php echo htmlspecialchars($LessonTitle) ?>">
            </div>
        </div>

        <div class="form-group">
            <h2 class="description">Description</h2>
            <div class="input">
                <span></span>
                <input type="text" name="LessonDescription" value="<?php echo htmlspecialchars($desc) ?>">
            </div>
        </div>

        <div class="form-group">
            <h2>Catégories</h2>
            <div class="categoriesList">
                <?php
                foreach($TAB_CATEGORIE as $categorie){
                    if (in_array((string)$categorie['id'], $TAB_CATEGORIE_CHOISI)) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }
                    echo '<label><input name="categories[]" type="checkbox" value="'.htmlspecialchars($categorie['id']).'" '.$checked.'>'.htmlspecialchars($categorie['categorieName']).'</label>';
                }
                ?>
            </div>
        </div>

        <div class="newLesson">
            <h2>Parties</h2>
            <?php
            for($i = 0; $i < $_SESSION['nbParts'] ; $i = $i +1){
                echo '<div class="LessonPart">
                <h3>Partie '.($i+1).'</h3>
                <div class="input">
                    <span></span>
                    <input type="text" name="namePart'.$i.'" value="'.htmlspecialchars($TAB_CONTENU[$i]['name']).'" placeholder="nom de la partie">
                </div>
                <h3>Leçon :</h3>
                <div class="textarea">
                    <span></span>
                    <textarea name="contentPart'.$i.'">'.htmlspecialchars($TAB_CONTENU[$i]['content']).'</textarea>
                </div>';
                
                for ($k = 0; $k < $_SESSION['nbExemple'][$i];$k = $k +1){
                    echo '<div class="exemple">
                    <h3>Exemple '.($k+1).' :</h3>
                    <p>consigne : </p>
                    <div class="textarea">
                        <span></span>
                        <textarea name="exemple'.$k.'-part'.$i.'">'.htmlspecialchars($TAB_CONTENU[$i]['exemples'][$k]['consigne']).'</textarea>
                    </div>
                    
                    <p>réponse : </p>
                    <div class="textarea">
                        <span></span>
                        <textarea name="reponse'.$k.'-part'.$i.'">'.htmlspecialchars($TAB_CONTENU[$i]['exemples'][$k]['reponse']).'</textarea>
                    </div>
                    <button name="delExemple'.$k.'-part'.$i.'" value="yes" type="submit" class="button"><span></span><p>Supprimer cet exemple</p></button>
                    </div>';
                }
                echo '<button type="submit" name="addExemple" class="button" value='.$i.'><span></span><p>Ajouter un exemple</p></button>
                <button name="DelPart" type="submit" value='.$i.' class="button"><span></span><p>Supprimer cette partie</p></button>
                </div>';
            }
            echo '<button type="submit" name="addPart" value="yes" class="button"><span></span><p>Ajouter une nouvelle partie</p></button>'
            ?>
        </div>

        <div class="form-group">
            <p>Quiz associé :</p>
            <select name="linkedQuiz">
                <option value="Aucun">Aucun</option>
                <?php
                foreach($quizzes as $quiz){
                    if ($quizSelected == $quiz['id']) {
                        echo '<option value="'.htmlspecialchars($quiz['id']).'" selected>'.htmlspecialchars($quiz['title']).'</option>';
                    } else {
                        echo '<option value="'.htmlspecialchars($quiz['id']).'">'.htmlspecialchars($quiz['title']).'</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="disponibilite form-group">
            <p>Mode de publication :</p>
            <select name="disponibilite" id="disponibilite">
                <?php $dispo = '';
                $dispo = $_SESSION['POST']['disponibilite'] == 'public' ? 'selected' : '';
                 ?>
                <option value="public" <?= $dispo ?> >publique</option>
                <?php $dispo = $_SESSION['POST']['disponibilite'] == 'ami' ? 'selected' : ''; ?>
                <option value="ami" <?= $dispo ?> >Seulement les amis</option>
                <?php $dispo = $_SESSION['POST']['disponibilite'] == 'private' ? 'selected' : ''; ?>
                <option value="private" <?= $dispo ?> >seulement vous</option>
            </select>
            <div class="ami-list">
                <?php
                if ($_SESSION['POST']['disponibilite'] == "ami"){
                    $hidden2 = '';
                } 
                else{
                    $hidden2 = 'hidden';
                }
                if (in_array('tous', $TAB_AMI_CHOISI)) {
                    $checkedTous = 'checked';
                } else {
                    $checkedTous = '';
                }
                echo '<label class="'.$hidden2.'"><input name="amiDispo[]" type="checkbox" value="tous" '.$checkedTous.'>Tous les amis</label>';
                foreach($TAB_AMI as $ami){
                    if (in_array($ami['ami_id'], $TAB_AMI_CHOISI)) {
                        $checked = 'checked';
                    } else {
                        $checked = '';
                    }
                    echo '<label class="'.$hidden2.'"><input name="amiDispo[]" type="checkbox" value="'.$ami['ami_id'].'" '.$checked.'>'.$ami['username'].'</label>';
                }
                ?>
            </div>
        </div>
        
        <?php if ($_SESSION['erreur']): ?>
            <p class="erreur">Chaque champ doit être rempli<br>Chaque partie peut avoir autant d'exemple que nécessaire<br>Au moins une catégorie doit être sélectionnée</p>
        <?php endif; ?>

        <button type="submit" name="create" id="create" value="yes" class="button"><span></span><p>Créer la leçon</p></button>
    </form>
</main>
<script src="./assets/js/popups.js"></script>
<script src="./assets/js/createLesson.js"></script>
<script src="./assets/js/sauvegardeScroll.js"></script>
<script src="./assets/js/selectDispo.js"></script>
