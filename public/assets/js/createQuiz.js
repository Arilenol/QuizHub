
const paramTimer = document.querySelector("#timer");
const timerP = document.querySelector(".timerP");
const timerV = document.querySelector("[name='timerValue']");

function fpHidden(ev){
      
    timerP.hidden = !timerP.hidden;
    timerV.hidden = !timerV.hidden;
}


paramTimer.addEventListener("click", fpHidden);

function questionManquant(quizs){
    const questions = []
    for(let quiz of quizs){
        questions.push(quiz.querySelector(".question"));
    }
    for (let question of questions){
        if (question.querySelector("textarea").value == "") {
            return true
        }
    }
    return false
}

function reponsesManquantes(quizs){
    const reponses = []
    for(let quiz of quizs){
        for (let reponse of quiz.querySelectorAll(".reponse")){
            reponses.push(reponse.querySelector(".input"));
        }
    }
    for (let reponse of reponses){
        if (reponse.querySelector("input").value == "") {
            return true
        }
    }
    return false
}

function erreurCheckbox(quizs){
    for(let quiz of quizs){
        let compt = 0;
        for (let validite of quiz.querySelectorAll(".checkbox")){
            if (validite.querySelector("input").checked){
                compt += 1;
            }
        }
        if (compt == 0 || compt == quiz.querySelectorAll(".reponse").length){
            return true;
        }
    }
    return false;
}

function disponibiliteErreur(dispo){
    const select = dispo.querySelector("select");
    let option = null
    for (let optionS of select.querySelectorAll("option")){
        if (optionS.selected){
            option = optionS
        }
    }
    if (option.value == "ami"){
        const amis = dispo.querySelectorAll("[name='amiDispo[]']");
        if (amis.length == 1){
            return true;
        }
        let compt = 0;
        for (let ami of amis){
            if (ami.checked){
                compt += 1;
            }
        }
        if (compt == 0){
            return true;
        }
    }
    return false;
}

const boutonCreate = document.querySelector("#create");
boutonCreate.addEventListener("click",(ev)=>{
    const titre = document.querySelector("[name='QuizTitle']");
    const description = document.querySelector("[name='QuizDescription'");
    const allCategories = document.querySelectorAll("[name='categories[]']");
    const categories = []
    for (let categorie of allCategories){
        if (categorie.checked){
            categories.push(categorie);
        }
    }
    const quizs = document.querySelectorAll(".newQuiz");
    const dispo = document.querySelector(".disponibilite");

    if (titre.value == ""){
        popupAvertissement("Le champ titre est vide. Vous ne pouvez pas créer le quiz.");
        ev.preventDefault()
    }
    else if (description.value == ""){
        popupAvertissement("Le champ description est vide. Vous ne pouvez pas créer le quiz.");
        ev.preventDefault()
    }
    else if (categories.length == 0){
        popupAvertissement("Vous n'avez sélectionner aucunes catégories. Veuillez en sélectionner au moins une.");
        ev.preventDefault()
    }
    else if(questionManquant(quizs)){
        popupAvertissement("Un champ d'intitulé de question est vide. Veuillez remplir le champ manquant.");
        ev.preventDefault()
    }
    else if (reponsesManquantes(quizs)){
        popupAvertissement("Un champ de réponse est vide. Veuillez le remplir.");
        ev.preventDefault();
    }
    else if(erreurCheckbox(quizs)){
        popupAvertissement("Il doit y avoir au minimum une réponse fausse et une juste pour chaque question. Vérifiez la validité des réponses.");
        ev.preventDefault();
    }
    else if (disponibiliteErreur(dispo)){
        popupAvertissement("Vous ne pouvez pas mettre la disponibilité en 'ami' en ne sélectionnant aucun ami. Si vous avez des amis, sélectionnez des amis. Sinon, ayez des amis.");
        ev.preventDefault()
    }
})


