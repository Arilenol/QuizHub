<?php
require_once ROOT . '/src/models/SignalementModel.php';
require_once ROOT . '/src/models/NotificationModel.php';
require_once ROOT . '/config/config.php';

class SignalementController
{

    private SignalementModel $model;
    private NotificationModel $notificationModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = getDbConnection();
        $this->model = new SignalementModel($this->db);
        $this->notificationModel = new NotificationModel($this->db);
    }

    public function index(){
        require_once ROOT. '/src/views/quiz/signalement.php';
    }

    public function submitReport(): void
    {
        // Vérifier que l'utilisateur est connecté
        if (!isset($_SESSION['id'])) {
            header('Location: ?page=log');
            exit;
        }

        // Récupérer et valider les données du formulaire
        $type = $_POST['type'] ?? '';
        $description = $_POST['description'] ?? '';
        $quiz_id = isset($_POST['quiz_id']) && !empty($_POST['quiz_id']) ? (int)$_POST['quiz_id'] : null;
        $lesson_id = isset($_POST['lesson_id']) && !empty($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : null;

        // Valider les données
        $types_valides = ['contenu_inapproprie', 'spam', 'harcelement', 'autre'];
        if (!in_array($type, $types_valides) || empty(trim($description))) {
            header('Location: ?page=signalement');
            exit;
        }

        $db = $this->db;
        
        try {
            // Démarrer une transaction pour regrouper les écritures
            $db->beginTransaction();
            
            // Sauvegarder le signalement
            $signalement_id = $this->model->createSignalement($_SESSION['id'], $type, $description, $quiz_id, $lesson_id);

            if ($signalement_id !== false) {
                // Récupérer le nom d'utilisateur de l'auteur
                $stmt = $db->prepare("SELECT username FROM users WHERE id = ?");
                $stmt->execute([$_SESSION['id']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $auteur_name = $user['username'] ?? 'Utilisateur inconnu';
                
                // Récupérer les infos du quiz ou de la leçon
                $quiz_info = null;
                $lesson_info = null;
                if ($quiz_id) {
                    $stmt = $db->prepare("SELECT title FROM quiz WHERE id = ?");
                    $stmt->execute([$quiz_id]);
                    $quiz_info = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                if ($lesson_id) {
                    $stmt = $db->prepare("SELECT title FROM Lecon WHERE id = ?");
                    $stmt->execute([$lesson_id]);
                    $lesson_info = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                // Envoyer les notifications à tous les admins
                $this->notifyAdmins($db, $signalement_id, $type, $description, $auteur_name, $quiz_id, $lesson_id, $quiz_info, $lesson_info);
                
                // Valider la transaction
                $db->commit();
                
                // Afficher la page de confirmation
                require_once ROOT . '/src/views/quiz/submitReport.php';
            } else {
                // Annuler la transaction en cas d'erreur
                $db->rollBack();
                header('Location: ?page=signalement');
                exit;
            }
        } catch (Exception $e) {
            // Annuler la transaction en cas d'exception
            $db->rollBack();
            header('Location: ?page=signalement');
            exit;
        }
    }

    private function notifyAdmins($db, int|string $signalement_id, string $type, string $description, string $auteur_name, ?int $quiz_id, ?int $lesson_id, ?array $quiz_info, ?array $lesson_info): void
    {
        // Récupérer tous les admins
        $stmt = $db->prepare("SELECT id FROM users WHERE admin = 1");
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Types lisibles en français
        $types_label = [
            'contenu_inapproprie' => 'Contenu inapproprié',
            'spam' => 'Spam',
            'harcelement' => 'Harcèlement',
            'autre' => 'Autre'
        ];

        $type_label = $types_label[$type] ?? $type;
        
        // Construire le message de notification
        $contenu_part = '';
        if ($quiz_info && isset($quiz_info['title'])) {
            $contenu_part = " - Quiz: \"{$quiz_info['title']}\"";
        } elseif ($lesson_info && isset($lesson_info['title'])) {
            $contenu_part = " - Leçon: \"{$lesson_info['title']}\"";
        }
        
        $message = "Nouveau signalement (#{$signalement_id}) par {$auteur_name}: {$type_label}{$contenu_part} - " . substr($description, 0, 50) . "...";

        // Envoyer une notification à chaque admin
        foreach ($admins as $admin) {
            $this->notificationModel->createNotification(
                $admin['id'],
                'signalement',
                $message,
                $signalement_id,
                'signalement'
            );
        }
    }
}

?>