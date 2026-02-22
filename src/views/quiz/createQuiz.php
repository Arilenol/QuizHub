<?php
    $title = 'création de quiz';
    $style = './assets/style/createQuiz.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>

<main class="create-quiz-page">
    <form method="post" action="index.php?page=standard&categorie=create">
        <button class="button" type="submit" name="Retour" value="yes"><span></span><p>< Retour</p></button>
        <h1>Créer un Quiz</h1>
        <input name="page" value="standard" hidden>
        <input name="categorie" value="create" hidden>

        <div class="form-group">
            <h2>Nom du quiz</h2>
            <div class="input">
                <span></span>
                <input type="text" name="QuizTitle" value="<?php echo htmlspecialchars($quizTitle) ?>">
            </div>
        </div>

        <div class="form-group">
            <h2>Description</h2>
            <div class="input">
                <span></span>
                <input type="text" name="QuizDescription" value="<?php echo htmlspecialchars($desc) ?>">
            </div>
        </div>

        <div class="form-group">
            <h2>Catégories</h2>
            <div class="categoriesList">
                <?php
                foreach($TAB_CATEGORIE as $categorie){
                    $checked = in_array((string)$categorie['id'], $TAB_CATEGORIE_CHOISI) ? 'checked' : '';
                    echo '<label><input name="categories[]" type="checkbox" value="'.htmlspecialchars($categorie['id']).'" '.$checked.'>'.htmlspecialchars($categorie['categorieName']).'</label>';
                }
                ?>
            </div>
        </div>

        <div class="questions-container">
            <?php for($i = 0; $i < $_SESSION['nbQuestions']; $i++): ?>
                <div class="newQuiz">
                    <h2 class="validite">Réponse valide ?</h2>
                    <h2 class="question">Question <?= $i + 1 ?></h2>
                    <div class="textarea questionInput" id="question<?= $i + 1 ?>">
                        <span></span>
                        <textarea type="text" name="question<?= $i ?>" placeholder="nom de la question"><?= htmlspecialchars($TAB_CONTENU[$i]['name']) ?></textarea>
                    </div>

                    <?php for ($k = 0; $k < $_SESSION['nbReponse'][$i]; $k++): ?>
                        <h2 class="reponse" style="grid-row: <?= ($k * 2) + 1 ?> / <?= ($k * 2) + 2 ?>; grid-column: 2 / 3">Réponse <?= $k + 1 ?> :</h2>
                        <div class="input responseInput" style="grid-row: <?= ($k * 2) + 2 ?> / <?= ($k * 2) + 3 ?>; grid-column: 2 / 3">
                            <span></span>
                            <input name="reponse<?= $k ?>-question<?= $i ?>" value="<?= htmlspecialchars($TAB_CONTENU[$i]['reponses'][$k]['texte']) ?>">
                        </div>
                        <div class="checkbox" style="grid-row: <?= ($k * 2) + 2 ?> / <?= ($k * 2) + 3 ?>; grid-column: 3 / 4">
                            <input type="checkbox" name="checkbox<?= $k ?>-question<?= $i ?>" <?= $TAB_CONTENU[$i]['reponses'][$k]['valide'] ? 'checked' : '' ?> hidden>
                        </div>
                    <?php endfor; ?>

                    <div class="question-buttons">
                        <button class="button" type="submit" name="addReponse" value="<?= $i ?>">
                            <span></span>
                            <p>Ajouter une réponse</p>
                        </button>
                        <button class="button" name="delReponse<?= $i ?>" value="yes" type="submit"><span></span> <p>Supprimer une réponse</p></button>
                    </div>

                    <?php if ($i != 0 || $_SESSION['nbQuestions'] > 1): ?>
                        <button class="button" name="DelQuestion" type="submit" value="<?= $i ?>"><span></span><p>Supprimer cette question</p></button>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>

        <button class="button" type="submit" name="addQuestion" value="yes">
            <span></span>
            <p>Ajouter une question</p>
        </button>

        <h2>Test</h2>
        <div class="form-group test-param">
            <p><?= $tabParametres[0]['desc'] ?></p>
            <div class="checkbox param" id="<?= $tabParametres[0]['name'] ?>">
                <input type="checkbox" id="param<?= $tabParametres[0]['name'] ?>" name="param<?= $tabParametres[0]['name'] ?>" <?= $TAB_PARAM[0] ?> hidden />
            </div>
        </div>

        <h2 hidden>Paramètres</h2>
        <div class="parametres" hidden>
            <?php foreach (array_slice($tabParametres, 1) as $indice => $param): ?>
                <div class="form-group">
                    <p><?= $param['desc'] ?> :</p>
                    <div class="checkbox param" id="<?= $param['name'] ?>">
                        <input type="checkbox" id="param<?= $param['name'] ?>" name="param<?= $param['name'] ?>" <?= $TAB_PARAM[$indice + 1] ?> hidden />
                    </div>
                    <?php if ($param['name'] == 'timer'): ?>
                        <?php $hidden = empty($_SESSION['POST']['param' . $param['name']]) ? 'hidden' : ''; ?>
                        <p class="timerP <?= $hidden ?>">Temps en minutes entre 0 et 120<br>(0 ne sera pas compté) :</p>
                        <input type="number" name="timerValue" value="<?= htmlspecialchars($timerValue) ?>" min="0" max="120" class="<?= $hidden ?>" />
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-group disponibilite">
            <h2>Mode de publication :</h2>
            <select name="disponibilite" id="disponibilite">
                <option value="public" <?= $_SESSION['POST']['disponibilite'] == 'public' ? 'selected' : '' ?>>publique</option>
                <option value="ami" <?= $_SESSION['POST']['disponibilite'] == 'ami' ? 'selected' : '' ?>>Seulement les amis</option>
                <option value="private" <?= $_SESSION['POST']['disponibilite'] == 'private' ? 'selected' : '' ?>>seulement vous</option>
            </select>
            <div class="ami-list">
                <?php
                $hidden2 = $_SESSION['POST']['disponibilite'] != "ami" ? 'hidden' : '';
                $checkedTous = in_array('tous', $TAB_AMI_CHOISI) ? 'checked' : '';
                echo '<label class="'.$hidden2.'"><input name="amiDispo[]" type="checkbox" value="tous" '.$checkedTous.'>Tous les amis</label>';
                foreach($TAB_AMI as $ami){
                    $checked = in_array($ami['ami_id'], $TAB_AMI_CHOISI) ? 'checked' : '';
                    echo '<label class="'.$hidden2.'"><input name="amiDispo[]" type="checkbox" value="'.$ami['ami_id'].'" '.$checked.'>'.$ami['username'].'</label>';
                }
                ?>
            </div>
        </div>

        <?php if ($_SESSION['erreur']): ?>
            <p class="erreur">Chaque champ doit être rempli<br>Chaque question doit avoir au moins une réponse juste et une réponse fausse<br>Au moins une catégorie doit être sélectionnée</p>
        <?php endif; ?>

        <button class="button" id="create" type="submit" name="create" value="yes"><span></span><p>Créer le quiz</p></button>
    </form>
</main>
<script src="./assets/js/popups.js"></script>
<script src="./assets/js/createQuiz.js"></script>
<script src="./assets/js/sauvegardeScroll.js"></script>
<script src="./assets/js/selectDispo.js"></script>
