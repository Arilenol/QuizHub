<?php
    $title = 'création de flashcards';
    $style = './assets/style/createFlashcards.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>

<form method = "post" action = "index.php?page=flashcard&categorie=create">
    <button type = "submit" name = "Retour" value = "yes"><span> < </span>Retour</button>
    <h1>Créer une Flashcard</h1>
    <input type = "hidden" name="page" value="flashcard">
    <input type="hidden" name ="categorie" value = "create">
    <h2>Nom des flashcards</h2>
    <input type="text" name ="FlashcardTitle" value = "<?php echo $title ?>">
    <p class="description">Description</p>
    <input type="text" name ="FlashcardDescription" value = "<?php echo $desc ?>">
    <div class = "newflashcard">
    <p class = "cartes">Cartes</p>
    <?php
    for($i = 0; $i < $_SESSION['nbCartes'] ; $i = $i +1){
        echo '<div class="Carte">
        <p>Carte '.($i+1).'</p>
        <p>Question :</p>
        <textarea name ="cardQuestion'.$i.'"> '.$TAB_CONTENU[$i]['question'].'</textarea>
        <p>Réponse :</p>
        <textarea name = "cardReponse'.$i.'">'.$TAB_CONTENU[$i]['reponse'].'</textarea>
        
        <button name = "DelCard" type="submit" value='.$i.'>Supprimer cette question</button>
        </div>';
    }
    echo '<button type = "submit" name = "addCard" value = "yes">Ajouter une question</button>'
    ?>
    </div>

    <button type = "submit" name = "create" value = "yes">Créer les flashcards</button>
</form>