<?php
class QuizModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Retourne le nombre total de questions d’un quiz.
     *
     * Cette méthode compte toutes les entrées de la table `question` associées
     * à un quiz donné via son identifiant. Si aucune question n'existe,
     * elle renvoie 0.
     *
     * @param int $idQuiz  Identifiant du quiz dont on souhaite connaître le nombre de questions.
     *
     * @return int  Nombre total de questions du quiz. 0 si aucune question n’est trouvée.
     */

    public function getMaxNbQuestion(int $idQuiz): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(id) AS max_questions FROM question WHERE quiz_id = ?");
        $stmt->execute([$idQuiz]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int) $result['max_questions'] : 0;
    }


    /**
     * Récupère une question d'un quiz selon son numéro d'ordre.
     *
     * Cette méthode permet de récupérer une question spécifique associée
     * à un quiz, en utilisant l'ID du quiz ainsi que le numéro de question
     * (numeroquiz). Si le numéro n'est pas fourni, la méthode récupère par défaut
     * la première question du quiz.
     *
     * @param int      $idQuiz      Identifiant du quiz dont on veut récupérer la question.
     * @param int|null $idQuestion  Numéro de la question dans le quiz (1 par défaut).
     *
     * @return array|false Retourne un tableau associatif contenant la question,
     *                     ou false si aucune question ne correspond.
     */

    public function getQuestion(int $idQuiz, ?int $idQuestion = 1): array|false
    {
        $stmt = $this->db->prepare("
        SELECT *
        FROM question
        WHERE quiz_id = ? AND numeroquiz = ?
    ");
        $stmt->execute([$idQuiz, $idQuestion]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    /**
     * Récupère toutes les réponses associées à une question.
     *
     * Cette méthode retourne l'ensemble des réponses liées à une question
     * spécifique, identifiée par son ID. Chaque réponse est renvoyée sous
     * forme de tableau associatif contenant ses informations (texte,
     * validité, identifiant, etc.).
     *
     * @param int $idQuestion  Identifiant de la question dont on veut obtenir les réponses.
     *
     * @return array|false  Retourne un tableau contenant toutes les réponses sous forme
     *                      de tableaux associatifs, ou false si aucune réponse n'est trouvée.
     */

    public function getReponses(int $idQuestion): array|false
    {
        $stmt = $this->db->prepare("SELECT * FROM reponse WHERE question_id = ?");
        $stmt->execute([$idQuestion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
