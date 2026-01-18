<?php
    $title = 'création de flashcards';
    $style = './assets/style/createFlashcards.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>

<form method = "post" action = "index.php?page=flashcard&categorie=create" style="display: flex; flex-direction: column; padding: 25px; gap: 15px">
    <button class="button" type = "submit" name = "Retour" value = "yes"><span></span><p> < Retour</p></button>
    <h1>Créer une Flashcard</h1>

        <input type = "hidden" name="page" value="flashcard">
    <input type="hidden" name ="categorie" value = "create">
    <h2>Nom des flashcards</h2>
    <div class="input">
        <span></span>
        <input type="text" name ="FlashcardTitle" value = "<?php echo htmlspecialchars($CardsTitle) ?>">
    </div>
    <p class="description">Description</p>
    <div class="input">
        <span></span>
        <input type="text" name ="FlashcardDescription" value = "<?php echo htmlspecialchars($desc) ?>">
    </div>

    <h2 style="display : inline;">Catégories
        <button id="hiddenCategories" type = "button ">▼</button>
    </h2>
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

    <div class = "newflashcard" style="display: flex; flex-direction: column; gap: 15px">
    <p class = "cartes">Cartes</p>
    <?php
    for($i = 0; $i < $_SESSION['nbCartes'] ; $i = $i +1){
        echo '<div class="Carte" style="display: flex; flex-direction: column; gap: 15px">
        <p >Carte '.($i+1).'</p>
        <p>Question :</p>
        <div class="textarea">
            <span></span>
            <textarea name ="cardQuestion'.$i.'"> '.htmlspecialchars($TAB_CONTENU[$i]['question']).'</textarea>
        </div>
        <p>Réponse :</p>
        <div class="textarea">
            <span></span>
            <textarea name = "cardReponse'.$i.'">'.htmlspecialchars($TAB_CONTENU[$i]['reponse']).'</textarea>
        </div>
        
        <button class="button" name = "DelCard" type="submit" value='.$i.'><span></span><p>Supprimer cette question</p></button>
        </div>';
    }
    echo '<button class="button" type = "submit" name = "addCard" value = "yes"><span></span><p>Ajouter une question</p></button>'
    ?>
    </div>
    <?php //-----------------------------------------------------ici---------------------------------------------------------?>
    <div class = "disponibilite">
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
        <?php
        //-----------------------------------------------------ici---------------------------------------------------------
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
        echo '<label '.$hidden2.'><input name = "amiDispo[]" type = "checkbox" value="tous" '.$checkedTous.'>Tous les amis</label>';
        foreach($TAB_AMI as $ami){
            if (in_array($ami['ami_id'], $TAB_AMI_CHOISI)) {
                $checked = 'checked';
            } else {
                $checked = '';
            }
            echo '<label '.$hidden2.'><input name = "amiDispo[]" type = "checkbox" value="'.htmlspecialchars($ami['ami_id']).'" '.$checked.'>'.htmlspecialchars($ami['username']).'</label>';
        }
        
        ?>
    </div>
    <?php
    //-----------------------------------------------------ici---------------------------------------------------------
        if ($_SESSION['erreur']){
            echo '<p class="erreur">Chaque champ doit être rempli<br>Au moins une catégorie doit être sélectionnée</p>';
        } 
    ?>
    <button class="button" type = "submit" id = "create" name = "create" value = "yes"><span></span><p>Créer les flashcards</p></button>
</form>
<script src="./assets/js/popups.js"></script>
<script src = "./assets/js/createFlashcard.js"></script>
<script src = "./assets/js/sauvegardeScroll.js"></script>
<script src = "./assets/js/selectDispo.js"></script>
<script src = "./assets/js/createContent.js"></script>