<?php
class ProfileModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Récupère un utilisateur via son adresse email.
     *
     * Cette méthode interroge la base de données afin de retrouver
     * les informations d’un utilisateur à partir de son email.  
     * Si un utilisateur correspond, ses données sont retournées sous forme de tableau
     * associatif. Sinon, la méthode retourne false.
     *
     * @param string $email L’adresse email de l’utilisateur recherché.
     * 
     * @return array|false Un tableau associatif contenant les informations de l’utilisateur
     *                     (id, username, email, password, etc.) ou false si aucun
     *                     utilisateur ne correspond.
     */
    public function getIdUserFromEmail(string $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Compte le nombre de créations (quiz) appartenant à un utilisateur.
     *
     * Cette méthode exécute une requête SQL afin de déterminer combien de
     * quiz ont été créés par un utilisateur spécifique en fonction de son ID.
     *
     * @param int $id L'identifiant de l'utilisateur.
     *
     * @return int Le nombre total de quiz créés par l'utilisateur.
     */
    public function getCreationsNumber(int $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(id) AS total FROM quiz WHERE user_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['total'];
    }

    /**
     * Compte le nombre de parties jouées (quiz)
     *
     * Cette méthode exécute une requête SQL afin de déterminer combien de
     * quiz ont été jouées par un utilisateur spécifique en fonction de son ID.
     *
     * @param int $id L'identifiant de l'utilisateur.
     *
     * @return int Le nombre total de quiz jouées par l'utilisateur.
     */
    public function getGamesNumber(int $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(id) AS resultat FROM quiz WHERE user_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['resultat'];
    }
}
