<?php
class FlashcardModel
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
    public function getFlashcardById(int $quizId): array
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
    public function getInfoFlashcardById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM carte WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Retourne le nombre total de questions d’une Flashcard.
     *
     * Cette méthode compte toutes les entrées de la table `carte` associées
     * à une flashcard donné via son identifiant. Si aucune question n'existe,
     * elle renvoie 0.
     *
     * @param int $id  Identifiant de la flashcard dont on souhaite connaître le nombre de questions.
     *
     * @return int  Nombre total de questions du quiz. 0 si aucune question n’est trouvée.
     */

    public function getMaxNbQuestion(int $id): int
    {
        $stmt = $this->db->prepare("
        SELECT MAX(numeroCarte) AS maxi
        FROM carte
        WHERE quiz_id = ?
    ");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        var_dump($id);
        var_dump($row['maxi']);
        return intval($row['maxi']);
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
    public function createFlashcard(int $nbCartes, int $user_id, string $title, string $desc, array $TAB_CONTENU, array $TAB_AMI_CHOISI, array $TAB_CATEGORIE_CHOISI, string $disponibilite)
    {
        try {
            $this->db->beginTransaction();
            $newFlashcard = $this->insertFlashcard($user_id, $title, $desc, $disponibilite);
            if (!$newFlashcard) {
                throw new PDOException("erreur dans l\'insertion de la flashcard dans FlashcardModel.php/createFlashcard");
            }
            for ($i = 0; $i < $nbCartes; $i++) {
                $newCarte = $this->insertCarte($newFlashcard, $i + 1, $TAB_CONTENU[$i]['question'], $TAB_CONTENU[$i]['reponse']);
                if (!$newFlashcard) {
                    throw new PDOException("erreur dans l\'insertion d\'une carte dans FlashcardModel.php/createFlashcard");
                }
            }
            if ($disponibilite == 'ami') {
                $amisSelectionnes = $this->normalizeSelectedFriends($user_id, $TAB_AMI_CHOISI);
                foreach ($amisSelectionnes as $ami) {
                    $newAmiDispo = $this->insertAmiDispo($newFlashcard, (int)$ami);
                    if (!$newAmiDispo) {
                        throw new PDOException("erreur dans l\'insertion des amis dans QuizModel.php/createQuiz");
                    }
                }
            }
            foreach ($TAB_CATEGORIE_CHOISI as $categorie) {
                $newCategorie = $this->insertQuizCategorie($newFlashcard, (int)$categorie);
                if (!$newCategorie) {
                    throw new PDOException("erreur dans l\'insertion des catégories dans FlashcardModel.php/createFlashcard");
                }
            }
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
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
    public function insertFlashcard(int $user_id, string $title, string $desc, string $disponibilite)
    {
        try {
            $newFlashcard = $this->db->prepare("INSERT INTO Quiz (user_id, title, description, difficulty, disponibilite, date, genre)
            VALUES (?, ?, ?, ?, ?, ?, ?); ");
            $newFlashcard->bindValue(1, $user_id);
            $newFlashcard->bindValue(2, $title);
            $newFlashcard->bindValue(3, $desc);
            $newFlashcard->bindValue(4, 1);
            $newFlashcard->bindValue(5, $disponibilite);
            $newFlashcard->bindValue(6, date('Y-m-d'));
            $newFlashcard->bindValue(7, 'flashcard');

            $reussite = $newFlashcard->execute();
            if (!$reussite) {
                return false;
            } else {
                return $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
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
    public function insertCarte(int $flashcard_id, int $numero, string $question, string $reponse)
    {
        try {
            $newCarte = $this->db->prepare("INSERT INTO Carte (quiz_id, numeroCarte, question, reponse)
            VALUES (?, ?, ?, ?);");

            $newCarte->bindValue(1, $flashcard_id);
            $newCarte->bindValue(2, $numero);
            $newCarte->bindValue(3, $question);
            $newCarte->bindValue(4, $reponse);

            $reussite = $newCarte->execute();
            if (!$reussite) {
                return false;
            } else {
                return $this->db->lastInsertId();
            }
        } catch (PDOException $e) {
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
    public function getAmis(int $user_id)
    {
        $amis = $this->db->prepare("SELECT 
                                CASE 
                                WHEN user1_id = ? THEN user2_id
                                ELSE user1_id
                                END AS ami_id , username
                                FROM amis JOIN users ON ami_id = users.id 
                                WHERE ? = user1_id OR ? = user2_id;");
        $amis->bindvalue(1, $user_id);
        $amis->bindvalue(2, $user_id);
        $amis->bindvalue(3, $user_id);

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
     * Récupère toutes les catégories disponibles.
     *
     * @return array Tableau associatif des catégories (chaque élément contient 'id' et 'CategorieName').
     * @throws Exception En cas d'erreur de récupération.
     */
    public function getAllCategories(): mixed
    {
        try {
            $sql = $this->db->prepare("SELECT DISTINCT id,CategorieName FROM categories;");
            $sql->execute();
            $categories = $sql->fetchAll(PDO::FETCH_ASSOC);
            return $categories;
        } catch (PDOException $e) {
            die("Fetching categories failed: " . $e->getMessage());
        } catch (Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Retourne le nombre de cartes pour une flashcard donnée.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @return int Nombre total de cartes (0 si aucune).
     */
    public function getFlashcardSize(int $idFlashcard): int
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) AS totalQuestions FROM Carte WHERE quiz_id = ?;");
            $stmt->execute([$idFlashcard]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return intval($row['totalQuestions']);
        } catch (PDOException $e) {
            die("Fetching quiz size failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère les informations sommaires d'une flashcard.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @return array|null Tableau associatif contenant 'title', 'description', 'disponibilite', 'genre' ou null si non trouvé.
     */
    public function getFlashcardInfos(int $idFlashcard)
    {
        try {
            $quiz = $this->db->prepare("SELECT title, description, disponibilite, genre FROM quiz WHERE id = ?;");
            $quiz->bindvalue(1, $idFlashcard);
            $quiz->execute();
            return $quiz->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Fetching quiz infos failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère toutes les cartes (question/réponse) d'une flashcard.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @return array Tableau de tableaux associatifs contenant 'id', 'question' et 'reponse'.
     */
    public function getCardsFromFlashcard(int $idFlashcard)
    {
        try {
            $cartes = $this->db->prepare("SELECT id, question, reponse FROM Carte WHERE quiz_id = ? ORDER BY numeroCarte ASC;");
            $cartes->bindValue(1, $idFlashcard);
            $cartes->execute();
            $TAB_CARD = $cartes->fetchAll(PDO::FETCH_ASSOC);
            return $TAB_CARD;
        } catch (PDOException $e) {
            die("Fetching questions and answers from quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère les catégories associées à une flashcard.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @return array Tableau associatif des catégories (chaque élément contient 'id' et 'categorieName').
     */
    public function getCategoriesFromFlashcard(int $idFlashcard)
    {
        try {
            $categories = $this->db->prepare("SELECT categories.id, categories.categorieName FROM categories 
            INNER JOIN categorie_quiz ON categories.id = categorie_quiz.category_id WHERE categorie_quiz.quiz_id = ?;");
            $categories->bindValue(1, $idFlashcard);
            $categories->execute();
            return $categories->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Fetching categories from quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère l'ID de l'utilisateur propriétaire d'une flashcard.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @return int|false L'ID de l'utilisateur si trouvé, ou false sinon.
     */
    public function getUserIdFromFlashcard(int $idFlashcard): int
    {
        try {
            $quiz = $this->db->prepare("SELECT user_id FROM quiz WHERE id = ?;");
            $quiz->bindvalue(1, $idFlashcard);
            $quiz->execute();
            $result = $quiz->fetch(PDO::FETCH_ASSOC);
            if (!empty($result)) {
                return (int)$result['user_id'];
            } else {
                return false;
            }
        } catch (PDOException $e) {
            die("Fetching user ID from quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour les catégories associées à une flashcard.
     *
     * Supprime d'abord les associations existantes puis insère les nouvelles.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @param array $categories Tableau d'IDs de catégories à associer.
     * @return bool true en cas de succès.
     */
    public function updateCategoriesFlashcard(int $idFlashcard, array $categories)
    {
        try {
            $delete = $this->db->prepare("DELETE FROM categorie_quiz WHERE quiz_id = ?;");
            $delete->bindValue(1, $idFlashcard);
            $delete->execute();
            foreach ($categories as $categorie) {
                $this->insertQuizCategorie($idFlashcard, (int)$categorie);
            }
            return true;
        } catch (PDOException $e) {
            die("Updating categories for quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour la disponibilité d'une flashcard et ses restrictions d'amis.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @param string $disponibilite Nouvelle disponibilité ('public', 'ami', ...).
     * @param array $amis Tableau d'IDs d'amis autorisés (utilisé si la disponibilité est 'ami').
     * @return bool true en cas de succès.
     */
    public function updateDisponibiliteFlashcard(int $idFlashcard, string $disponibilite, array $amis)
    {
        try {
            $delete = $this->db->prepare("DELETE FROM amiDisponibilite WHERE quiz_id = ?;");
            $delete->bindValue(1, $idFlashcard);
            $delete->execute();
            $update = $this->db->prepare("UPDATE quiz SET disponibilite = ? WHERE id = ?;");
            $update->bindValue(1, $disponibilite);
            $update->bindValue(2, $idFlashcard);
            $update->execute();
            if ($disponibilite == 'ami') {
                $ownerId = $this->getUserIdFromFlashcard($idFlashcard);
                $amisSelectionnes = $this->normalizeSelectedFriends((int)$ownerId, $amis);
                foreach ($amisSelectionnes as $ami) {
                    $this->insertAmiDispo($idFlashcard, (int)$ami);
                }
            }
            return true;
        } catch (PDOException $e) {
            die("Updating disponibilite for quiz failed: " . $e->getMessage());
        }
    }

    private function normalizeSelectedFriends(int $userId, array $amis): array
    {
        if (in_array('tous', $amis, true)) {
            $allFriends = $this->getAmis($userId);
            $ids = array_map(static fn($ami) => (int)$ami['ami_id'], $allFriends);
            return array_values(array_unique(array_filter($ids, static fn($id) => $id > 0)));
        }

        $ids = array_map('intval', $amis);
        return array_values(array_unique(array_filter($ids, static fn($id) => $id > 0)));
    }

    /**
     * Met à jour une carte (question/réponse) identifiée par son numéro dans une flashcard.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @param int $numeroCarte Numéro d'ordre de la carte dans la flashcard.
     * @param string $questionContent Nouveau texte de la question.
     * @param string $reponseContent Nouveau texte de la réponse.
     * @return bool true en cas de succès.
     */
    public function updateCardFromFlashcard(int $idFlashcard, int $numeroCarte, string $questionContent, string $reponseContent)
    {
        try {
            $updateCarte = $this->db->prepare("UPDATE Carte SET question = ?, reponse = ? WHERE quiz_id = ? AND numeroCarte = ? RETURNING id;");
            $updateCarte->bindValue(1, $questionContent);
            $updateCarte->bindValue(2, $reponseContent);
            $updateCarte->bindValue(3, $idFlashcard);
            $updateCarte->bindValue(4, $numeroCarte);
            $updateCarte->execute();
            return true;
        } catch (PDOException $e) {
            die("Updating question in quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Ajoute une nouvelle carte à une flashcard.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @param int $numCarte Numéro d'ordre de la nouvelle carte.
     * @param string $questionContent Texte de la question.
     * @param string $reponseContent Texte de la réponse.
     * @return bool true en cas de succès.
     */
    public function addCardToFlashcard(int $idFlashcard, int $numCarte, string $questionContent, string $reponseContent)
    {
        try {
            $this->insertCarte($idFlashcard, $numCarte, $idFlashcard, $questionContent, $reponseContent);

            return true;
        } catch (PDOException $e) {
            die("Adding question to quiz failed: " . $e->getMessage());
        }
    }

    /**
     * Met à jour le titre et la description d'une flashcard.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @param string $title Nouveau titre.
     * @param string $description Nouvelle description.
     * @return bool true en cas de succès.
     */
    public function updateResumflashcard(int $idFlashcard, string $title, string $description)
    {
        try {
            $updateResum = $this->db->prepare("UPDATE quiz SET title = ?, description = ? WHERE id = ?;");
            $updateResum->bindValue(1, $title);
            $updateResum->bindValue(2, $description);
            $updateResum->bindValue(3, $idFlashcard);
            $updateResum->execute();
            return true;
        } catch (PDOException $e) {
            die("Updating quiz resum failed: " . $e->getMessage());
        }
    }

    /**
     * Récupère la liste des IDs d'amis sélectionnés pour une flashcard.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @return array Tableau d'IDs d'amis.
     */
    public function getAmisSelection(int $idFlashcard)
    {
        try {
            $amis = $this->db->prepare("SELECT ami_id FROM amiDisponibilite WHERE quiz_id = ?;");
            $amis->bindvalue(1, $idFlashcard);
            $amis->execute();
            $result = $amis->fetchAll(PDO::FETCH_ASSOC);
            $TAB_AMIS = array();
            foreach ($result as $ami) {
                $TAB_AMIS[] = $ami['ami_id'];
            }
            return $TAB_AMIS;
        } catch (PDOException $e) {
            die("Fetching selected friends failed: " . $e->getMessage());
        }
    }

    /**
     * Supprime une carte d'une flashcard identifiée par son numéro.
     *
     * @param int $idFlashcard Identifiant de la flashcard (quiz).
     * @param int $numeroCarte Numéro d'ordre de la carte à supprimer.
     * @return bool true en cas de succès.
     */
    public function deleteCardFromFlashcard(int $idFlashcard, int $numeroCarte)
    {
        try {
            $getCard = $this->db->prepare("SELECT id FROM Carte WHERE quiz_id = ? AND numeroCarte = ?;");
            $getCard->bindValue(1, $idFlashcard);
            $getCard->bindValue(2, $numeroCarte);
            $getCard->execute();
            $question = $getCard->fetch(PDO::FETCH_ASSOC);
            if ($question) {
                $deleteCard = $this->db->prepare("DELETE FROM Carte WHERE id = ?;");
                $deleteCard->bindValue(1, $question['id']);
                $deleteCard->execute();
            }
            return true;
        } catch (PDOException $e) {
            die("Deleting question from quiz failed: " . $e->getMessage());
        }
    }
}
