<?php
class LogModel{
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Crée un utilisateur dans la base de données.
     * 
     * Cette méthode insère un utilisateur avec un username, un email et un mot de passe.
     * Le mot de passe est automatiquement haché avant insertion.
     * 
     * @param string $username Le nom d'utilisateur.
     * @param string $email L'adresse email de l'utilisateur.
     * @param string $password Le mot de passe de l'utilisateur.
     * @return bool Vrai si l'utilisateur a été inséré avec succès, faux sinon.
     */
    public function createUser(string $username, string $email, string $password) : bool{
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        return $stmt->execute([$username, $email, $hashedPassword]);
    }

    public function getUserByEmail(string $email): array|false {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie qu'un mot de passe correspond à l'utilisateur donné.
     * 
     * Cette méthode récupère l'utilisateur associé à l'email fourni,
     * et vérifie si le mot de passe fourni correspond au mot de passe haché stocké en base.
     * 
     * @param string $email L'adresse email de l'utilisateur.
     * @param string $password Le mot de passe à vérifier.
     * @return bool Vrai si le mot de passe correspond, faux sinon ou si l'utilisateur n'existe pas.
     */
    public function verifyPassword(string $email, string $password): bool {
        $user = $this->getUserByEmail($email);
        if (!$user) return false;
        return password_verify($password, $user['password']);
    }
}
?>