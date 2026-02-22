<?php
    $title = 'modification de flashcard';
    $style = './assets/style/modifyFlashcard.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>



<div class="modify-flashcard-page">
    <form method="post" data-id="<?php echo htmlspecialchars($idFlashcard) ?>" action="index.php?page=flashcard&categorie=modify&id=<?php echo htmlspecialchars($idFlashcard) ?>">
        <input type="hidden" name="idFlashcard" id="idFlashcard" value="<?php echo htmlspecialchars($idFlashcard) ?>">
        <button class="button" type="submit" name="Retour" value="yes"><span></span><p> < Retour</p></button>
        <h2>Résumé des flashcards
            <button class="modifResum" id ="modifResum">Modifier</button>
        </h2>
        <p class="name">Nom des flashcards</p>
        <div class="input">
            <span></span>
            <input type="text" name ="FlashcardTitle" id="FlashcardTitle" form="no" value="<?php echo htmlspecialchars($flashcardInfos['title']) ?>" disabled>
        </div>
        <p class="description">Description</p>
        <div class="input">
            <span></span>
            <input type="text" name ="FlashcardDescription" id="FlashcardDescription" form="no" value="<?php echo htmlspecialchars($flashcardInfos['description']) ?>" disabled>
        </div>

        <h2>Catégories
            <button id="modifCategories" type="button">Modifier</button>
        </h2>
        <div class="categoriesList">
            <?php
            foreach($ALL_CATEGORIES as $categorie){
                if (in_array($categorie, $TAB_CATEGORIES)) {
                    $checked = 'checked';
                } else {
                    $checked = '';
                }
                echo '<label '.(!empty($checked) ? '' : 'hidden').'><input class="category" name="categories[]" type="checkbox" value="'.htmlspecialchars($categorie['id']).'" '.$checked.' '.(!empty($checked) ? '' : 'hidden').' disabled>'.htmlspecialchars($categorie['categorieName']).'</label>';
            }
            ?>
        </div>

        <div id="cards">
            <?php
            for($i = 0; $i < $taille ; $i = $i +1){
                echo '<div class="newCard" id="Card'.$i.'" value="'.$i.'">';
                echo '<div class="partContent question">
                    <p class="section-title">Carte '.($i + 1).'
                    <button class="button modifCard" name="modifCard" id="modifCard'.$i.'" type="submit" value='.$i.'>Modifier</button>';
                    if ($i != 0 || $taille > 1){
                        echo '<button class="button delCardButton" name="DelCard" id="DelCard'.$i.'" type="submit" value='.$i.'>Supprimer</button>';
                    }
                echo '</p>';
                echo '<p>Question</p>
                    <div class="textarea" id="cardQuestion'.($i+1).'">
                        <span></span>
                        <textarea type="text" name="cardQuestion'.$i.'" id="question'.$i.'" placeholder="question de la carte" disabled>'.htmlspecialchars($TAB_CARD[$i]['question']).'</textarea>
                    </div>
                    <p>Réponse</p>
                    <div class="textarea" id="cardResponse'.($i+1).'">
                        <span></span>
                        <textarea type="text" name="cardResponse'.$i.'" id="response'.$i.'" placeholder="réponse à la question" disabled>'.htmlspecialchars($TAB_CARD[$i]['reponse']).'</textarea>
                    </div>
                    </div>';
                echo '</div>';
            }
            ?>
        </div>
        <button class="button" type="submit" name="addCard" id="addCard" value="yes">
            <span></span>
            <p>Ajouter une carte</p>
        </button>

        <div class="disponibilite">
            <p class="section-title">Mode de publication <button id="modifDispo">Modifier</button></p>
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
            if ($flashcardInfos['disponibilite'] == "ami"){
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
            echo '<label class="friends" '.$hidden2.'><input name="amiDispo[]" type="checkbox" value="tous" '.$checkedTous.' disabled>Tous les amis</label>';
            foreach($ALL_AMIS as $ami){
                if (in_array($ami['ami_id'], $TAB_AMIS)) {
                    $checked = 'checked';
                } else {
                    $checked = '';
                }
                echo '<label class="friends" '.$hidden2.'><input name="amiDispo[]" type="checkbox" value="'.htmlspecialchars($ami['ami_id']).'" '.$checked.' disabled>'.htmlspecialchars($ami['username']).'</label>';
            }
            
            ?>
        </div>
        <?php
            if ($erreur){
                echo '<p class="erreur">Chaque champ doit être rempli<br>Au moins une catégorie doit être sélectionnée</p>';
            } 
        ?>
    </form>
</div>
<script src = "./assets/js/popups.js"></script>
<script src = "./assets/js/modifyFlashcard.js"></script>
<script src = "./assets/js/sauvegardeScroll.js"></script>
<script src = "./assets/js/selectDispo.js"></script>
