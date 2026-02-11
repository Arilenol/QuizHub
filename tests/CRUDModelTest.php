<?php
use PHPUnit\Framework\TestCase;


// require_once 'src\models\CRUDModel.php';
// class CRUDModelTest extends TestCase
// {
//     private $categoryModel;
//     private $manager;
//     private $db;

//     protected function setUp(): void
//     {
//         // Connexion à une base SQLite en mémoire
//         $this->db = new PDO('sqlite::memory:');
//         $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//         // Création des tables nécessaires pour les tests
//         $this->db->exec("
//             CREATE TABLE users (
//                 id INTEGER PRIMARY KEY AUTOINCREMENT,
//                 username TEXT UNIQUE NOT NULL,
//                 email TEXT,
//                 description TEXT,
//                 admin INTEGER DEFAULT 0
//             );
//         ");

//         $this->db->exec("
//             CREATE TABLE categories (
//                 id INTEGER PRIMARY KEY AUTOINCREMENT,
//                 categorieName TEXT UNIQUE NOT NULL
//             );
//         ");

//         $this->db->exec("
//             CREATE TABLE quiz (
//                 id INTEGER PRIMARY KEY AUTOINCREMENT,
//                 title TEXT,
//                 description TEXT,
//                 difficulty INTEGER,
//                 user_id INTEGER,
//                 date TEXT,
//                 genre TEXT
//             );
//         ");

//         $this->db->exec("
//             CREATE TABLE categorie_quiz (
//                 quiz_id INTEGER,
//                 category_id INTEGER
//             );
//         ");

//         $this->db->exec("
//             CREATE TABLE lecon (
//                 id INTEGER PRIMARY KEY AUTOINCREMENT,
//                 title TEXT,
//                 description TEXT,
//                 date TEXT,
//                 quiz_id INTEGER,
//                 user_id INTEGER
//             );
//         ");

//         $this->db->exec("
//             CREATE TABLE likes (
//                 quiz_id INTEGER
//             );
//         ");

//         $this->db->exec("
//             CREATE TABLE dislikes (
//                 quiz_id INTEGER
//             );
//         ");

//         // Instanciation de la classe à tester
//         $this->manager = new CRUDModel($this->db);
//         $this->categoryModel = new CategorieModel($this->db);
//     }

//     /** Tests des catégories */
//     public function testCreateAndGetCategories(): void
//     {
//         $catId = $this->categoryModel->createCategory('Maths');
//         $this->assertIsInt($catId);

//         $categories = $this->manager->getCategories();
//         $this->assertCount(1, $categories);
//         $this->assertEquals('Maths', $categories[0]['CategorieName']);
//     }

//     public function testGetCategoriesFromQuiz(): void
//     {
//         $this->db->exec("INSERT INTO users (username) VALUES ('Alice')");
//         $userId = $this->db->lastInsertId();

//         $stmt = $this->db->prepare("INSERT INTO quiz (title, description, difficulty, user_id, date, genre) VALUES (?, ?, ?, ?, ?, ?)");
//         $stmt->execute(['Quiz Test', 'Desc', 2, $userId, date('Y-m-d'), 'Science']);
//         $quizId = $this->db->lastInsertId();

//         $catId = $this->categoryModel->createCategory('Science');
//         $this->db->exec("INSERT INTO categorie_quiz (quiz_id, category_id) VALUES ($quizId, $catId)");

//         $categories = $this->manager->getCategoriesFromQuiz($quizId);
//         $this->assertCount(1, $categories);
//         $this->assertEquals('Science', $categories[0]['categorieName']);
//     }

//     /** Tests des quiz */
//     public function testSearchQuizByTitleAndAll(): void
//     {
//         $this->db->exec("INSERT INTO users (username) VALUES ('Bob')");
//         $userId = $this->db->lastInsertId();

//         $quizStmt = $this->db->prepare("INSERT INTO quiz (title, description, difficulty, user_id, date, genre) VALUES (?, ?, ?, ?, ?, ?)");
//         $quizStmt->execute(['Quiz 1', 'Desc 1', 1, $userId, date('Y-m-d'), 'Math']);
//         $quizId = $this->db->lastInsertId();

//         $catId = $this->categoryModel->createCategory('Math');
//         $this->db->exec("INSERT INTO categorie_quiz (quiz_id, category_id) VALUES ($quizId, $catId)");

//         $resultTitle = $this->manager->searchQuizByTitle('Quiz 1');
//         $this->assertCount(1, $resultTitle);
//         $this->assertEquals('Quiz 1', $resultTitle[0]['title']);

