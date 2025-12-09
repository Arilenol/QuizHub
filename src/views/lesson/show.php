<?php
$title = "Leçon";
$style = '../public/assets/style/lesson/show.css';
include __DIR__ . '/../partials/header.php';
?>

<button onclick="window.location.href='?page=home'" class="back-btn">← Retour</button>

<main class="lesson-page">

    <!-- Header de la leçon -->
    <section class="lesson-header">
        <h1><?= htmlspecialchars($lesson['title']) ?></h1>
        <div class="meta-info">
            <!-- Si tu veux afficher l'auteur et la date -->
            <p><?= htmlspecialchars($lesson['username'] ?? '') ?> — <?= htmlspecialchars($lesson['date'] ?? '') ?></p>
        </div>
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

</main>

</body>
</html>
