<!DOCTYPE html>
<html lang="fr">
<?php
require_once '../src/views/partials/header.php';
?>

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
        <div class="button" style="margin : 25px" onclick="history.back()">
            <span></span>
            <p>← Retour</p>
        </div>

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

                <!-- Gestion de la disponibilité -->
                <form method="POST" action="" style="margin-top: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9;">
                    <input type="hidden" name="action" value="update_disponibilite">
                    <input type="hidden" name="lesson_id" value="<?= htmlspecialchars($lesson['id']) ?>">
                    <p style="font-weight: bold; margin-bottom: 10px;">Mode de publication :</p>
                    <select name="disponibilite" required style="padding: 5px; font-size: inherit;">
                        <option value="public" <?= ($lesson['disponibilite'] ?? '') === 'public' ? 'selected' : '' ?>>Publique</option>
                        <option value="ami" <?= ($lesson['disponibilite'] ?? '') === 'ami' ? 'selected' : '' ?>>Seulement les amis</option>
                        <option value="private" <?= ($lesson['disponibilite'] ?? '') === 'private' ? 'selected' : '' ?>>Privé</option>
                    </select>
                    <button type="submit" style="margin-top: 10px; margin-left: 10px;">Mettre à jour la disponibilité</button>
                </form>
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
            <?php if($lesson['quiz_id']!==null): ?>
            <div class="info">
                <p>
                    Cette leçon offre un quiz pour vérifier ses connaissances
                </p>
                <a href="?page=<?= $lesson['genre'] ?>&id=<?= $lesson['quiz_id'] ?> <?= $lesson['genre'] === "flashcard" ? "&action=start" : "" ?>">Cliquez-ici pour commencer</a>
            </div>
            <?php endif; ?>
        </main>
    </div>

</body>

</html>