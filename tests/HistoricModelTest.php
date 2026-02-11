<?php
use PHPUnit\Framework\TestCase;

require_once 'src/models/HistoricModel.php';

class HistoricModelTest extends TestCase
{
    private $model;
    private $db;

    protected function setUp(): void
    {
        // Créer une base SQLite en mémoire
        $this->db = new PDO('sqlite::memory:');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Créer la table nécessaire
        $this->db->exec("
            CREATE TABLE resultat (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                quiz_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                score INTEGER,
                tempsPris INTEGER
            );
        ");

        $this->model = new HistoricModel($this->db);
    }

    public function testSaveHistoricInsertsNew(): void
    {
        $quizId = 1;
        $userId = 42;

        $result = $this->model->saveHistoric($quizId, $userId);
        $this->assertTrue($result, "La fonction doit retourner true après insertion.");

        $stmt = $this->db->prepare("SELECT * FROM resultat WHERE quiz_id = ? AND user_id = ?");
        $stmt->execute([$quizId, $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($row, "L'enregistrement doit exister dans la table resultat.");
        $this->assertNull($row['score'], "Le score doit être NULL.");
        $this->assertNull($row['tempsPris'], "Le temps doit être NULL.");
    }

    public function testSaveHistoricReplacesExisting(): void
    {
        $quizId = 1;
        $userId = 42;

        // Insérer un ancien résultat
        $stmt = $this->db->prepare("INSERT INTO resultat (quiz_id, user_id, score, tempsPris) VALUES (?,?,10,120)");
        $stmt->execute([$quizId, $userId]);

        // Vérifier qu'il existe
        $stmtCheck = $this->db->prepare("SELECT * FROM resultat WHERE quiz_id = ? AND user_id = ?");
        $stmtCheck->execute([$quizId, $userId]);
        $oldRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(10, $oldRow['score']);
        $this->assertEquals(120, $oldRow['tempsPris']);

        // Appeler saveHistoric
        $result = $this->model->saveHistoric($quizId, $userId);
        $this->assertTrue($result, "La fonction doit retourner true après remplacement.");

        // Vérifier qu'il n'y a plus que le nouvel enregistrement
        $stmtCheck->execute([$quizId, $userId]);
        $newRow = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($newRow, "Le nouvel enregistrement doit exister.");
        $this->assertNull($newRow['score'], "Le score doit être réinitialisé à NULL.");
        $this->assertNull($newRow['tempsPris'], "Le temps doit être réinitialisé à NULL.");
    }

    protected function tearDown(): void
    {
        $this->db = null;
        $this->model = null;
    }
}
