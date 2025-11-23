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
     * Récupère les différentes parties de la leçon par son ID
     *
     * @param int $id Identifiant de la leçon
     * @return array|false Tableau associatif des parties de la leçon, ou false si non trouvé
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
     * Crée une nouvelle leçon, toutes ces parties associées et leurs exemples
     *
     * @param int $id nombre correspondant à l'id du créateur
     * @param string $title Titre de la leçon
     * @param string $description Description de la leçon
     * @param int $nbParts nombre de partie dans la leçon
     * @param array $nbExemple tableau qui spécifie le nombree d'exemple pour chaque partie
     * @param array $TAB_CONTENU contient toutes les informations de toutes les parties et tous les exemple sous forme d'un tableau associatif à 2 dimensions
     * @param int $quizSelected correspond au quiz associé à la leçon (potentiellement null)
     * @return int|false Retourne l'ID de la leçon créée, ou false en cas d’échec
     */
    public function createLesson(int $id, String $title, String $description, int $nbParts, array $nbExemple, array $TAB_CONTENU, ?int $quizSelected): int|false {
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

            $this->db->commit();
            return $newLesson;

        } catch (PDOException $e) {
            error_log("Erreur création leçon : " . $e->getMessage());
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * prend tous les quizs d'un auteur
     * @param int $authorId
     * @return array|false tableau associatif des quizs associés à l'id en paramètre ou false si l'éxécution est un échec
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
     * @param int $user_id identifiant de l'utilisateur qui a créé la leçon
     * @param int $quizSelected identifiant du quiz associé à la leçon (potentiellement null)
     * @param string $title titre de la leçon
     * @param string $description description de la leçon
     * @return int|false retourne l'identifiant de la leçon créée ou false si l'éxécution est un échec 
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
     * @param int $numeroPartie numero de la partie
     * @param int $lecon_id identifiant du de la leçon associé
     * @param string $title titre de la partie
     * @param string $content contenu de la partie
     * @return int|false retourne l'identifiant de la partie crréée ou false si l'éxécution est un échec 
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
     * 
     * @param int $numeroExemple numero de l'exemple au sein de la partie
     * @param int $partie_id identifiant de la partie associée
     * @param string $consigne la consigne de l'exemple
     * @param string $reponse la reponse à la consigne de l'exemple
     * @return int|false retourne l'identifiant de l'exemple
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
}
?>
