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
            footerQuestion.classList.add("is-editing");

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

                const repList = quizQuestion.querySelectorAll(".responseInput");
                const nextRowStart = (repList.length * 2) + 1;

                const repTitle = quizQuestion.querySelector(".reponse");
                const repInput = quizQuestion.querySelector(".responseInput");
                const checkRep = quizQuestion.querySelector(".checkbox");

                const newRepTitle = repTitle.cloneNode(true);
                const newRepInput = repInput.cloneNode(true);
                const newCheck = checkRep.cloneNode(true);

                newRepTitle.textContent = "Reponse " + (repList.length + 1) + " :";
                newRepInput.querySelector("input").value = "";
                newRepInput.querySelector("input").disabled = false;
                newCheck.querySelector("input").checked = false;
                newCheck.querySelector("input").disabled = false;

                newRepTitle.style.gridRowStart = nextRowStart;
                newRepTitle.style.gridRowEnd = nextRowStart + 1;
                newRepInput.style.gridRowStart = nextRowStart + 1;
                newRepInput.style.gridRowEnd = nextRowStart + 2;
                newCheck.style.gridRowStart = nextRowStart + 1;
                newCheck.style.gridRowEnd = nextRowStart + 2;

                const questionDiv = quizQuestion.querySelector(".questionInput");
                questionDiv.style.gridRowEnd = nextRowStart + 2;

                quizQuestion.appendChild(newRepTitle);
                quizQuestion.appendChild(newRepInput);
                quizQuestion.appendChild(newCheck);

                const footer = quizQuestion.querySelector(".questionFooter");
                const delBtn = quizQuestion.querySelector(".delQuestionButton");
                if (footer) {
                    footer.style.gridRowStart = nextRowStart + 2;
                    footer.style.gridRowEnd = nextRowStart + 3;
                }
                if (delBtn) {
                    delBtn.style.gridRowStart = nextRowStart + 2;
                    delBtn.style.gridRowEnd = nextRowStart + 3;
                }

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
                const repTitles = quizQuestion.querySelectorAll(".reponse");
                const repInputs = quizQuestion.querySelectorAll(".responseInput");
                const checkList = quizQuestion.querySelectorAll(".checkbox");

                if (repInputs.length > 2){
                    const lastRepTitle = repTitles[repTitles.length - 1];
                    const lastRepInput = repInputs[repInputs.length - 1];
                    const lastCheck = checkList[checkList.length - 1];

                    quizQuestion.removeChild(lastRepTitle);
                    quizQuestion.removeChild(lastRepInput);
                    quizQuestion.removeChild(lastCheck);

                    const questionDiv = quizQuestion.querySelector(".questionInput");
                    const remainingRepInputs = repInputs.length - 1;
                    const newEndRow = (remainingRepInputs * 2) + 2;
                    questionDiv.style.gridRowEnd = newEndRow;

                    const footer = quizQuestion.querySelector(".questionFooter");
                    const delBtn = quizQuestion.querySelector(".delQuestionButton");
                    if (footer) {
                        footer.style.gridRowStart = newEndRow;
                        footer.style.gridRowEnd = newEndRow + 1;
                    }
                    if (delBtn) {
                        delBtn.style.gridRowStart = newEndRow;
                        delBtn.style.gridRowEnd = newEndRow + 1;
                    }
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
                DelBtn.remove();
            }
            footerQuestion.appendChild(applyModif);
            footerQuestion.appendChild(Annuler);
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

            const lastQuestion = document.querySelectorAll(".newQuiz")[document.querySelectorAll(".newQuiz").length-1];
            const newQuestion = lastQuestion.cloneNode(true);

            const questionInput = newQuestion.querySelector(".questionInput");
            const questionTextarea = questionInput.querySelector("textarea");
            questionTextarea.disabled = false;
            questionTextarea.value = "";
            questionInput.style.gridRowEnd = 6;

            const repTitles = newQuestion.querySelectorAll(".reponse");
            const repInputs = newQuestion.querySelectorAll(".responseInput");
            const checkList = newQuestion.querySelectorAll(".checkbox");

            for (let j = 0; j < repTitles.length; j++){
                if (j < 2){
                    repTitles[j].textContent = "Reponse " + (j + 1) + " :";
                }
                else{
                    repTitles[j].remove();
                }
            }

            for (let j = 0; j < repInputs.length; j++){
                if (j < 2){
                    const input = repInputs[j].querySelector("input");
                    input.value = "";
                    input.disabled = false;
                }
                else{
                    repInputs[j].remove();
                }
            }

            for (let j = 0 ; j < checkList.length; j++){
                if (j < 2){
                    checkList[j].querySelector("input").checked = false;
                    checkList[j].querySelector("input").disabled = false;
                }
                else{
                    checkList[j].remove();
                }
            }

            let i = parseInt(newQuestion.querySelector(".modifierQuestion").value, 10);
            newQuestion.querySelector("#questionFooter"+i).id = "questionFooter"+(i+1);
            newQuestion.querySelector(".question").textContent = "Question "+(i+2);
            questionInput.id = "question"+(i+1);

            for (let repInput of newQuestion.querySelectorAll(".responseInput")){
                repInput.querySelector("input").name = "reponse"+(i+1)+"[]";
            }
            for (let check of newQuestion.querySelectorAll(".checkbox")){
                check.querySelector("input").name = "checkbox"+(i+1)+"[]";
            }

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
            delBtn.style.gridRowStart = 5;
            delBtn.style.gridRowEnd = 6;
            delBtn.addEventListener("click", delQuestionFunction);

            const questionFooter = newQuestion.querySelector(".questionFooter");
            questionFooter.id = "questionFooter"+(i+1);
            questionFooter.style.gridRowStart = 5;
            questionFooter.style.gridRowEnd = 6;

            questionTextarea.id = "textarea"+(i+1);
            questionTextarea.name = "question"+(i+1);

            newQuestion.id = "quizQuestion"+(i+1);
            const footer = questionFooter.querySelector(".modifierQuestion");
            footer.value = i+1;
            footer.id = "modifier"+(i+1);
            footer.addEventListener("click", modifQuestionClick);

            lastQuestion.after(newQuestion);

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

    /*const modifParams = document.querySelector("#modifParam");

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

    });*/
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


