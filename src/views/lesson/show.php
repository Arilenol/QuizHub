<?php
$title = "Leçon";
$style = './assets/style/lesson/show.css';
require_once '../src/views/partials/header.php';
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="./assets/js/lessonToPdf.js" defer></script>
<div class="buttonAction">
    <button onclick="window.location.href='?page=home'" class="button"><span></span>
        <p>← Retour</p>
    </button>
    <button class="button signalement" onclick="window.location.href='?page=signalement&id=<?= $lesson['id'] ?>&type=lesson'"><span></span>
        <p>Signaler cette leçon</p>
    </button>
</div>
<main class="lesson-page">

    <!-- Header de la leçon -->
    <section class="lesson-header">
        <h1><?= htmlspecialchars($lesson['title']) ?></h1>
        <div class="meta-info">
            <p><?= htmlspecialchars($lesson['username'] ?? '') ?> — <?= htmlspecialchars($lesson['date'] ?? '') ?></p>
        </div>
        <button class="button" onclick="save()"><span></span>
            <p>Télécharger la leçon</p>
        </button>
    </section>

    <!-- Introduction -->
    <?php if (!empty($lesson['description'])) : ?>
        <section class="lesson-intro">
            <p><?= nl2br(htmlspecialchars($lesson['description'])) ?></p>
        </section>
    <?php endif; ?>

    <!-- Parties de la leçon -->
    <?php
    $i = 0;
    foreach ($parties as $index => $part) :
    ?>
        <section class="lesson-part">
            <h2><?= htmlspecialchars($part['title']) ?></h2>
            <p><?= nl2br(htmlspecialchars($part['content'])) ?></p>

            <!-- Exemples -->
            <?php if (!empty($resultats[$i])) : ?>
                <?php foreach ($resultats[$i] as $exemple) : ?>
                    <div class="example-box">
                        <strong><?= htmlspecialchars($exemple['consigne']) ?></strong>
                        <p><?= htmlspecialchars($exemple['reponse']) ?></p>
                    </div>
                <?php endforeach;
                $i++; ?>
            <?php endif; ?>
        </section>

        <!-- Séparateur visuel si pas la dernière partie -->
        <?php if ($index < count($parties) - 1) : ?>
            <div class="divider"></div>
        <?php endif; ?>

    <?php endforeach; ?>
    <?php if (isset($lesson['quiz_id']) && $lesson['quiz_id'] !== null) :  ?>

    <div class="info">
        <p>
            Cette leçon offre un quiz pour vérifier ses connaissances
        </p>
        <a href="?page=<?= $lesson['genre'] == "test" ? "pageInterQuiz&type=test" : $lesson['genre'] ?>&id=<?= $lesson['quiz_id'] ?> <?= $lesson['genre'] === "flashcard" ? "&action=start" : "" ?>">Cliquez-ici pour commencer</a>
    </div>
    <?php endif; ?> 

</main>

</body>

</html>