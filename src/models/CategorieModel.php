<?php

class CategorieModel {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère toutes les catégories
     */
    public function getAllCategories(): mixed {
        try {
            $sql = $this->db->prepare("SELECT id, categorieName, description FROM categories ORDER BY categorieName ASC;");
            $sql->execute();
            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        } catch(PDOException $e) {
            die("Fetching all categories failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Crée une nouvelle catégorie
     */
    public function createCategory($categorieName, $description = ''): mixed {
        try {
            $sql = $this->db->prepare("INSERT INTO categories (categorieName, description) VALUES (?, ?);");
            $sql->bindParam(1, $categorieName);
            $sql->bindParam(2, $description);
            $sql->execute();
            return $this->db->lastInsertId();
        } catch(PDOException $e) {
            // Vérifier si c'est une erreur de contrainte UNIQUE
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                throw new Exception("Cette catégorie existe déjà.");
            }
            die("Creating category failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Supprime une catégorie
     * Attention: Les quiz associés conserveront les autres catégories
     */
    public function deleteCategory($categoryId): bool {
        try {
            // D'abord supprimer les associations de la catégorie_quiz
            $sql = $this->db->prepare("DELETE FROM categorie_quiz WHERE category_id = ?;");
            $sql->bindParam(1, $categoryId);
            $sql->execute();
            
            // Ensuite supprimer la catégorie elle-même
            $sql = $this->db->prepare("DELETE FROM categories WHERE id = ?;");
            $sql->bindParam(1, $categoryId);
            $sql->execute();
            
            return true;
        } catch(PDOException $e) {
            die("Deleting category failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère le nombre de quiz associés à une catégorie
     */
    public function getQuizCountByCategory($categoryId): int {
        try {
            $sql = $this->db->prepare("SELECT COUNT(*) as count FROM categorie_quiz WHERE category_id = ?;");
            $sql->bindParam(1, $categoryId);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch(PDOException $e) {
            die("Counting quizzes by category failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Met à jour une catégorie
     */
    public function updateCategory($categoryId, $categorieName, $description = ''): bool {
        try {
            $sql = $this->db->prepare("UPDATE categories SET categorieName = ?, description = ? WHERE id = ?;");
            $sql->bindParam(1, $categorieName);
            $sql->bindParam(2, $description);
            $sql->bindParam(3, $categoryId);
            $sql->execute();
            return true;
        } catch(PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                throw new Exception("Cette catégorie existe déjà.");
            }
            die("Updating category failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère une catégorie par son ID
     */
    public function getCategoryById($categoryId): mixed {
        try {
            $sql = $this->db->prepare("SELECT id, categorieName, description FROM categories WHERE id = ?;");
            $sql->bindParam(1, $categoryId);
            $sql->execute();
            $category = $sql->fetch(PDO::FETCH_ASSOC);
            return $category;
        } catch(PDOException $e) {
            die("Fetching category by ID failed: " . $e->getMessage());
        } catch(Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}

?>
