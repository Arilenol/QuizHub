<?php
    $title = 'création de leçons';
    $style = './assets/style/createLesson.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>

<form method = "post" action = "index.php?page=lesson&categorie=create">
    <button type = "submit" name = "Retour" value = "yes"><span> < </span>Retour</button>
    <h1>Créer une leçon</h1>
    <input type = "hidden" name="page" value="lesson">
    <input type="hidden" name ="categorie" value = "create">
    <h2>Nom de la leçon</h2>
    <input type="text" name ="LessonTitle" value = "<?php echo $title ?>">
    <p class="description">Description</p>
    <input type="text" name ="LessonDescription" value = "<?php echo $desc ?>">
    <div class = "newLesson">
    <p class = "LessonParts">Parties</p>
    <?php
    for($i = 0; $i < $_SESSION['nbParts'] ; $i = $i +1){
        echo '<div class="LessonPart">
        <p>Partie '.($i+1).'</p>
        <input type = "text" name ="namePart'.$i.'" value = "'.$TAB_CONTENU[$i]['name'].'" placeholder="nom de la partie">
        <p>Leçon :</p>
        <textarea name = "contentPart'.$i.'">'.$TAB_CONTENU[$i]['content'].'</textarea>';
        
        for ($k = 0; $k < $_SESSION['nbExemple'][$i];$k = $k +1){
            echo '<div class="exemple">
            <p>Exemple '.($k+1).' :</p>
            <p>consigne : <p>
            <textarea name ="exemple'.$k.'-part'.$i.'">'.$TAB_CONTENU[$i]['exemples'][$k]['consigne'].'</textarea>
            <p>réponse : <p>
            <textarea name ="reponse'.$k.'-part'.$i.'">'.$TAB_CONTENU[$i]['exemples'][$k]['reponse'].'</textarea>
            <button name= "delExemple'.$k.'-part'.$i.'" value="yes" type="submit"> Supprimer cet exemple</button>
            </div>';

        }
        echo '<button type = "submit" name="addExemple" value='.$i.'>Ajouter un exemple</button>
        <button name = "DelPart" type="submit" value='.$i.'>Supprimer cette partie</button>
        </div>';
    }
    echo '<button type = "submit" name = "addPart" value = "yes">Ajouter une nouvelle partie</button>'
    ?>
    </div>

    <select name="linkedQuiz" onchange = "this.form.submit()">
        <option value ="Aucun">Aucun</option>
        <?php
        foreach($quizzes as $quiz){
            if ($quizSelected == $quiz['id']) {
                echo '<option value='.$quiz['id'].' selected>'.$quiz['title'].'</option>';
            } else {
            echo '<option value='.$quiz['id'].' >'.$quiz['title'].'</option>';
            }
        }
        ?>
    </select>
    <button type = "submit" name = "create" value = "yes">Créer la leçon</button>
</form>