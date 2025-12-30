let modif = false
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector("form");
    const idQuiz = parseInt(document.querySelector("#idFlashcard").value,10);
    const URL = form.getAttribute("action")+"?page=flashcard&categorie=modify&id="+idQuiz;

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
            const title = document.querySelector("#FlashcardTitle");
            const description = document.querySelector("#FlashcardDescription");
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
                    formData.append("FlashcardTitle", title.value);
                    formData.append("FlashcardDescription", description.value);
                    
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

            modifResum.replaceWith(Appliquer);
            Appliquer.after(Annuler);

        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }
    });

    function delCardFunction(ev){
        if (!modif){
            ev.preventDefault();
            const i = ev.currentTarget.value;
            popupValidation(() => {
                const formData = new FormData();
                formData.append("DelCard", i);
                
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
    const delCards = document.querySelectorAll(".delCardButton");
    for (let delCard of delCards){
        delCard.addEventListener("click", delCardFunction);
    }

    function modifCardFunction(ev){
        if(!modif){
            modif = true;
            ev.preventDefault();
            let i = ev.currentTarget.value;
            const titre = document.querySelector("#question"+i);
            const content = document.querySelector("#response"+i);

            titre.disabled = false;
            content.disabled = false;

            const AppliquerBtn = document.createElement("button");
            AppliquerBtn.type = "submit";
            AppliquerBtn.classList.add("button");
            AppliquerBtn.name = "appliquerCard";
            AppliquerBtn.id = "appliquerCard";
            AppliquerBtn.textContent = "Appliquer";
            AppliquerBtn.addEventListener("click", (evt) =>{
                evt.preventDefault();
                popupValidation(() => {

                    if (titre.value != "" && content.value != ""){
                        const formData = new FormData();
                        formData.append("appliquerCard", i);
                        formData.append("cardQuestion", titre.value);
                        formData.append("cardResponse", content.value);
                        
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
            for (let btnS of ev.currentTarget.querySelectorAll(".delCardButton")){
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

    const modifCards = document.querySelectorAll(".modifCard");
    for (let mCardB of modifCards){
        mCardB.addEventListener("click", modifCardFunction);
    }

    const addCard = document.querySelector("#addCard");
    addCard.addEventListener("click", (ev) =>{
        if (!modif){
            ev.preventDefault();
            const newCard = document.querySelectorAll(".newCard")[document.querySelectorAll(".newCard").length-1].cloneNode(true);
            const modifB = newCard.querySelector(".modifCard");
            const i = parseInt(modifB.value,10) + 1;
            modifB.value = String(i);
            modifB.id = "modifCard"+i;
            modifB.addEventListener("click" , modifCardFunction);
            newCard.querySelector(".section-title").textContent = "Carte "+(i+1);
            for (let delPart of newCard.querySelectorAll(".delCardButton")){
                delPart.remove();
            }
            const titrePart = newCard.querySelector("#question"+(i-1));
            titrePart.name = "question"+i;
            titrePart.id = "question"+i;
            titrePart.value = "";

            const contentPart = newCard.querySelector("#response"+(i-1));
            contentPart.name = "response"+i;
            contentPart.id = "response"+i;
            contentPart.value = "";

            newCard.querySelector(".section-title").appendChild(modifB);
            document.querySelector("#cards").appendChild(newCard);

            modifB.click();
        }
        else{
            ev.preventDefault();
            popupAvertissement("Veuillez terminer la modification en cours avant d'en effectuer une autre.");
        }
        
    });
});