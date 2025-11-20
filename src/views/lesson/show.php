<?php 
$title = "Leçon";
$style = '../public/assets/style/lesson/show.css';
include __DIR__ . '/../partials/header.php';
?>

    <main class="lesson-page">
    <button onclick=window.location.href="?page=home" class="back-btn">← Retour</button>

    <section class="lesson-header">
      <h2><?=htmlspecialchars($lesson['title'])?></h2>
      <p><?=htmlspecialchars($lesson['description'])?></p>
    </section>
<?php
    $i=0;
    foreach ($parties as $part) {
        echo'<section class="lesson-part">';
        echo '<h3>'.htmlspecialchars($part['title']).'</h3>';
        echo'<p>'.htmlspecialchars($part['content']).'</p>';
        if (!empty($resultats[$i])){
            foreach ($resultats[$i] as $exemple){
                echo'<div class="example-box">';
                    echo'<p><strong>'.htmlspecialchars($exemple['consigne']).'</strong></p>';
                    echo'<p><strong>'.htmlspecialchars($exemple['reponse']).'</strong></p>';
                echo'</div>';
        }
        $i++;
    }
        echo'</section>';
    }
?>
    </main>
</body>
</html>
