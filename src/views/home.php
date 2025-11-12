<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../public/assets/style/home.css">
    <title>Accueil</title>
</head>
<body>
    <h1>Créations populaires</h1>
    <div class=popCreations>
            <?php

            ?>
    </div>
    <h1>Vos créations</h1>
    <div class=newCreations>
            <?php
                // foreach ($quiz as $q) {
                //     echo '<article></article>';
                // }
                for ($i=0; $i < 12; $i++) { 
                    echo '<article>';
                    echo '<span clas = title>'.$quiz[$i]['title'].'</span>';
                    echo '<span clas = title>'.$quiz[$i]['description'].'</span>';
                    echo '<span clas = title>'.$quiz[$i]['title'].'</span>';
                    echo '<span clas = title>'.$quiz[$i]['title'].'</span>';
                    echo '</article>';
                }
            ?>
    </div>
</body>
</html>