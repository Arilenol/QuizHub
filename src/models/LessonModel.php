<?php
class LessonModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Récupère une leçon par son ID
     *
     * @param int $id Identifiant de la leçon
     * @return array|false Tableau associatif de la leçon, ou false si non trouvé
     */
    public function getLesson($id) : array|false {
        $stmt = $this->db->prepare("SELECT * FROM lecon WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Récupère les différentes parties de la leçon par son ID
     *
     * @param int $id Identifiant de la leçon
     * @return array|false Tableau associatif des parties de la leçon, ou false si non trouvé
     */
    public function getPart(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT title, content,id
            FROM partie
            WHERE lecon_id = ?
            ORDER BY numeroPartie ASC
        ");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les exemples associés à une partie de leçon.
     *
     * Cette méthode retourne tous les exemples liés à une partie spécifique,
     * classés par numéro d'ordre.
     *
     * @param int $id Identifiant de la partie
     * @return array|false Tableau associatif des exemples, ou false si non trouvé
     */
    public function getExemple(int $id): array|false {
        $stmt = $this->db->prepare("
            SELECT consigne, reponse, numeroExemple, partie_id
            FROM exemple
            WHERE partie_id = ?
            ORDER BY numeroExemple ASC
        ");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée une nouvelle leçon complète avec toutes ses parties, exemples et restrictions d'amis.
     *
     * Cette méthode effectue une transaction complète qui crée une leçon, insère
     * toutes ses parties, tous ses exemples, et les restrictions d'amis si applicable.
     * En cas d'erreur, la transaction est annulée et aucune donnée n'est conservée.
     *
     * @param int    $id              Identifiant de l'utilisateur créateur de la leçon.
     * @param string $title           Titre de la leçon.
     * @param string $description     Description de la leçon.
     * @param int    $nbParts         Nombre total de parties dans la leçon.
     * @param array  $nbExemple       Tableau spécifiant le nombre d'exemples pour chaque partie.
     * @param array  $TAB_CONTENU     Tableau contenant toutes les informations des parties et exemples.
     * @param array  $TAB_AMI_CHOISI  Tableau des IDs d'amis autorisés à accéder (si dispo='ami').
     * @param string $disponibilite   Type de disponibilité ('public', 'ami', etc.).
     * @param int|null $quizSelected  Identifiant du quiz associé à la leçon (optionnel).
     *
     * @return bool  true si la création est réussie, false en cas d'erreur.
     */
    public function createLesson(int $id, String $title, String $description, int $nbParts, array $nbExemple, array $TAB_CONTENU, array $TAB_AMI_CHOISI, string $disponibilite, ?int $quizSelected): int|false {
        try {
            $this->db->beginTransaction();

            $newLesson = $this->insertLesson($id, $quizSelected, $title, $description);
            if (!$newLesson){
                throw new PDOException("erreur dans l\'insertion de la leçon dans LessonModel.php/createLesson");
            }
            for ($i = 0; $i < $nbParts ; $i++){

                $newPart = $this->insertPart($i, $newLesson, $TAB_CONTENU[$i]['name'], $TAB_CONTENU[$i]['content']);
                if (!$newPart){
                    throw new PDOException('erreur dans l\'insertion de la partie '.$i.' dans LessonModel.php/createLesson');
                }
                for ($k = 0; $k < $nbExemple[$i]; $k++){
                    $newExample = $this->insertExample($k, $newPart, $TAB_CONTENU[$i]['exemples'][$k]['consigne'], $TAB_CONTENU[$i]['exemples'][$k]['reponse']);
                    if (!$newExample){
                        throw new PDOException('erreur dans l\'insertion de l\'exemple '.$k.' de la partie '.$i.' dans LessonModel.php/createLesson');
                    }

                }

            }
            if ($disponibilite == 'ami'){
                foreach($TAB_AMI_CHOISI as $ami){
                    $newAmiDispo = $this->insertAmiDispo($newLesson, (int)$ami);
                    if (!$newAmiDispo) {
                        throw new PDOException("erreur dans l\'insertion des amis dans QuizModel.php/createQuiz");
                    }
                }
            }
            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            error_log("Erreur création leçon : " . $e->getMessage());
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Insère une restriction d'accès à la leçon pour un ami spécifique.
     *
     * Cette méthode crée une association entre une leçon et un ami,
     * indiquant que l'ami peut accéder à la leçon.
     *
     * @param int $lesson_id  Identifiant de la leçon.
     * @param int $ami_id     Identifiant de l'ami autorisé à accéder à la leçon.
     *
     * @return int|false  Retourne l'ID de l'ami inséré, ou false en cas d'erreur.
     */
    public function insertAmiDispo(int $lesson_id, int $ami_id)
    {
        try {
            $newAmiDispo = $this->db->prepare("INSERT INTO amiDisponibilite(lesson_id, ami_id) VALUES (?, ?);");
            $newAmiDispo->bindValue(1, $lesson_id);
            $newAmiDispo->bindValue(2, $ami_id);

            $reussite = $newAmiDispo->execute();
            if ($reussite === false) {
                return false;
            } else {
                return $ami_id;
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion d'ami dispo : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les quizzes créés par un utilisateur.
     *
     * Cette méthode retourne la liste des quizzes associés à un auteur spécifié,
     * incluant leur ID et titre.
     *
     * @param int $authorId  Identifiant de l'utilisateur auteur.
     *
     * @return array|false  Tableau associatif des quizzes (id, title), ou false en cas d'erreur.
     */
    public function getQuizByAuthor(int $authorId){
        $sql = $this->db->prepare("SELECT id,title FROM quiz WHERE user_id = ? ;");

        $sql->bindParam(1,$authorId);
        $reussite = $sql->execute();
        if($reussite){
            return $sql->fetchAll(PDO::FETCH_ASSOC);
        }else{
            return false;
        }
        
    }

    /**
     * Insère une nouvelle leçon dans la base de données.
     *
     * Cette méthode crée un enregistrement leçon avec les informations fournies,
     * incluant potentiellement un quiz associé.
     *
     * @param int    $user_id     Identifiant de l'utilisateur créateur de la leçon.
     * @param int|null $quizSelected Identifiant du quiz associé à la leçon (optionnel).
     * @param string $title       Titre de la leçon.
     * @param string $description Description de la leçon.
     *
     * @return int|false  Retourne l'ID de la leçon insérée, ou false en cas d'erreur.
     */
    public function insertLesson(int $user_id, ?int $quizSelected, String $title, String $description){
        try{
            $newLesson = $this->db->prepare("INSERT INTO Lecon (user_id, quiz_id, title, description) VALUES (?, ?, ?, ?);");
            $newLesson->bindValue(1, $user_id);
            $newLesson->bindValue(2, $quizSelected);
            $newLesson->bindValue(3, $title);
            $newLesson->bindValue(4, $description);
            $reussite = $newLesson->execute();
            if (!$reussite){
                return false;
            }else{
                return $this->db->lastInsertId();
            }
        }catch (PDOException $e){
            error_log("Erreur d'insertion de leçon : " . $e->getMessage());
            return false;
        }
        
    }

    /**
     * Insère une nouvelle partie pour une leçon.
     *
     * Cette méthode crée un enregistrement partie avec un numéro d'ordre,
     * un titre et du contenu associé à une leçon.
     *
     * @param int    $numeroPartie  Numéro d'ordre de la partie au sein de la leçon.
     * @param int    $lecon_id      Identifiant de la leçon à laquelle appartient la partie.
     * @param string $title         Titre de la partie.
     * @param string $content       Contenu textuel de la partie.
     *
     * @return int|false  Retourne l'ID de la partie insérée, ou false en cas d'erreur.
     */
    public function insertPart(int $numeroPartie, int $lecon_id, String $title, string $content){
        try{
            $newPart = $this->db->prepare("INSERT INTO Partie (numeroPartie, lecon_id, title, content) VALUES (?, ?, ?, ?);");
            $newPart->bindValue(1, $numeroPartie);
            $newPart->bindValue(2, $lecon_id);
            $newPart->bindValue(3, $title);
            $newPart->bindValue(4, $content);

            $reussite = $newPart->execute();

            if ($reussite){
                return $this->db->lastInsertId();
            }else{
                return false;
            }
        }catch(PDOException $e){
            error_log("Erreur d'insertion de partie : ".$e->getMessage());
            return false;
        }
    }

    /**
     * Insère un nouvel exemple pour une partie de leçon.
     *
     * Cette méthode crée un enregistrement exemple avec une consigne et sa réponse,
     * associé à une partie donnée, avec un numéro d'ordre.
     *
     * @param int    $numeroExemple  Numéro d'ordre de l'exemple au sein de la partie.
     * @param int    $partie_id      Identifiant de la partie à laquelle appartient l'exemple.
     * @param string $consigne       Texte de la consigne de l'exemple.
     * @param string $reponse        Texte de la réponse à la consigne.
     *
     * @return int|false  Retourne l'ID de l'exemple inséré, ou false en cas d'erreur.
     */
    public function insertExample(int $numeroExemple, int $partie_id, string $consigne, string $reponse){
        try{
            $newExample = $this->db->prepare("INSERT INTO Exemple (numeroexemple, partie_id, consigne, reponse) VALUES (?, ?, ?, ?);");
            $newExample->bindValue(1, $numeroExemple);
            $newExample->bindValue(2, $partie_id);
            $newExample->bindValue(3, $consigne);
            $newExample->bindValue(4, $reponse);

            $reussite = $newExample->execute();

            if ($reussite){
                return $this->db->lastInsertId();
            }else{
                return false;
            }
        }catch(PDOException $e){
            error_log("Erreur d'insertion d'exemple : ".$e->getMessage());
            return false;
        }
    }

    /**
     * Récupère tous les amis d'un utilisateur.
     *
     * Cette méthode retourne une liste de tous les amis connectés à l'utilisateur
     * spécifié, en incluant leur ID et leur nom d'utilisateur.
     *
     * @param int $user_id  Identifiant de l'utilisateur pour lequel récupérer les amis.
     *
     * @return array  Tableau de tableaux associatifs contenant 'ami_id' et 'username' pour chaque ami.
     */
    public function getAmis(int $user_id){
        $amis = $this->db->prepare("SELECT 
                                CASE 
                                WHEN user1_id = ? THEN user2_id
                                ELSE user1_id
                                END AS ami_id , username
                                FROM amis JOIN users ON ami_id = users.id 
                                WHERE ? = user1_id OR ? = user2_id;");
        $amis->bindvalue(1,$user_id);
        $amis->bindvalue(2,$user_id);
        $amis->bindvalue(3,$user_id);

        $amis->execute();

        $result = $amis->fetchAll(PDO::FETCH_ASSOC);
        return $result;
        
    }
}
?>
