<?php

class CategorieModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère toutes les catégories sous forme de tableau existantes dans la base de données.
     *
     * @return array<mixed> Un tableau associatif contenant toutes les catégories. Chaque élément contient :
     *                      - 'id' : int, l'identifiant de la catégorie
     *                      - 'categorieName' : string, le nom de la catégorie
     *                      - 'description' : string, la description de la catégorie
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception   Pour toute autre erreur générale.
     */
    public function getAllCategories(): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT id, categorieName, description FROM categories ORDER BY categorieName ASC;");
            $sql->execute();
            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        } catch (PDOException $e) {
            die("Fetching all categories failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Crée une nouvelle catégorie dans la base de données.
     *
     * @param string $categorieName Le nom de la catégorie à créer.
     * @param string $description   La description de la catégorie (optionnelle, par défaut '').
     *
     * @return int L'identifiant de la catégorie nouvellement créée.
     *
     * @throws Exception Si la catégorie existe déjà (contrainte UNIQUE).
     * @throws PDOException Si la requête SQL échoue pour une autre raison.
     * @throws Exception   Pour toute autre erreur générale.
     */
    public function createCategory(string $categorieName, string $description = ''): mixed
    {
        try {
            $sql = $this->db->prepare("INSERT INTO categories (categorieName, description) VALUES (?, ?);");
            $sql->bindParam(1, $categorieName);
            $sql->bindParam(2, $description);
            $sql->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            // Vérifier si c'est une erreur de contrainte UNIQUE
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                throw new Exception("Cette catégorie existe déjà.");
            }
            die("Creating category failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    /**
     * Supprime une catégorie existante.
     *
     * Les quiz associés à cette catégorie conserveront leurs autres catégories.
     *
     * @param int $categoryId L'identifiant de la catégorie à supprimer.
     *
     * @return bool True si la suppression a réussi.
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception   Pour toute autre erreur générale.
     */
    public function deleteCategory(int $categoryId): bool
    {
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
        } catch (PDOException $e) {
            die("Deleting category failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    /**
     * Récupère le nombre de quiz associés à une catégorie spécifique.
     *
     * @param int $categoryId L'identifiant de la catégorie.
     *
     * @return int Le nombre de quiz associés à cette catégorie.
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception   Pour toute autre erreur générale.
     */
    public function getQuizCountByCategory(int $categoryId): int
    {
        try {
            $sql = $this->db->prepare("SELECT COUNT(*) as count FROM categorie_quiz WHERE category_id = ?;");
            $sql->bindParam(1, $categoryId);
            $sql->execute();
            $result = $sql->fetch(PDO::FETCH_ASSOC);
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            die("Counting quizzes by category failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Met à jour les informations d'une catégorie existante.
     *
     * @param int $categoryId       L'identifiant de la catégorie à mettre à jour.
     * @param string $categorieName Le nouveau nom de la catégorie.
     * @param string $description   La nouvelle description de la catégorie (optionnelle, par défaut '').
     *
     * @return bool True si la mise à jour a réussi.
     *
     * @throws Exception Si le nouveau nom de catégorie existe déjà (contrainte UNIQUE).
     * @throws PDOException Si la requête SQL échoue pour une autre raison.
     * @throws Exception   Pour toute autre erreur générale.
     */
    public function updateCategory(int $categoryId, string $categorieName, string $description = ''): bool
    {
        try {
            $sql = $this->db->prepare("UPDATE categories SET categorieName = ?, description = ? WHERE id = ?;");
            $sql->bindParam(1, $categorieName);
            $sql->bindParam(2, $description);
            $sql->bindParam(3, $categoryId);
            $sql->execute();
            return true;
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'UNIQUE') !== false) {
                throw new Exception("Cette catégorie existe déjà.");
            }
            die("Updating category failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Récupère les informations d'une catégorie à partir de son identifiant.
     *
     * @param int $categoryId L'identifiant de la catégorie à récupérer.
     *
     * @return array<mixed>|false Un tableau associatif contenant :
     *                             - 'id' : int, l'identifiant de la catégorie
     *                             - 'categorieName' : string, le nom de la catégorie
     *                             - 'description' : string, la description de la catégorie
     *                             Ou false si la catégorie n'existe pas.
     *
     * @throws PDOException Si la requête SQL échoue.
     * @throws Exception   Pour toute autre erreur générale.
     */
    public function getCategoryById(int $categoryId): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT id, categorieName, description FROM categories WHERE id = ?;");
            $sql->bindParam(1, $categoryId);
            $sql->execute();
            $category = $sql->fetch(PDO::FETCH_ASSOC);
            return $category;
        } catch (PDOException $e) {
            die("Fetching category by ID failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
