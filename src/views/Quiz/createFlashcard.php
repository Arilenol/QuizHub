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
        <input type="text" name ="FlashcardTitle" value = "<?php echo $CardsTitle ?>">
    </div>
    <p class="description">Description</p>
    <div class="input">
        <span></span>
        <input type="text" name ="FlashcardDescription" value = "<?php echo $desc ?>">
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
            <textarea name ="cardQuestion'.$i.'"> '.$TAB_CONTENU[$i]['question'].'</textarea>
        </div>
        <p>Réponse :</p>
        <div class="textarea">
            <span></span>
            <textarea name = "cardReponse'.$i.'">'.$TAB_CONTENU[$i]['reponse'].'</textarea>
        </div>
        
        <button class="button" name = "DelCard" type="submit" value='.$i.'><span></span><p>Supprimer cette question</p></button>
        </div>';
    }
    echo '<button class="button" type = "submit" name = "addCard" value = "yes"><span></span><p>Ajouter une question</p></button>'
    ?>
    </div>

    <button class="button" type = "submit" name = "create" value = "yes"><span></span><p>Créer les flashcards</p></button>
</form>
<script src = "./assets/js/sauvegardeScroll.js"></script>