<?php
$title = "Signaler un contenu";
$style = './assets/style/quiz/signalement.css';
require_once '../src/views/partials/header.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    echo "<div style='text-align: center; padding: 40px;'>";
    echo "<p style='font-size: 18px; color: red;'>Vous devez être connecté pour signaler un contenu.</p>";
    echo "</div>";
    exit;
}
?>

<button class="retour" onclick="history.back()">← Retour</button>
<div class="signalement-page">
    <h1>Signaler un contenu</h1>

    <form action="?page=submitReport" method="post" class="signalement-form">
        <!-- IDs du contenu (caché) - on passe les deux, un seul sera rempli -->
        <input type="hidden" name="quiz_id" value="<?= isset($_GET['id']) && (!isset($_GET['type']) || $_GET['type'] === 'quiz') ? htmlspecialchars($_GET['id']) : '' ?>">
        <input type="hidden" name="lesson_id" value="<?= isset($_GET['id']) && isset($_GET['type']) && $_GET['type'] === 'lesson' ? htmlspecialchars($_GET['id']) : '' ?>">
        
        <!-- Choix du type de signalement -->
        <label for="type">Type de signalement :</label>
        <select name="type" id="type" required>
            <option value="" disabled selected>-- Sélectionnez un type --</option>
            <option value="contenu_inapproprie">Contenu inapproprié</option>
            <option value="spam">Spam</option>
            <option value="harcelement">Harcèlement</option>
            <option value="autre">Autre</option>
        </select>

        <!-- Description du problème -->
        <label for="description">Description :</label>
        <textarea name="description" id="description" rows="5" placeholder="Expliquez brièvement le problème..." required></textarea>

        <!-- Bouton de soumission -->
        <button type="submit">Envoyer le signalement</button>
    </form>
</div>