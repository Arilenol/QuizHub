<?php
use PHPUnit\Framework\TestCase;

require_once 'src\models\HomeModel.php';

class HomeModelTest extends TestCase
{
    private $pdo;
    private HomeModel $model;

    protected function setUp(): void
    {
        // Création d'un mock PDO
        $this->pdo = $this->createMock(PDO::class);
        $this->model = new HomeModel($this->pdo);
    }

    public function testGetByIdReturnsQuiz(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([1])
             ->willReturn(true);
        $stmt->expects($this->once())
             ->method('fetch')
             ->with(PDO::FETCH_ASSOC)
             ->willReturn(['id' => 1, 'title' => 'Test Quiz']);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->with("SELECT * FROM quiz WHERE id = ?")
                  ->willReturn($stmt);

        $result = $this->model->getById(1);
        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Test Quiz', $result['title']);
    }

    public function testGetAllInfoTransformsCategories(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('fetchAll')
             ->with(PDO::FETCH_ASSOC)
             ->willReturn([
                 [
                     'id' => 1,
                     'categories' => 'Cat1,Cat2',
                     'title' => 'Quiz 1',
                     'user_name' => 'User1',
                 ]
             ]);

        $this->pdo->expects($this->once())
                  ->method('query')
                  ->willReturn($stmt);

        $results = $this->model->getAllInfo();
        $this->assertIsArray($results);
        $this->assertIsArray($results[0]['categories']);
        $this->assertEquals(['Cat1','Cat2'], $results[0]['categories']);
    }

    public function testCreateInstanceExecutesInsert(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->willReturn(true);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->willReturn($stmt);

        $result = $this->model->createInstance(5);
        $this->assertTrue($result);
    }

    public function testCheckIfNotInstanceReturnsTrueWhenEmpty(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([5])
             ->willReturn(true);
        $stmt->expects($this->once())
             ->method('fetchColumn')
             ->willReturn(false);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->willReturn($stmt);

        $result = $this->model->checkIfNotInstance(5);
        $this->assertTrue($result);
    }

    public function testGetCurrentStreakReturnsInteger(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with([5])
             ->willReturn(true);
        $stmt->expects($this->once())
             ->method('fetchColumn')
             ->willReturn('3');

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->willReturn($stmt);

        $streak = $this->model->getCurrentStreak(5);
        $this->assertIsInt($streak);
        $this->assertEquals(3, $streak);
    }

    public function testIncrementStreakExecutesUpdate(): void
    {
        $selectStmt = $this->createMock(PDOStatement::class);
        $selectStmt->expects($this->once())->method('execute')->with([5])->willReturn(true);
        $selectStmt->expects($this->once())->method('fetchAll')->willReturn([['current_streak' => 2]]);

        $updateStmt = $this->createMock(PDOStatement::class);
        $updateStmt->expects($this->once())->method('execute')->with([5])->willReturn(true);

        $this->pdo->expects($this->exactly(2))
                  ->method('prepare')
                  ->willReturnOnConsecutiveCalls($selectStmt, $updateStmt);

        $this->model->incrementStreak(5);
    }

    public function testGetAllCreationsByFriendsTransformsCategories(): void
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())->method('execute')->with(['me' => 10])->willReturn(true);
        $stmt->expects($this->once())->method('fetchAll')->willReturn([
            ['id' => 1, 'categories' => 'Cat1,Cat2', 'title' => 'Quiz Ami', 'user_name' => 'Friend1']
        ]);

        $this->pdo->expects($this->once())->method('prepare')->willReturn($stmt);

        $results = $this->model->getAllCreationsByFriends(10);
        $this->assertIsArray($results);
        $this->assertEquals(['Cat1','Cat2'], $results[0]['categories']);
    }
}
