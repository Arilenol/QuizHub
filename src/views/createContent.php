<?php
    $title = 'création de contenu';
    $style = './assets/style/createContent.css';

    require __DIR__ .'/partials/header.php';

?>

<button type = "submit" name = "Retour" value = "yes"><span> < </span>Retour</button>
<h1>Que souhaitez-vous créer aujourd'hui ?</h1>
<div class = "createCategories">
    <div class = "category" onclick = "window.location.href= 'index.php?page=standard&categorie=create'" >
        <img alt="icône pour les quizs/test">
        <p class = "nameCategory">Quiz/Test</p>
        <p class = "descriptionCategory">
            Un quiz ou un test est une série de QCM. 
            Le quiz se distingue du test par la possibilité de voir immédiatement les résultats après chaque réponse, 
            ainsi que de retenter les questions échouées.
        </p>
    </div>
    <div class = "category" onclick = "window.location.href = 'index.php?page=lesson&categorie=create'">
        <img alt="icône pour les leçons">
        <p class = "nameCategory">Leçon</p>
        <p class = "descriptionCategory">
            Une leçon est un contenu présentant des explications ou des notions théoriques sur un thème précis. 
            Elle peut être associée à un quiz afin de permettre à l’utilisateur de réviser avant d’effectuer un quiz ou autre.
        </p>
    </div>
    <div class = "category" onclick = "window.location.href = 'index.php?page=flashcard&categorie=create'">
        <img alt="icône pour les flashcards">
        <p class = "nameCategory">Flashcard</p>
        <p class = "descriptionCategory">
            Les flashcards sont un outil d'entraînement qui permet de tester ses connaissances : 
            on essaie de répondre à une question présentée seule, 
            puis on consulte la réponse pour vérifier si l'on a juste.
        </p>
    </div>
</div>