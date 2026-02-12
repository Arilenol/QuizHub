<?php
// Définir la constante ROOT si elle n'existe pas
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}

require_once 'config.php';

function migrationUpdateTables() {
    try {
        $db = getDbConnection();
        
        echo "Migration en cours...\n";
        
        // Supprimer les anciennes tables s'il y en a
        echo "Suppression des anciennes tables...\n";
        $db->exec("DROP TABLE IF EXISTS signalements;");
        $db->exec("DROP TABLE IF EXISTS notifications;");
        
        // Créer la table notifications
        echo "Création de la table notifications...\n";
        $sql = "CREATE TABLE IF NOT EXISTS notifications(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                type TEXT NOT NULL,
                message TEXT NOT NULL,
                contenu_id INTEGER,
                contenu_type TEXT,
                date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
                is_read BOOLEAN DEFAULT 0,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                CHECK (contenu_type IN ('quiz','lesson','signalement'))
            );";
        $db->exec($sql);
        
        // Créer la table signalements
        echo "Création de la table signalements...\n";
        $sql = "CREATE TABLE IF NOT EXISTS signalements(
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                quiz_id INTEGER,
                lesson_id INTEGER,
                type TEXT NOT NULL,
                description TEXT NOT NULL,
                date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
                status TEXT DEFAULT 'nouveau',
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (quiz_id) REFERENCES quiz(id) ON DELETE SET NULL,
                FOREIGN KEY (lesson_id) REFERENCES Lecon(id) ON DELETE SET NULL,
                CHECK (type IN ('contenu_inapproprie','spam','harcelement','autre')),
                CHECK (status IN ('nouveau','en_cours','resolu','rejete'))
            );";
        $db->exec($sql);
        
        echo "✓ Migration terminée avec succès!\n";
        echo "Les tables notifications et signalements ont été créées/mises à jour.\n";
        
    } catch (PDOException $e) {
        echo "✗ Erreur PDO: " . $e->getMessage() . "\n";
        die();
    } catch (Exception $e) {
        echo "✗ Erreur: " . $e->getMessage() . "\n";
        die();
    }
}

// Exécuter si le fichier est appelé directement
if (php_sapi_name() === 'cli' || (isset($_GET['run_migration']) && $_GET['run_migration'] === 'true')) {
    migrationUpdateTables();
}
?>
