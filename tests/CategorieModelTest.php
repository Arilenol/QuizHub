<?php
use PHPUnit\Framework\TestCase;

require_once 'src\models\CategorieModel.php';

class CategorieModelTest extends TestCase
{
    private $CategorieModel;
    private $db;

    protected function setUp(): void
    {
        // Connexion à une base de test en mémoire (SQLite) pour éviter de toucher la base réelle
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Création des tables nécessaires pour les tests
        $this->db->exec("
            CREATE TABLE categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                categorieName TEXT UNIQUE NOT NULL,
                description TEXT
            );
        ");
        $this->db->exec("
            CREATE TABLE categorie_quiz (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER NOT NULL,
                quiz_id INTEGER NOT NULL
            );
        ");

        // Instanciation de la classe à tester
        $this->CategorieModel = new CategorieModel($this->db);
    }

    public function testCreateAndGetCategory(): void
    {
        $categoryId = $this->CategorieModel->createCategory('Test Cat', 'Description test');
        $this->assertIsString($categoryId);

        $category = $this->CategorieModel->getCategoryById($categoryId);
        $this->assertEquals('Test Cat', $category['categorieName']);
        $this->assertEquals('Description test', $category['description']);
    }

    public function testGetAllCategories(): void
    {
        $this->CategorieModel->createCategory('Cat 1');
        $this->CategorieModel->createCategory('Cat 2');

        $categories = $this->CategorieModel->getAllCategories();
        $this->assertCount(2, $categories);
        $this->assertEquals('Cat 1', $categories[0]['categorieName']);
        $this->assertEquals('Cat 2', $categories[1]['categorieName']);
    }

    public function testUpdateCategory(): void
    {
        $categoryId = $this->CategorieModel->createCategory('Old Name', 'Old Desc');
        $result = $this->CategorieModel->updateCategory($categoryId, 'New Name', 'New Desc');

        $this->assertTrue($result);
        $category = $this->CategorieModel->getCategoryById($categoryId);
        $this->assertEquals('New Name', $category['categorieName']);
        $this->assertEquals('New Desc', $category['description']);
    }

    public function testDeleteCategory(): void
    {
        $categoryId = $this->CategorieModel->createCategory('To Delete');
        $result = $this->CategorieModel->deleteCategory($categoryId);

        $this->assertTrue($result);
        $category = $this->CategorieModel->getCategoryById($categoryId);
        $this->assertFalse($category);
    }

    public function testGetQuizCountByCategory(): void
    {
        $categoryId = $this->CategorieModel->createCategory('With Quiz');

        // Ajout de 2 quiz à cette catégorie
        $this->db->exec("INSERT INTO categorie_quiz (category_id, quiz_id) VALUES ($categoryId, 1), ($categoryId, 2);");

        $count = $this->CategorieModel->getQuizCountByCategory($categoryId);
        $this->assertEquals(2, $count);
    }

    protected function tearDown(): void
    {
        $this->db = null;
        $this->CategorieModel = null;
    }
}
?>