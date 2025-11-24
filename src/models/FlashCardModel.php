<?php
class FlashCardModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Récupère tous les IDs des questions d'une flashcard par l'ID du quiz
     *
     * @param int $quizId Identifiant du quiz
     * @return array Tableau des IDs des questions (vide si aucune)
     */
    public function getFlashCardById(int $quizId): array
    {
        $stmt = $this->db->prepare("SELECT id FROM carte WHERE quiz_id = ? ORDER BY numeroCarte ASC");
        $stmt->execute([$quizId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // retourne un tableau d'IDs
    }

    /**
     * Récupère les informations complètes d'une question par son ID
     *
     * @param int $id Identifiant de la question
     * @return array|null Tableau associatif des infos de la question ou null si non trouvé
     */
    public function getInfoFlashCardById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM carte WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }


    public function createFlashcard(int $nbCartes, int $user_id, string $title, string $desc, array $TAB_CONTENU){
        try{
            $this->db->beginTransaction();
            $newFlashcard = $this->insertFlashcard($user_id, $title, $desc);
            if (!$newFlashcard){
                throw new PDOException("erreur dans l\'insertion de la flashcard dans FlashcardModel.php/createFlashcard");
            }
            for ($i = 0; $i < $nbCartes ; $i++){
                $newCarte = $this->insertCarte($newFlashcard, $i, $TAB_CONTENU[$i]['question'], $TAB_CONTENU[$i]['reponse']);
                if (!$newFlashcard){
                    throw new PDOException("erreur dans l\'insertion d\'une carte dans FlashcardModel.php/createFlashcard");
                }
            }
            $this->db->commit();
        }catch (PDOException $e){
            error_log("Erreur création de flashcard : " . $e->getMessage());
            $this->db->rollBack();
            return false;
        }
    }

    public function insertFlashcard(int $user_id, string $title, string $desc){
        try{
            $newFlashcard = $this->db->prepare("INSERT INTO Quiz (user_id, title, description, difficulty, disponibilite, nbjaime, nbjaimepas, date, genre)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?); ");
            $newFlashcard->bindValue(1,$user_id);
            $newFlashcard->bindValue(2,$title);
            $newFlashcard->bindValue(3,$desc);
            $newFlashcard->bindValue(4,1);
            $newFlashcard->bindValue(5,'public');
            $newFlashcard->bindValue(6,0);
            $newFlashcard->bindValue(7,0);
            $newFlashcard->bindValue(8,date('Y-m-d'));
            $newFlashcard->bindValue(9, 'flashcard');

            $reussite = $newFlashcard->execute();
            if (!$reussite){
                return false;
            }else{
                return $this->db->lastInsertId();
            }

        }catch (PDOException $e){
            error_log("Erreur d'insertion de flashcard : " . $e->getMessage());
            return false;
        }
    }

    public function insertCarte(int $flashcard_id, int $numero, string $question, string $reponse){
        try{
            $newCarte = $this->db->prepare("INSERT INTO Carte (quiz_id, numeroCarte, question, reponse)
            VALUES (?, ?, ?, ?);");

            $newCarte->bindValue(1,$flashcard_id );
            $newCarte->bindValue(2,$numero );
            $newCarte->bindValue(3, $question);
            $newCarte->bindValue(4, $reponse);

            $reussite = $newCarte->execute();
            if (!$reussite){
                return false;
            }else{
                return $this->db->lastInsertId();
            }

        }catch (PDOException $e){
            error_log("Erreur d'insertion de carte : " . $e->getMessage());
            return false;
        }
    }
}
