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
                popupValidation(() => {
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
                },"L'élément sera modifé de façon permanente.\n Etes-vous sûr d'accepter les modification ?");
                

            });
            const Annuler = document.createElement("button");
            Annuler.type = "submit";
            Annuler.name = "Annuler";
            Annuler.textContent = "Annuler";
            Annuler.addEventListener("click", (ev) =>{
                ev.preventDefault();
                window.location.reload();
            });

            modifCat.replaceWith(AppliquerBtn);
            AppliquerBtn.after(Annuler);
        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
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
                popupValidation(() => {
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
                },"L'élément sera modifé de façon permanente.\n Etes-vous sûr d'accepter les modification ?");
                

            });

            const Annuler = document.createElement("button");
            Annuler.type = "submit";
            Annuler.name = "Annuler";
            Annuler.textContent = "Annuler";
            Annuler.addEventListener("click", (ev) =>{
                ev.preventDefault();
                window.location.reload();
            });

            modifPublish.replaceWith(AppliquerBtnPublish);
            AppliquerBtnPublish.after(Annuler);
        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
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
                newCheck.querySelector("input").disabled = false;

                newRep.style.gridRowStart = nextRow;
                newRep.style.gridRowEnd = nextRow + 1;
                newCheck.style.gridRowStart = nextRow;
                newCheck.style.gridRowEnd = nextRow + 1;

                
                const questionDiv = quizQuestion.querySelector(".question");
                questionDiv.style.gridRowEnd = nextRow + 1;

                quizQuestion.appendChild(newRep);
                quizQuestion.appendChild(newCheck);

                resetCheckboxSVG();
                initCheckboxSVG();
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
            applyModif.classList.add("button");
            applyModif.name = "applyModif";
            applyModif.id = "applyModif"+i;
            applyModif.value = i;
            applyModif.appendChild(document.createElement("span"));
            const pApplyModif = document.createElement("p");
            pApplyModif.textContent = "Appliquer";
            applyModif.appendChild(pApplyModif);
            applyModif.addEventListener("click", (evt)=>{
                evt.preventDefault();
                popupValidation(() => {
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
                        popupAvertissement("Veuillez vérifier que la question possède des champs complets et qu'il y ait au moins ne réponse juste et fausse.");
                    }
                },"L'élément sera modifé de façon permanente.\n Etes-vous sûr d'accepter les modification ?")
                
                
            });

            const DelBtn = document.querySelector("#DelQuestion"+i);

            const Annuler = document.createElement("button");
            Annuler.type = "submit";
            Annuler.classList.add("button");
            Annuler.name = "Annuler";
            Annuler.appendChild(document.createElement("span"));
            const pAnnuler = document.createElement("p");
            pAnnuler.textContent = "Annuler";
            Annuler.appendChild(pAnnuler);
            Annuler.addEventListener("click", (ev) =>{
                ev.preventDefault();
                window.location.reload();
            });
            if (DelBtn) {
                DelBtn.replaceWith(applyModif);
                applyModif.after(Annuler);
            } else {
                // Pas de bouton supprimer → on ajoute quand même les boutons
                const footer = document.querySelector("#questionFooter"+i);
                footer.appendChild(applyModif);
                applyModif.after(Annuler);
            }
            /*DelBtn.replaceWith(applyModif);
            applyModif.after(Annuler);*/
            
            ev.currentTarget.remove();

        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }
    }

    for(let modif of modifQuestions){
        modif.addEventListener("click", modifQuestionClick);
    }

    const addQuestionBtn = document.querySelector("#addQuestion");
    addQuestionBtn.addEventListener("click", (ev) => {
        if (!modif){
            ev.preventDefault();
            
            const newQuestion = document.querySelectorAll(".newQuiz")[document.querySelectorAll(".newQuiz").length-1].cloneNode(true);
            newQuestion.querySelector(".question").disabled = false;
            newQuestion.querySelector(".question").querySelector("textarea").value = "";
            
            newQuestion.querySelector(".question").style.gridRowEnd = 3;
            const repList = newQuestion.querySelectorAll(".reponse");
            const checkList = newQuestion.querySelectorAll(".checkbox");
            console.log(repList, typeof repList);
            for( let i = 0; i < repList.length; i++){
                if (i < 2){
                    repList[i].querySelector("div").querySelector("input").value = "";
                    repList[i].querySelector("div").querySelector("input").disabled = false;
                }
                else{
                    repList[i].remove();
                }
            }
            for (let i = 0 ; i < checkList.length; i++){
                if (i < 2){
                    checkList[i].querySelector("input").checked = false;
                    checkList[i].querySelector("input").disabled = false;
                }
                else{
                    checkList[i].remove();
                }
            }
            let i = parseInt(newQuestion.querySelector(".modifierQuestion").value);
            newQuestion.querySelector("#questionFooter"+i).id = "questionFooter"+(i+1);
            newQuestion.querySelector(".question").querySelector("p").textContent = "Question "+(i+2);
            newQuestion.querySelector(".question").querySelector(".textarea").id = "question"+(i+1);
            for (let rep of newQuestion.querySelectorAll(".reponse")){
                rep.querySelector("div").querySelector("input").name = "reponse"+(i+1)+"[]";
            }
            for (let check of newQuestion.querySelectorAll(".checkbox")){
                check.querySelector("input").name = "checkbox"+(i+1)+"[]";
            }
            /*newQuestion.querySelector(".delQuestionButton").value = (i+1);
            newQuestion.querySelector(".delQuestionButton").id = "DelQuestion"+(i+1);*/
            let delBtn = newQuestion.querySelector(".delQuestionButton");

            if (!delBtn) {
                delBtn = document.createElement("button");
                delBtn.classList.add("button", "delQuestionButton");
                delBtn.name = "DelQuestion";
                delBtn.appendChild(document.createElement("span"));
                const p = document.createElement("p");
                p.textContent = "Supprimer cette question";
                delBtn.appendChild(p);

                newQuestion.querySelector(".questionFooter").appendChild(delBtn);
            }

            delBtn.value = i + 1;
            delBtn.id = "DelQuestion" + (i + 1);
            delBtn.addEventListener("click", delQuestionFunction);
            newQuestion.querySelector(".questionFooter").id = "questionFooter"+(i+1);
            newQuestion.querySelector(".question").querySelector("textarea").id = "textarea"+(i+1);
            newQuestion.querySelector(".question").querySelector("textarea").name = "question"+(i+1);

            newQuestion.id = "quizQuestion"+(i+1);
            const footer = newQuestion.querySelector(".questionFooter").querySelector(".modifierQuestion");
            footer.value = i+1;
            footer.id = "modifier"+(i+1);
            
            footer.addEventListener("click", modifQuestionClick);

            document.querySelectorAll(".newQuiz")[document.querySelectorAll(".newQuiz").length-1].after(newQuestion);

            ev.currentTarget.remove();

            resetCheckboxSVG();
            initCheckboxSVG();
            footer.click();

        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }
    });

    const modifParams = document.querySelector("#modifParam");

    modifParams.addEventListener("click", (ev) => {
        if (!modif){
            modif = true;
            ev.preventDefault();
            const divParams = document.querySelectorAll(".param");
            const timerV = document.querySelector("#timerV");

            timerV.disabled = false;

            for(let div of divParams){
                div.querySelector("input").disabled = false;
            }

            const AppliquerBtn = document.createElement("button");
            AppliquerBtn.type = "submit";
            AppliquerBtn.name = "appliquerParam";
            AppliquerBtn.id = "appliquerParam";
            AppliquerBtn.textContent = "Appliquer";
            AppliquerBtn.addEventListener("click", (evt) =>{
                evt.preventDefault();
                popupValidation(() => {
                    const formData = new FormData();

                    formData.append("appliquerParam", 1);
                    for(let param of divParams){
                        if (param.querySelector("input").checked){
                            formData.append("params[]", 1);
                        }
                        else{
                            formData.append("params[]", 0);
                        }
                    }
                    formData.append("timerValue", timerV.value);
                    
                    fetch(URL, {
                        method: "POST",
                        body: formData
                    }).then(() => {
                        window.location.href = URL;
                    });
                },"L'élément sera modifé de façon permanente.\n Etes-vous sûr d'accepter les modification ?")
                

            });
            const Annuler = document.createElement("button");
            Annuler.type = "submit";
            Annuler.name = "Annuler";
            Annuler.textContent = "Annuler";
            Annuler.addEventListener("click", (ev) =>{
                ev.preventDefault();
                window.location.reload();
            });

            modifParams.replaceWith(AppliquerBtn);
            AppliquerBtn.after(Annuler);

        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }

    });
    const modifTest = document.querySelector("#modifTest");
    modifTest.addEventListener("click", (ev) => {
        if (!modif){
            modif = true;
            ev.preventDefault();
            const genreTest = document.querySelector("#genreTest");
            genreTest.disabled = false;

            if (genreTest.checked){
                genreTest.checked = false;
                modifTest.closest("h2").textContent = "Quiz standard ";
            }
            else{
                genreTest.checked = true;
                modifTest.closest("h2").textContent = "Test ";
            }


            const formData = new FormData();
            formData.append("changerGenre", 1);
            if (genreTest.checked){
                formData.append("genre", "test");
            }
            else{
                formData.append("genre", "standard");
            }
            
            fetch(URL, {
                method: "POST",
                body: formData
            }).then(() => {
                window.location.href = URL;
            });

        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }
    });

    const modifResum = document.querySelector("#modifResum");
    modifResum.addEventListener("click", (ev) => {
        if (!modif){
            modif = true;
            ev.preventDefault();
            const title = document.querySelector("#QuizTitle");
            const description = document.querySelector("#QuizDescription");
            title.disabled = false;
            description.disabled = false;

            const Appliquer = document.createElement("button");
            Appliquer.type = "submit";
            Appliquer.name = "appliquerResum";
            Appliquer.id = "appliquerResum";
            Appliquer.textContent = "Appliquer";
            Appliquer.addEventListener("click", (evt) =>{
                evt.preventDefault();
                popupValidation(() => {
                    const formData = new FormData();

                    formData.append("appliquerResum", 1);
                    formData.append("QuizTitle", title.value);
                    formData.append("QuizDescription", description.value);
                    
                    fetch(URL, {
                        method: "POST",
                        body: formData
                    }).then(() => {
                        window.location.href = URL;
                    });
                },"L'élément sera modifé de façon permanente.\n Etes-vous sûr d'accepter les modification ?");
                

            });
            const Annuler = document.createElement("button");
            Annuler.type = "submit";
            Annuler.name = "Annuler";
            Annuler.textContent = "Annuler";
            Annuler.addEventListener("click", (ev) =>{
                ev.preventDefault();
                window.location.reload();
            });

            modifResum.replaceWith(Appliquer);
            Appliquer.after(Annuler);

        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }
    });

    function delQuestionFunction(ev){
        if (!modif){
            ev.preventDefault();
            const i = ev.currentTarget.value;
            popupValidation(() => {
                const formData = new FormData();
                formData.append("DelQuestion", i);
                
                fetch(URL, {
                    method: "POST",
                    body: formData
                }).then(() => {
                    window.location.href = URL;
                });
            },"L'élément sera supprimer définitivement. Etes-vous sûr de vouloir continuer ?");
            

        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }
    }
    const deleteQuestion = document.querySelectorAll(".delQuestionButton");
    for (let delQ of deleteQuestion){
        delQ.addEventListener("click", delQuestionFunction);
    }

});