//         $resultAll = $this->manager->searchQuizByAll($catId, 'Desc 1', 'Bob', '', 'title_asc');
//         $this->assertCount(1, $resultAll);
//         $this->assertEquals('Quiz 1', $resultAll[0]['title']);
//     }

//     public function testSearchQuizByContentAndAuthor(): void
//     {
//         $this->db->exec("INSERT INTO users (username) VALUES ('Carol')");
//         $userId = $this->db->lastInsertId();

//         $quizStmt = $this->db->prepare("INSERT INTO quiz (title, description, difficulty, user_id, date, genre) VALUES (?, ?, ?, ?, ?, ?)");
//         $quizStmt->execute(['Quiz 2', 'Content test', 2, $userId, date('Y-m-d'), 'Science']);
//         $quizId = $this->db->lastInsertId();

//         $catId = $this->categoryModel->createCategory('Science');
//         $this->db->exec("INSERT INTO categorie_quiz (quiz_id, category_id) VALUES ($quizId, $catId)");

//         $result = $this->manager->searchQuizByContentAndAuthor('Content test', 'Carol');
//         $this->assertCount(1, $result);
//         $this->assertEquals('Quiz 2', $result[0]['title']);
//     }

//     /** Tests auteurs */
//     public function testGetNomAuteurAndInfo(): void
//     {
//         $this->db->exec("INSERT INTO users (username, email, description) VALUES ('Dave','dave@test.com','Author test')");
//         $userId = $this->db->lastInsertId();

//         $name = $this->manager->getNomAuteur($userId);
//         $this->assertEquals('Dave', $name);

//         $info = $this->manager->getAuthorInfo($userId);
//         $this->assertEquals('Dave', $info['username']);
//         $this->assertEquals('dave@test.com', $info['email']);
//     }

//     public function testUpdateAndDeleteAuthor(): void
//     {
//         $this->db->exec("INSERT INTO users (username, email, description) VALUES ('Eve','eve@test.com','Desc')");
//         $userId = $this->db->lastInsertId();

//         $updated = $this->manager->updateAuthor($userId, 'EveNew', 'eve2@test.com', 'Desc2');
//         $this->assertTrue($updated);

//         $deleted = $this->manager->deleteAuthor($userId);
//         $this->assertTrue($deleted);
//     }

//     /** Tests leçons */
//     public function testSearchLessonByTitleAndContent(): void
//     {
//         $this->db->exec("INSERT INTO users (username) VALUES ('Frank')");
//         $userId = $this->db->lastInsertId();

//         $this->db->exec("INSERT INTO lecon (title, description, date, quiz_id, user_id) VALUES ('Lesson 1','Content 1','2026-02-11',1,$userId)");

//         $lessonsTitle = $this->manager->searchLessonByTitle('Lesson 1');
//         $this->assertCount(1, $lessonsTitle);
//         $this->assertEquals('Lesson 1', $lessonsTitle[0]['title']);

//         $lessonsContent = $this->manager->searchLessonByContentAndAuthor('Content 1','Frank');
//         $this->assertCount(1, $lessonsContent);
//         $this->assertEquals('Lesson 1', $lessonsContent[0]['title']);
//     }

//     public function testGetCategoriesFromLesson(): void
//     {
//         $this->db->exec("INSERT INTO users (username) VALUES ('George')");
//         $userId = $this->db->lastInsertId();

//         $this->db->exec("INSERT INTO quiz (title, description, difficulty, user_id, date, genre) VALUES ('QuizLesson','Desc',1,$userId,'2026-02-11','Math')");
//         $quizId = $this->db->lastInsertId();

//         $catId = $this->categoryModel->createCategory('Algebra');
//         $this->db->exec("INSERT INTO categorie_quiz (quiz_id, category_id) VALUES ($quizId,$catId)");

//         $this->db->exec("INSERT INTO lecon (title, description, date, quiz_id, user_id) VALUES ('Lesson Algebra','Desc','2026-02-11',$quizId,$userId)");
//         $lessonId = $this->db->lastInsertId();

//         $categories = $this->manager->getCategoriesFromLesson($lessonId);
//         $this->assertCount(1, $categories);
//         $this->assertEquals('Algebra', $categories[0]['categorieName']);
//     }

//     protected function tearDown(): void
//     {
//         $this->db = null;
//         $this->manager = null;
//     }
// }

?>
