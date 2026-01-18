<?php
    require_once ROOT . '/src/models/CategorieModel.php';
    require_once ROOT . '/config/config.php';

    class CategorieController {
        private $model;

        public function index() {
            $db = getDbConnection();
            $this->model = new CategorieModel($db);
            
            $message = '';
            $messageType = ''; // 'success' ou 'error'
            
            // Traiter la création d'une nouvelle catégorie
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
                if ($_POST['action'] === 'create' && isset($_POST['categorieName'])) {
                    $categorieName = htmlspecialchars(trim($_POST['categorieName']));
                    $description = htmlspecialchars(trim($_POST['description'] ?? ''));
                    
                    if (!empty($categorieName)) {
                        try {
                            $this->model->createCategory($categorieName, $description);
                            $message = "Catégorie créée avec succès !";
                            $messageType = 'success';
                        } catch (Exception $e) {
                            $message = "Erreur : " . $e->getMessage();
                            $messageType = 'error';
                        }
                    } else {
                        $message = "Le nom de la catégorie ne peut pas être vide.";
                        $messageType = 'error';
                    }
                }
                // Traiter la suppression d'une catégorie
                elseif ($_POST['action'] === 'delete' && isset($_POST['categoryId'])) {
                    $categoryId = (int)$_POST['categoryId'];
                    try {
                        $this->model->deleteCategory($categoryId);
                        $message = "Catégorie supprimée avec succès !";
                        $messageType = 'success';
                    } catch (Exception $e) {
                        $message = "Erreur lors de la suppression : " . $e->getMessage();
                        $messageType = 'error';
                    }
                }
            }
            
            // Récupérer toutes les catégories
            $categories = $this->model->getAllCategories();
            
            // Récupérer le nombre de quiz pour chaque catégorie
            foreach ($categories as &$category) {
                $category['quizCount'] = $this->model->getQuizCountByCategory($category['id']);
            }
            unset($category);
            
            require ROOT . '/src/views/CRUD/CRUDcategories.php';
        }
    }
?>
