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


    /**
     * Crée une flashcard complète avec toutes ses cartes et restrictions d'amis.
     *
     * Cette méthode effectue une transaction complète qui crée une flashcard, insère
     * toutes ses cartes (question/réponse), et les restrictions d'amis si applicable.
     * En cas d'erreur, la transaction est annulée et aucune donnée n'est conservée.
     *
     * @param int    $nbCartes        Nombre total de cartes dans la flashcard.
     * @param int    $user_id         Identifiant de l'utilisateur créateur de la flashcard.
     * @param string $title           Titre de la flashcard.
     * @param string $desc            Description de la flashcard.
     * @param array  $TAB_CONTENU     Tableau contenant les questions et réponses de chaque carte.
     * @param array  $TAB_AMI_CHOISI  Tableau des IDs d'amis autorisés à accéder (si dispo='ami').
     * @param string $disponibilite   Type de disponibilité ('public', 'ami', etc.).
     *
     * @return bool  true si la création est réussie, false en cas d'erreur.
     */
    public function createFlashcard(int $nbCartes, int $user_id, string $title, string $desc, array $TAB_CONTENU, array $TAB_AMI_CHOISI, array $TAB_CATEGORIE_CHOISI, string $disponibilite){
        try{
            $this->db->beginTransaction();
            $newFlashcard = $this->insertFlashcard($user_id, $title, $desc);
            if (!$newFlashcard){
                throw new PDOException("erreur dans l\'insertion de la flashcard dans FlashcardModel.php/createFlashcard");
            }
            for ($i = 0; $i < $nbCartes ; $i++){
                $newCarte = $this->insertCarte($newFlashcard, $i+1, $TAB_CONTENU[$i]['question'], $TAB_CONTENU[$i]['reponse']);
                if (!$newFlashcard){
                    throw new PDOException("erreur dans l\'insertion d\'une carte dans FlashcardModel.php/createFlashcard");
                }
            }
            if ($disponibilite == 'ami'){
                foreach($TAB_AMI_CHOISI as $ami){
                    $newAmiDispo = $this->insertAmiDispo($newFlashcard, (int)$ami);
                    if (!$newAmiDispo) {
                        throw new PDOException("erreur dans l\'insertion des amis dans QuizModel.php/createQuiz");
                    }
                }
            }
            foreach($TAB_CATEGORIE_CHOISI as $categorie){
                $newCategorie = $this->insertQuizCategorie($newFlashcard, (int)$categorie);
                if (!$newCategorie) {
                    throw new PDOException("erreur dans l\'insertion des catégories dans FlashcardModel.php/createFlashcard");
                }
            }
            $this->db->commit();
            return true;
        }catch (PDOException $e){
            error_log("Erreur création de flashcard : " . $e->getMessage());
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Insère une nouvelle flashcard dans la base de données.
     *
     * Cette méthode crée un enregistrement flashcard (via la table Quiz avec genre='flashcard')
     * avec les informations fournies. Les paramètres par défaut incluent : difficulté = 1,
     * disponibilité = 'public', nbjaime = 0, nbjaimepas = 0.
     *
     * @param int    $user_id  Identifiant de l'utilisateur créateur de la flashcard.
     * @param string $title    Titre de la flashcard.
     * @param string $desc     Description de la flashcard.
     *
     * @return int|false  Retourne l'ID de la flashcard insérée, ou false en cas d'erreur.
     */
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

    /**
     * Insère une restriction d'accès à la flashcard pour un ami spécifique.
     *
     * Cette méthode crée une association entre une flashcard et un ami,
     * indiquant que l'ami peut accéder à la flashcard.
     *
     * @param int $quiz_id  Identifiant de la flashcard.
     * @param int $ami_id   Identifiant de l'ami autorisé à accéder à la flashcard.
     *
     * @return int|false  Retourne l'ID de l'ami inséré, ou false en cas d'erreur.
     */
    public function insertAmiDispo(int $quiz_id, int $ami_id)
    {
        try {
            $newAmiDispo = $this->db->prepare("INSERT INTO amiDisponibilite(quiz_id, ami_id) VALUES (?, ?);");
            $newAmiDispo->bindValue(1, $quiz_id);
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
     * Insère une nouvelle carte dans une flashcard.
     *
     * Cette méthode crée un enregistrement carte avec une question et sa réponse,
     * associé à une flashcard donnée, avec un numéro d'ordre.
     *
     * @param int    $flashcard_id  Identifiant de la flashcard à laquelle appartient la carte.
     * @param int    $numero        Numéro d'ordre de la carte dans la flashcard.
     * @param string $question      Texte de la question sur la carte.
     * @param string $reponse       Texte de la réponse sur la carte.
     *
     * @return int|false  Retourne l'ID de la carte insérée, ou false en cas d'erreur.
     */
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

    /**
     * Insère une association entre un quiz et une catégorie.
     *
     * Cette méthode crée un lien dans la table `categorie_quiz` pour indiquer
     * qu'une catégorie est associée à un quiz spécifique.
     *
     * @param int $quiz_id      Identifiant du quiz.
     * @param int $categorie_id Identifiant de la catégorie à associer.
     *
     * @return int|false  Retourne l'ID de la catégorie insérée, ou false en cas d'erreur.
     */
    public function insertQuizCategorie(int $quiz_id, int $categorie_id)
    {
        try {
            $newQuizCategorie = $this->db->prepare("INSERT INTO categorie_quiz(category_id, quiz_id) VALUES (?, ?);");
            $newQuizCategorie->bindValue(1, $categorie_id);
            $newQuizCategorie->bindValue(2, $quiz_id);

            $reussite = $newQuizCategorie->execute();
            if ($reussite === false) {
                return false;
            } else {
                return $categorie_id;
            }
        } catch (PDOException $e) {
            error_log("Erreur d'insertion de quiz categorie : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère la liste des catégories disponibles.
     *
     * Cette méthode retourne un tableau associatif contenant les catégories
     * (id et CategorieName) présentes dans la table `categories`.
     *
     * @return array|false  Tableau associatif des catégories, ou false en cas d'erreur.
     */
    public function getAllCategories(): mixed{
        try{
            $sql = $this->db->prepare("SELECT DISTINCT id,CategorieName FROM categories;");
            $sql->execute();
            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        }catch(PDOException $e){
            die("Fetching categories failed: " . $e->getMessage());
        }catch(Exception $e){
            die("Error: " . $e->getMessage());
        }
    }
}
