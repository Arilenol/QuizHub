<?php
    $title = 'création de quiz';
    $style = './assets/style/createQuiz.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>

<form method = "post" action = "index.php?page=standard&categorie=create">
    <button type = "submit" name = "Retour" value = "yes"><span> < </span>Retour</button>
    <h1>Créer un Quiz</h1>
    <input type = "hidden" name="page" value="standard">
    <input type="hidden" name ="categorie" value = "create">
    <h2>Nom du quiz</h2>
    <input type="text" name ="QuizTitle" value = "<?php echo htmlspecialchars($title) ?>">
    <p class="description">Description</p>
    <input type="text" name ="QuizDescription" value = "<?php echo htmlspecialchars($desc) ?>">
    <div class = "newQuiz">
    <p class="validite">réponse valide ?</p>
    <?php
    for($i = 0; $i < $_SESSION['nbQuestions'] ; $i = $i +1){
        echo '<div class="Question">
        <p>Question</p>
        <input type = "text" name ="question'.$i.'" value = "'.$TAB_CONTENU[$i]['name'].'" placeholder="nom de la question">';
        
        for ($k = 0; $k < $_SESSION['nbReponse'][$i];$k = $k +1){
            echo '<div class="reponse">
            <p>Réponse '.($k+1).' :</p>
            <textarea name ="reponse'.$k.'-question'.$i.'" value = "">'.$TAB_CONTENU[$i]['reponses'][$k]['texte'].'</textarea>
            <div class="checkbox">
                <input type="checkbox" name="checkbox'.$k.'-question'.$i.'" '.($TAB_CONTENU[$i]['reponses'][$k]['valide'] ? 'checked':'').'>
            </div>
            </div>';
            //----------------------------------------faudra demander à kilian---------------------------------------

        }
        echo '<button name= "delReponse'.$i.'" value="yes" type="submit"> Supprimer une réponse</button>
        <button type = "submit" name="addReponse" value='.$i.'>Ajouter une réponse</button>';
        if ($i != 0){
            echo '<button name = "DelQuestion" type="submit" value='.$i.'>Supprimer cette question</button>';
        }
        echo '</div>';
    }
    ?>
    <button type = "submit" name = "addQuestion" value = "yes">Ajouter une question</button>
    
    </div>
    
    <h2>Paramètres</h2>
    <div class = "parametres">
        <?php
            foreach ($tabParametres as $indice => $param){
                //----------------------------------------faudra demander à kilian---------------------------------------
                echo '<p>'.$param['name'].'</p>
                <div class="checkbox">
                    <input type="checkbox" name="param'.$indice.'">
                </div>';
            }

        ?>
    </div>

    <button type = "submit" name = "create" value = "yes">Créer le quiz</button>
</form>