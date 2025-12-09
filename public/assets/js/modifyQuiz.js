let modif = false;

document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const idQuiz = parseInt(document.querySelector("#idQuiz").value,10);
    const URL = form.getAttribute("action")+"?page=standard&categorie=modify&id="+idQuiz;


    const paramTimer = document.querySelector("#timer");
    const timerP = document.querySelector(".timerP");
    const timerV = document.querySelector("[name='timerValue']");
    function fpHidden(ev){
        
        timerP.hidden = !timerP.hidden;
        timerV.hidden = !timerV.hidden;
    }

    paramTimer.addEventListener("click", fpHidden);

    const modifCat = document.querySelector("#modifCategories");
    const catList = document.querySelectorAll(".category");

    modifCat.addEventListener("click", (ev) => {
        if (!modif){
            modif = true;
            ev.preventDefault();
            for (let cat of catList){
                if (cat.hidden){
                    cat.hidden = false;
                    cat.closest("label").hidden = false;
                }
                if(cat.disabled){
                    cat.disabled = false;
                }
            }
            const AppliquerBtn = document.createElement("button");
            AppliquerBtn.type = "submit";
            AppliquerBtn.name = "appliquerCat";
            AppliquerBtn.id = "appliquerCat";
            AppliquerBtn.textContent = "Appliquer";
            AppliquerBtn.addEventListener("click", (evt) =>{
                evt.preventDefault();
                const formData = new FormData();

                formData.append("appliquerCat", 1);
                for(let cat of catList){
                    if (cat.checked){
                        formData.append("categories[]", cat.value);
                    }
                }
                
                fetch(URL, {
                    method: "POST",
                    body: formData
                }).then(() => {
                    window.location.href = URL;
                });

            });
            modifCat.replaceWith(AppliquerBtn);
        }
        else{
            ev.preventDefault();
            alert("Veuillez terminer la modification en cours avant d'en modifier une autre.");
        }
        
    });

    const modifPublish = document.querySelector("#modifDispo");
    const amis = document.querySelectorAll(".friends");
    const select = document.querySelector("#disponibilite");

    modifPublish.addEventListener("click", (ev) => {
        if (!modif){
            modif = true;
            ev.preventDefault();
            select.disabled = false;
            for(let ami of amis){
                if(ami.hidden){
                    ami.hidden = false;
                }
                if(ami.children[0].disabled){
                    ami.children[0].disabled = false;
                }
            }
            const AppliquerBtnPublish = document.createElement("button");
            AppliquerBtnPublish.type = "submit";
            AppliquerBtnPublish.name = "appliquerDispo";
            AppliquerBtnPublish.id = "appliquerDispo";
            AppliquerBtnPublish.textContent = "Appliquer";
            AppliquerBtnPublish.addEventListener("click", (evt) =>{
                evt.preventDefault();
                const formData = new FormData();

                formData.append("appliquerDispo", 1);
                formData.append("disponibilite",select.value);
                for(let ami of amis){
                    if (ami.children[0].checked){
                        formData.append("amiDispo[]", ami.children[0].value);
                    }
                }
                
                fetch(URL, {
                    method: "POST",
                    body: formData
                }).then(() => {
                    window.location.href = URL;
                });

            });
            modifPublish.replaceWith(AppliquerBtnPublish);
        }
        else{
            ev.preventDefault();
            alert("Veuillez terminer la modification en cours avant d'en modifier une autre.");
        }
        
    });

    const modifQuestions = document.querySelectorAll(".modifierQuestion");

    function questionCorrect(i){
        const textareaContent = document.querySelector("[name='question"+i+"']");
        const repQuestion = document.querySelectorAll("[name='reponse"+i+"[]']");
        const checkQuestion = document.querySelectorAll("[name='checkbox"+i+"[]']");

        let auMoinsUneJuste = false;
        let auMoinsUneFausse = false;
        for (let check of checkQuestion){
            if (check.checked){
                auMoinsUneJuste = true;
            }
            else{
                auMoinsUneFausse = true;
            }
        }
        if (!auMoinsUneFausse || !auMoinsUneJuste) {
            return false;
        }
        if (textareaContent.value.trim() === ""){
            return false;
        }
        for (let rep of repQuestion){
            if (rep.value.trim() === ""){
                return false;
            }
        }
        return true;
    }

    function modifQuestionClick(ev){
        if(!modif){
            modif = true;
            ev.preventDefault();
            let i = ev.currentTarget.value;
            const textareaContent = document.querySelector("#textarea"+i);
            const repQuestion = document.querySelectorAll("[name='reponse"+i+"[]']");
            const checkQuestion = document.querySelectorAll("[name='checkbox"+i+"[]']");

            const footerQuestion = document.querySelector("#questionFooter"+i);

            textareaContent.disabled = false;
            for (let rep of repQuestion){
                rep.disabled = false;
            }
            for (let check of checkQuestion){
                check.disabled = false;
            }

            const addRep = document.createElement("button");
            addRep.classList.add("button", "addRep");
            addRep.name = "addRep"+i;
            addRep.appendChild(document.createElement("span"));
            const pAddRep = document.createElement("p");
            pAddRep.textContent = "Ajouter une réponse";
            addRep.appendChild(pAddRep);
            addRep.addEventListener("click", (evt) =>{
                evt.preventDefault();
                const quizQuestion = document.querySelector("#quizQuestion"+i);

                
                const repList = quizQuestion.querySelectorAll(".reponse");
                const nextRow = repList.length + 1; 

                const divRep = quizQuestion.querySelector(".reponse");
                const checkRep = quizQuestion.querySelector(".checkbox");

                const newRep = divRep.cloneNode(true);
                const newCheck = checkRep.cloneNode(true);
                
                newRep.querySelector("p").textContent = "nouvelle réponse :";
                
                newRep.querySelector("div").querySelector("input").value = "";
                newCheck.querySelector("input").checked = false;

                newRep.style.gridRowStart = nextRow;
                newRep.style.gridRowEnd = nextRow + 1;
                newCheck.style.gridRowStart = nextRow;
                newCheck.style.gridRowEnd = nextRow + 1;

                
                const questionDiv = quizQuestion.querySelector(".question");
                questionDiv.style.gridRowEnd = nextRow + 1;

                quizQuestion.appendChild(newRep);
                quizQuestion.appendChild(newCheck);
            });
            
            footerQuestion.appendChild(addRep);

            const supprRep = document.createElement("button");
            supprRep.classList.add("button", "delRep");
            supprRep.name = "delRep"+i;
            supprRep.appendChild(document.createElement("span"));
            const pSupprRep = document.createElement("p");
            pSupprRep.textContent = "Supprimer une réponse";
            supprRep.appendChild(pSupprRep);
            supprRep.addEventListener("click", (evt) =>{
                evt.preventDefault();
                const quizQuestion = document.querySelector("#quizQuestion"+i);
                const repList = quizQuestion.querySelectorAll(".reponse");
                const lastRep = repList[repList.length -1];
                const checkList = quizQuestion.querySelectorAll(".checkbox");
                const lastCheck = checkList[checkList.length -1];

                if (repList.length > 2){
                    quizQuestion.removeChild(lastRep);
                    quizQuestion.removeChild(lastCheck);

                    const questionDiv = quizQuestion.querySelector(".question");
                    const newEndRow = repList.length;
                    questionDiv.style.gridRowEnd = newEndRow;
                }
            });
            footerQuestion.appendChild(supprRep);

            const applyModif = document.createElement("button");
            applyModif.type = "submit";
            applyModif.name = "applyModif";
            applyModif.id = "applyModif"+i;
            applyModif.value = i;
            applyModif.textContent = "Appliquer";
            applyModif.addEventListener("click", (evt)=>{
                evt.preventDefault();
                if (questionCorrect(i)){
                    const formData = new FormData();

                    formData.append("applyModif", i);
                    formData.append("question"+i,textareaContent.value);
                    for(let rep of document.querySelectorAll("[name='reponse"+i+"[]']")){
                        formData.append("reponse"+i+"[]", rep.value);
                    }
                    for(let checkRep of document.querySelectorAll("[name='checkbox"+i+"[]']")){
                        if (checkRep.checked){
                            formData.append("checkbox"+i+"[]", 1);
                        }
                        else{
                            formData.append("checkbox"+i+"[]", 0);
                        }
                    }
                    
                    fetch(URL, {
                        method: "POST",
                        body: formData
                    }).then(() => {
                        window.location.href = URL;
                    });
                }
                else{
                    alert("Veuillez vérifier que la question possède des champs complets et qu'il y ait au moins ne réponse juste et fausse.");
                }
                
            });

            const DelBtn = document.querySelector("#DelQuestion"+i);
            DelBtn.replaceWith(applyModif);
            ev.currentTarget.remove();

        }
        else{
            ev.preventDefault();
            alert("Veuillez terminer la modification en cours avant d'en modifier une autre.");
        }
    }

    for(let modif of modifQuestions){
        modif.addEventListener("click", modifQuestionClick);
    }


});
