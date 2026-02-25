let modif = false
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const idQuiz = parseInt(document.querySelector("#idLesson").value,10);
    const URL = form.getAttribute("action")+"?page=lesson&categorie=modify&id="+idQuiz;

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
                popupValidation(() =>{
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
            Annuler.textContent = "Annuler";
            Annuler.name = "Annuler";
            Annuler.addEventListener("click", (ev) =>{
                ev.preventDefault();
                window.location.reload();
            });

            modifCat.replaceWith(AppliquerBtn);
            AppliquerBtn.after(Annuler);
        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en modifier une autre.");
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
            Annuler.textContent = "Annuler";
            Annuler.name = "Annuler";
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

    const modifResum = document.querySelector("#modifResum");
    modifResum.addEventListener("click", (ev) => {
        if (!modif){
            modif = true;
            ev.preventDefault();
            const title = document.querySelector("#LessonTitle");
            const description = document.querySelector("#LessonDescription");
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
                    formData.append("LessonTitle", title.value);
                    formData.append("LessonDescription", description.value);
                    
                    fetch(URL, {
                        method: "POST",
                        body: formData
                    }).then(() => {
                        window.location.href = URL;
                    },"L'élément sera modifé de façon permanente.\n Etes-vous sûr d'accepter les modification ?");
                })
                

            });
            const Annuler = document.createElement("button");
            Annuler.type = "submit";
            Annuler.textContent = "Annuler";
            Annuler.name = "Annuler";
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

    const modifQuizAssoc = document.querySelector("#modifQuizAssoc");
    modifQuizAssoc.addEventListener("click", (ev) =>{
        if (!modif){
            modif = true;
            ev.preventDefault();

            const select = document.querySelector("#quizUser");
            select.disabled = false;

            const AppliquerBtn = document.createElement("button");
            AppliquerBtn.type = "submit";
            AppliquerBtn.name = "appliquerAssoc";
            AppliquerBtn.id = "appliquerAssoc";
            AppliquerBtn.textContent = "Appliquer";
            AppliquerBtn.addEventListener("click", (evt) =>{
                evt.preventDefault();
                popupValidation(() =>{
                    const formData = new FormData();

                    formData.append("appliquerAssoc", select.value);
                    
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
            Annuler.textContent = "Annuler";
            Annuler.name = "Annuler";
            Annuler.addEventListener("click", (ev) =>{
                ev.preventDefault();
                window.location.reload();
            });

            modifQuizAssoc.replaceWith(AppliquerBtn);
            AppliquerBtn.after(Annuler);
        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en modifier une autre.");
        }
    });

    function delPartFunction(ev){
        if (!modif){
            ev.preventDefault();
            const i = ev.currentTarget.value;
            popupValidation(() => {
                const formData = new FormData();
                formData.append("DelPart", i);
                
                fetch(URL, {
                    method: "POST",
                    body: formData
                }).then(() => {
                    window.location.href = URL;
                });
            },"L'élément sera supprimer définitivement, ainsi que les exemples associés. Etes-vous sûr de vouloir continuer ?");
            

        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }
    }
    const delParts = document.querySelectorAll(".delPartButton");
    for (let delPart of delParts){
        delPart.addEventListener("click", delPartFunction);
    }

    function modifPartFunction(ev){
        if(!modif){
            modif = true;
            ev.preventDefault();
            let i = ev.currentTarget.value;
            const titre = document.querySelector("#title"+i);
            const content = document.querySelector("#content"+i);

            titre.disabled = false;
            content.disabled = false;

            const AppliquerBtn = document.createElement("button");
            AppliquerBtn.type = "submit";
            AppliquerBtn.classList.add("button");
            AppliquerBtn.name = "appliquerPart";
            AppliquerBtn.id = "appliquerPart";
            AppliquerBtn.textContent = "Appliquer";
            AppliquerBtn.addEventListener("click", (evt) =>{
                evt.preventDefault();
                popupValidation(() => {

                    if (titre.value != "" && content.value != ""){
                        const formData = new FormData();
                        formData.append("appliquerPart", i);
                        formData.append("title", titre.value);
                        formData.append("content", content.value);
                        
                        fetch(URL, {
                            method: "POST",
                            body: formData
                        }).then(() => {
                            window.location.href = URL;
                        });
                    }
                    else{
                        popupAvertissement("les valeurs entrées ne doivent pas être nulles.");
                    }

                    
                    
                },"L'élément sera modifé de façon permanente.\n Etes-vous sûr d'accepter les modification ?");
                

            });
            const Annuler = document.createElement("button");
            Annuler.type = "submit";
            Annuler.classList.add("button");
            Annuler.name = "Annuler";
            Annuler.textContent = "Annuler";
            Annuler.addEventListener("click", (ev) =>{
                ev.preventDefault();
                window.location.reload();
            });
            for (let btnS of ev.currentTarget.querySelectorAll(".delPartButton")){
                btnS.remove();
            }

            ev.currentTarget.after(AppliquerBtn);
            AppliquerBtn.after(Annuler);
            ev.currentTarget.remove();

        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }
    }

    const modifParts = document.querySelectorAll(".modifPart");
    for (let mPartB of modifParts){
        mPartB.addEventListener("click", modifPartFunction);
    }

    const addPart = document.querySelector("#addPart");
    addPart.addEventListener("click", (ev) =>{
        if (!modif){
            modif = true;
            ev.preventDefault();
            const newPart = document.querySelectorAll(".newPart")[document.querySelectorAll(".newPart").length-1].cloneNode(true);
            newPart.querySelectorAll(".reponse").forEach(element => {
                supprAllChilds(element);
            });
            const modifB = newPart.querySelector(".modifPart");
            const i = parseInt(modifB.value,10) + 1;
            modifB.value = String(i);
            modifB.id = "modifPart"+i;
            modifB.addEventListener("click" , modifPartFunction);
            newPart.querySelector(".section-title").textContent = "Partie "+(i+1);
            for (let delPart of newPart.querySelectorAll(".delPartButton")){
                delPart.remove();
            }
            const titrePart = newPart.querySelector("#title"+(i-1));
            titrePart.name = "title"+i;
            titrePart.id = "title"+i;
            titrePart.value = "";

            const contentPart = newPart.querySelector("#content"+(i-1));
            contentPart.name = "content"+i;
            contentPart.id = "content"+i;
            contentPart.value = "";

            newPart.querySelector(".section-title").appendChild(modifB);
            document.querySelector("#parts").appendChild(newPart);

            modifB.click();
        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }
        
    });

    function modifierExFunction(ev, i){
        if (!modif){
            modif = true;
            ev.preventDefault();
            const k = ev.currentTarget.value;
            const textConsigne = document.querySelector("#consigne"+i+"-ex"+k);
            const textReponse = document.querySelector("#reponse"+i+"-ex"+k);

            textConsigne.disabled = false;
            textReponse.disabled = false;

            const AppliquerBtn = document.createElement("button");
            AppliquerBtn.type = "submit";
            AppliquerBtn.classList.add("button");
            AppliquerBtn.name = "appliquerEx";
            AppliquerBtn.id = "appliquerEx";
            const spanAp = document.createElement("span");
            const pAppliquer = document.createElement("p");
            pAppliquer.textContent = "Appliquer";
            AppliquerBtn.appendChild(spanAp);
            AppliquerBtn.appendChild(pAppliquer);
            AppliquerBtn.addEventListener("click", (evt) =>{
                evt.preventDefault();
                popupValidation(() => {

                    if (textConsigne.value != "" && textReponse.value != ""){
                        const formData = new FormData();
                        formData.append("appliquerEx", i);
                        formData.append("numExemple", k);
                        formData.append("textConsigne", textConsigne.value);
                        formData.append("textReponse", textReponse.value);
                            
                        fetch(URL, {
                            method: "POST",
                            body: formData
                        }).then(() => {
                            window.location.href = URL;
                        });
                    }
                    else{
                        popupAvertissement("les valeurs entrées ne doivent pas être nulles ou vides.");
                    }

                        
                        
                },"L'élément sera modifé de façon permanente.\n Etes-vous sûr d'accepter les modification ?");
                    

            });
            const Annuler = document.createElement("button");
            Annuler.type = "submit";
            Annuler.classList.add("button");
            Annuler.name = "Annuler";
            const spanAn = document.createElement("span");
            const pAnnuler = document.createElement("p");
            pAnnuler.textContent = "Annuler";
            Annuler.appendChild(spanAn);
            Annuler.appendChild(pAnnuler);
            Annuler.addEventListener("click", (ev) =>{
                ev.preventDefault();
                window.location.reload();
            });
            for (let btnS of ev.currentTarget.querySelectorAll(".supprimerEx")){
                btnS.remove();
            }
            ev.currentTarget.after(AppliquerBtn);
            AppliquerBtn.after(Annuler);
            ev.currentTarget.remove();
        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }

    }

    function suppExFunction(ev, i){
        if (!modif){
            ev.preventDefault();
            const k = ev.currentTarget.value;
            popupValidation(() => {
                const formData = new FormData();
                formData.append("delEx", i);
                formData.append("delNumEx", k);
                            
                fetch(URL, {
                    method: "POST",
                    body: formData
                }).then(() => {
                    window.location.href = URL;
                });          
            },"L'exemple sera supprimé de façon permanente. Etes-vous sûr de vouloir continuer ?");
        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer les modifications en cours avant de supprimer cet élément");
        }
    }

    const parts = document.querySelectorAll(".newPart");
    for (let part of parts){
        let i = part.getAttribute("value");
        for (ex of part.querySelectorAll(".modifierEx")){
            ex.addEventListener("click", (ev) => modifierExFunction(ev,i));
        }
        for (ex of part.querySelectorAll(".supprimerEx")){
            ex.addEventListener("click", (ev) => suppExFunction(ev,i));
        }
    }

    function addExemple(ev, i) {
        if (modif) {
            ev.preventDefault();
            popupAvertissement("Veuillez terminer les modifications en cours avant d'ajouter un exemple");
        }
        else{
            ev.preventDefault();

            const part = document.querySelector("#LessonPart" + i);
            const exemples = part.querySelectorAll(".example");
            const k = exemples.length;

            const example = document.createElement("div");
            example.classList.add("reponse", "example");
            example.style.gridRowStart = k + 1;
            example.style.gridRowEnd = k + 2;
            example.style.gridColumnStart = "2";
            example.style.gridColumnEnd = "3";

            const title = document.createElement("p");
            title.classList.add("section-title");
            title.textContent = "Exemple " + (k + 1) + " :";
            example.appendChild(title);

            const consigneWrapper = document.createElement("div");
            consigneWrapper.classList.add("textarea");
            consigneWrapper.style.width = "calc(100% - 40px)";

            const consigneSpan = document.createElement("span");
            const consigne = document.createElement("textarea");
            consigne.name = "consigne" + i + "-ex" + k;
            consigne.id = "consigne" + i + "-ex" + k;
            consigne.disabled = true;

            consigneWrapper.appendChild(consigneSpan);
            consigneWrapper.appendChild(consigne);
            example.appendChild(consigneWrapper);

            const reponseWrapper = document.createElement("div");
            reponseWrapper.classList.add("textarea");
            reponseWrapper.style.width = "calc(100% - 40px)";

            const reponseSpan = document.createElement("span");
            const reponse = document.createElement("textarea");
            reponse.name = "reponse" + i + "-ex" + k;
            reponse.id = "reponse" + i + "-ex" + k;
            reponse.disabled = true;

            reponseWrapper.appendChild(reponseSpan);
            reponseWrapper.appendChild(reponse);
            example.appendChild(reponseWrapper);

            const btns = document.createElement("div");
            btns.classList.add("exampleBtns");

            const btnModif = document.createElement("button");
            btnModif.classList.add("button", "modifierEx");
            btnModif.type = "submit";
            btnModif.id = "modifier" + i + "-ex" + k;
            btnModif.name = "modifierEx";
            btnModif.value = k;
            btnModif.innerHTML = "<span></span><p>Modifier</p>";
            btnModif.addEventListener("click", (ev) => modifierExFunction(ev, i));

            const btnSuppr = document.createElement("button");
            btnSuppr.classList.add("button", "supprimerEx");
            btnSuppr.type = "submit";
            btnSuppr.id = "supprimer" + i + "-ex" + k;
            btnSuppr.name = "supprimerEx";
            btnSuppr.value = k;
            btnSuppr.innerHTML = "<span></span><p>Supprimer l'exemple</p>";
            btnSuppr.addEventListener("click", (ev) => suppExFunction(ev, i));

            btns.appendChild(btnModif);
            btns.appendChild(btnSuppr);
            example.appendChild(btns);

            const btnAdd = part.querySelector(".addEx");
            part.insertBefore(example, btnAdd);

            btnModif.click();
        }

        
    }

    const addExs = document.querySelectorAll(".addEx");
    for (let btnAdd of addExs){
        btnAdd.addEventListener("click", (ev) => addExemple(ev,btnAdd.value));
    }

});