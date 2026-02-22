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
    echo "<link rel='stylesheet' href='./assets/style/global.css'>";
    echo "<link rel='stylesheet' href='./assets/style/categories.css'>";
    ?>
    <title>Gestion des Catégories</title>
</head>

<body>
    <div class="catalogue">
        <div class="button" style="margin : 25px" onclick="history.back()">
            <span></span>
            <p>← Retour</p>
        </div>

        <h1 class="page-title">Gestion des Catégories</h1>

        <!-- Affichage des messages -->
        <?php if (!empty($message)): ?>
            <div class="message <?= $messageType ?>">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout de catégorie -->
        <div class="add-category-form">
            <h2 style="margin-top: 0; color: #0AB1BD;">Créer une nouvelle catégorie</h2>
            <form method="POST" action="?page=Categorie">
                <input type="hidden" name="action" value="create">

                <div class="form-group">
                    <label for="categorieName">Nom de la catégorie *</label>
                    <input type="text" id="categorieName" name="categorieName" required
                        placeholder="Ex: Mathématiques, Français, etc...">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"
                        placeholder="Décrivez brièvement cette catégorie (optionnel)"></textarea>
                </div>

                <div class="form-buttons">
                    <div class="button" type="submit">
                        <span></span>
                        <p>Créer la catégorie</p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Liste des catégories -->
        <h2 style="color: #0AB1BD; margin-top: 30px;">Catégories existantes (<?= count($categories) ?>)</h2>

        <?php if (empty($categories)): ?>
            <div class="no-categories">
                <p>Aucune catégorie trouvée. Créez la première ci-dessus !</p>
            </div>
        <?php else: ?>
            <div class="categories-list">
                <?php foreach ($categories as $category): ?>
                    <div class="category-card">
                        <div class="category-name"><?= htmlspecialchars($category['categorieName']) ?></div>

                        <?php if (!empty($category['description'])): ?>
                            <div class="category-description">
                                <?= htmlspecialchars($category['description']) ?>
                            </div>
                        <?php else: ?>
                            <div class="category-description" style="color: #ccc;">
                                <em>Pas de description</em>
                            </div>
                        <?php endif; ?>

                        <div class="category-stats">
                            📊 <?= $category['quizCount'] ?> quiz associé<?= $category['quizCount'] > 1 ? 's' : '' ?>
                        </div>

                        <div class="category-actions">
                            <form method="POST" action="?page=Categorie" style="flex: 1;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="categoryId" value="<?= $category['id'] ?>">
                                <button type="submit" class="btn-delete"
                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ? Les quiz associés conserveront les autres catégories.');">
                                    🗑️ Supprimer
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>