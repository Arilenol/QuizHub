<?php
    $title = 'modification de quiz';
    $style = './assets/style/modifyQuiz.css';
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    require __DIR__ . '/../partials/header.php';
?>

<main class="modify-quiz-page">
    <form method="post" data-id="<?php echo htmlspecialchars($idQuiz) ?>" action="index.php?page=standard&categorie=modify&id=<?php echo htmlspecialchars($idQuiz) ?>">
        <input type="hidden" name="idQuiz" id="idQuiz" value="<?php echo htmlspecialchars($idQuiz) ?>">
        <button class="button" type="submit" name="Retour" value="yes"><span></span><p>< Retour</p></button>
        
        <div class="form-group">
            <h2>Résumé du quiz <button class="modifResum" id="modifResum">Modifier</button></h2>
            <p class="name">Nom du quiz</p>
            <div class="input">
                <span></span>
                <input form="no" type="text" name="QuizTitle" id="QuizTitle" value="<?php echo htmlspecialchars($quizInfos['title']) ?>" disabled>
            </div>
            <p class="description">Description</p>
            <div class="input">
                <span></span>
                <input form="no" type="text" name="QuizDescription" id="QuizDescription" value="<?php echo htmlspecialchars($quizInfos['description']) ?>" disabled>
            </div>
        </div>

        <div class="form-group">
            <h2>Catégories <button id="modifCategories" type="button">Modifier</button></h2>
            <div class="categoriesList">
                <?php foreach($ALL_CATEGORIES as $categorie): ?>
                    <?php $checked = in_array($categorie, $TAB_CATEGORIES) ? 'checked' : ''; ?>
                    <label class="<?= !empty($checked) ? '' : 'hidden' ?>">
                        <input class="category" name="categories[]" type="checkbox" value="<?= htmlspecialchars($categorie['id']) ?>" <?= $checked ?> <?= !empty($checked) ? '' : 'hidden' ?> disabled>
                        <?= htmlspecialchars($categorie['categorieName']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="questions-container">
            <?php for($i = 0; $i < $taille; $i++): ?>
                <div class="newQuiz" id="quizQuestion<?= $i ?>">
                    <h2 class="validite">Réponse valide ?</h2>
                    <h2 class="question">Question <?= $i + 1 ?></h2>
                    <div class="textarea questionInput" id="question<?= $i + 1 ?>">
                        <span></span>
                        <textarea type="text" name="question<?= $i ?>" id="textarea<?= $i ?>" placeholder="nom de la question" disabled><?= htmlspecialchars($TAB_QUESTIONS[$i]['question']) ?></textarea>
                    </div>
                    
                    <?php for($k = 0; $k < $TAB_QUESTIONS[$i]['nbReponse']; $k++): ?>
                        <h2 class="reponse" style="grid-row: <?= ($k * 2) + 1 ?> / <?= ($k * 2) + 2 ?>; grid-column: 2 / 3">Réponse <?= $k + 1 ?> :</h2>
                        <div class="input responseInput" style="grid-row: <?= ($k * 2) + 2 ?> / <?= ($k * 2) + 3 ?>; grid-column: 2 / 3">
                            <span></span>
                            <input form="no" name="reponse<?= $i ?>[]" value="<?= htmlspecialchars($TAB_QUESTIONS[$i]['reponses'][$k]['reponse']) ?>" disabled>
                        </div>
                        <div class="checkbox" style="grid-row: <?= ($k * 2) + 2 ?> / <?= ($k * 2) + 3 ?>; grid-column: 3 / 4">
                            <input type="checkbox" name="checkbox<?= $i ?>[]" <?= $TAB_QUESTIONS[$i]['reponses'][$k]['estCorrecte'] ? 'checked' : '' ?> disabled hidden>
                        </div>
                    <?php endfor; ?>

                    <div id="questionFooter<?= $i ?>" class="questionFooter" style="grid-row: <?= $TAB_QUESTIONS[$i]['nbReponse'] * 2 + 1 ?> / <?= $TAB_QUESTIONS[$i]['nbReponse'] * 2 + 2 ?>; grid-column: -2 / -3;">
                        <button class="button modifierQuestion" type="submit" id="modifier<?= $i ?>" name="modifierQuestion" value="<?= $i ?>">
                            <span></span>
                            <p>Modifier</p>
                        </button>
                    </div>

                    <?php if ($i != 0 || $taille > 1): ?>
                        <button class="button delQuestionButton" style="grid-row: <?= $TAB_QUESTIONS[$i]['nbReponse'] * 2 + 1 ?> / <?= $TAB_QUESTIONS[$i]['nbReponse'] * 2 + 2 ?>; grid-column: -1 / -2;" name="DelQuestion" id="DelQuestion<?= $i ?>" type="submit" value="<?= $i ?>"><span></span><p>Supprimer cette question</p></button>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
        <button class="button" type="submit" name="addQuestion" id="addQuestion" value="yes">
            <span></span>
            <p>Ajouter une question</p>
        </button>

        <div class="form-group">
            <h2><?= $quizInfos['genre'] == 'test' ? 'test' : 'Quiz standard' ?>
                <button id="modifTest">Changer</button>
                <div class="checkbox" id="Test">
                    <input type="checkbox" id="genreTest" name="genreTest" <?= $quizInfos['genre'] == 'test' ? 'checked' : '' ?> disabled hidden />
                </div>
            </h2>
        </div>

        <div class="parametres" hidden>
            <?php foreach ($tabParametres as $indice => $param): ?>
                <div class="form-group param-group">
                    <p><?= $param['desc'] ?> :</p>
                    <div class="checkbox param" id="<?= $param['name'] ?>" value="<?= $indice ?>">
                        <input type="checkbox" id="param<?= $param['name'] ?>" name="param<?= $param['name'] ?>" <?= $TAB_PARAMS[$indice] != 0 ? 'checked' : '' ?> disabled hidden />
                    </div>
                    <?php if ($param['name'] == 'timer'): ?>
                        <?php $hidden = (empty($TAB_PARAMS[0]) || $TAB_PARAMS[0] == 0) ? 'hidden' : ''; ?>
                        <p class="timerP <?= $hidden ?>" id="timerP">Temps en minutes entre 0 et 120<br>(0 ne sera pas compté) :</p>
                        <input type="number" form="no" name="timerValue" id="timerV" value="<?= htmlspecialchars($TAB_PARAMS[$indice]) ?>" min="0" max="120" class="<?= $hidden ?>" disabled />
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="form-group disponibilite">
            <h2 class="section-title">Mode de publication <button id="modifDispo">Modifier</button></h2>
            <select name="disponibilite" id="disponibilite" disabled>
                <option value="public" <?= $quizInfos['disponibilite'] == 'public' ? 'selected' : '' ?>>publique</option>
                <option value="ami" <?= $quizInfos['disponibilite'] == 'ami' ? 'selected' : '' ?>>Seulement les amis</option>
                <option value="private" <?= $quizInfos['disponibilite'] == 'private' ? 'selected' : '' ?>>seulement vous</option>
            </select>
            <div class="ami-list" <?= $quizInfos['disponibilite'] != "ami" ? 'hidden' : '' ?>>
                <?php
                $hidden2 = $quizInfos['disponibilite'] != "ami" ? 'hidden' : '';
                $checkedTous = in_array('tous', $TAB_AMIS) ? 'checked' : '';
                echo '<label class="friends" '.$hidden2.'><input name="amiDispo[]" type="checkbox" value="tous" '.$checkedTous.' disabled>Tous les amis</label>';
                foreach($ALL_AMIS as $ami){
                    $checked = in_array($ami['ami_id'], $TAB_AMIS) ? 'checked' : '';
                    echo '<label class="friends" '.$hidden2.'><input name="amiDispo[]" type="checkbox" value="'.htmlspecialchars($ami['ami_id']).'" '.$checked.' disabled>'.htmlspecialchars($ami['username']).'</label>';
                }
                ?>
            </div>
        </div>

        <?php if ($erreur): ?>
            <p class="erreur">Chaque champ doit être rempli<br>Chaque question doit avoir au moins une réponse juste et une réponse fausse<br>Au moins une catégorie doit être sélectionnée</p>
        <?php endif; ?>
    </form>
</main>
<script src="./assets/js/popups.js"></script>
<script src="./assets/js/modifyQuiz.js"></script>
<script src="./assets/js/sauvegardeScroll.js"></script>
<script src="./assets/js/selectDispo.js"></script>
