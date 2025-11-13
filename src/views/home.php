
<?php 
$title = "Accueil";
include 'partials/header.php'; ?>
    <h1>Créations populaires</h1>
    <div class=popCreations>
            <?php
                //en attente des nbJaimes
            ?>
    </div>
    <h1>Vos créations</h1>
    <div class=newCreations>
            <?php
                // attente du js pour faire avec un foreach
                // foreach ($quiz as $q) {
                //     echo '<article></article>';
                // }
                for ($i=0; $i < 7; $i++) { 
                    echo '<article>';
                    echo '<span class = title>'.$quiz[$i]['title'].'</span>';
                    echo '<span class = description>'.$quiz[$i]['description'].'</span>';
                    echo '<span class = username> Par '.$quiz[$i]['user_name'].'</span>';
                    echo '<span class = date> publié le : '.$quiz[$i]['date'].'</span>';
                    echo '</article>';
                }
            ?>
    </div>
</body>
</html>