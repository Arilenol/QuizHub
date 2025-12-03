<?php
    $title = 'création de contenu';
    $style = './assets/style/createContent.css';

    require __DIR__ .'/partials/header.php';

?>

<div style="display: flex; flex-direction: column; padding: 25px">
    <button class="button" type = "submit" name = "Retour" onclick = "window.location.href='index.php?page=home'" value = "yes">
        <span></span> <p>< Retour</p></button>
    <h1 style="text-align: center   ">Que souhaitez-vous créer aujourd'hui ?</h1>
    <div class = "createCategories">
        <div class="category" onclick = "window.location.href= 'index.php?page=standard&categorie=create'" >
            <img src="./assets/images/quiz.svg" alt="icône pour les quizs/test">
            <h1 class = "nameCategory">Quiz/Test</h1>
            <p class = "descriptionCategory">
                Un quiz ou un test est une série de QCM. 
                Le quiz se distingue du test par la possibilité de voir immédiatement les résultats après chaque réponse, 
                ainsi que de retenter les questions échouées.
            </p>
        </div>
        <div class = "category" onclick = "window.location.href = 'index.php?page=lesson&categorie=create'">
            <img src="./assets/images/lecon.svg" alt="icône pour les leçons">
            <h1 class = "nameCategory">Leçon</h1>
            <p class = "descriptionCategory">
                Une leçon est un contenu présentant des explications ou des notions théoriques sur un thème précis. 
                Elle peut être associée à un quiz afin de permettre à l’utilisateur de réviser avant d’effectuer un quiz ou autre.
            </p>
        </div>
        <div class = "category" onclick = "window.location.href = 'index.php?page=flashcard&categorie=create'">
            <img src="./assets/images/flashcard.svg" alt="icône pour les flashcards">
            <h1 class = "nameCategory">Flashcard</h1>
            <p class = "descriptionCategory">
                Les flashcards sont un outil d'entraînement qui permet de tester ses connaissances : 
                on essaie de répondre à une question présentée seule, 
                puis on consulte la réponse pour vérifier si l'on a juste.
            </p>
        </div>
    </div>
</div>