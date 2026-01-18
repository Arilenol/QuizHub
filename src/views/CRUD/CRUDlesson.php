<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    echo "<link rel='stylesheet' href='./assets/style/CRUDrecherche.css'>";
    echo "<link rel='stylesheet' href='./assets/style/lesson/show.css'>";
    echo "<link rel='stylesheet' href='./assets/style/global.css'>";
    ?>
    <title>CRUD leçon - Détails</title>
</head>

<body>
    <div id="catalogue" class="catalogue">
        <button onclick="window.location.href='?page=CRUD'" class="retour" id="retour">&lt; Retour</button>

        <main class="lesson-page">

            <!-- Header de la leçon -->
            <section class="lesson-header">
                <h1><?= htmlspecialchars($lesson['title']) ?></h1>
                <div class="meta-info">
                    <p><?= htmlspecialchars($lesson['username'] ?? '') ?> — <?= htmlspecialchars($lesson['date'] ?? '') ?></p>
                </div>
                <?php if (!empty($lesson['categories']) && is_array($lesson['categories'])): ?>
                    <div class="quiz-cat">
                        <?php foreach ($lesson['categories'] as $cat): ?>
                            <span class="category"><?= htmlspecialchars($cat['categorieName']) ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Description -->
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

            <div class="info">
                <p>
                    Cette leçon offre un quiz pour vérifier ses connaissances
                </p>
                <a href="?page=<?= $lesson['genre'] ?>&id=<?= $lesson['quiz_id'] ?> <?= $lesson['genre'] === "flashcard" ? "&action=start" : "" ?>">Cliquez-ici pour commencer</a>
            </div>
        </main>
    </div>

</body>

</html>
