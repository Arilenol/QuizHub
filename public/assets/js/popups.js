function popupAvertissement(texte){
    const popupOverlay = document.createElement("div");
    popupOverlay.classList.add("popup_overlay");

    const popup = document.createElement("div");
    popup.classList.add("popup");

    const message = document.createElement("p");
    message.textContent = texte;
    message.classList.add("popup_msg");

    const btn = document.createElement("button");
    btn.classList.add("popup_btn");
    const spanBtn = document.createElement("span");
    const pBtn = document.createElement("p");
    pBtn.textContent = "OK";

    popup.appendChild(message);
    btn.appendChild(spanBtn);
    btn.appendChild(pBtn);
    popup.appendChild(btn);
    popupOverlay.appendChild(popup);

    document.querySelector("body").appendChild(popupOverlay);

    btn.addEventListener("click", () => supprAllChilds(popupOverlay));
}

function popupValidation(fonction){
    const popupOverlay = document.createElement("div");
    popupOverlay.classList.add("popup_overlay");

    const popup = document.createElement("div");
    popup.classList.add("popup");

    const message = document.createElement("p");
    message.textContent = "L'élément sera modifé de façon permanente.\n Etes-vous sûr d'accepter les modification ?";
    message.classList.add("popup_msg");

    const btnAnnuler = document.createElement("button");
    btnAnnuler.classList.add("popup_btn");
    const spanAnnul = document.createElement("span");
    const pAnnul = document.createElement("p");
    pAnnul.textContent = "Annuler";
    

    const btnValider = document.createElement("button");
    btnValider.classList.add("popup_btn");
    const spanValid = document.createElement("span");
    const pValid = document.createElement("p");
    pValid.textContent = "Valider";

    popup.appendChild(message);
    btnAnnuler.appendChild(spanAnnul);
    btnAnnuler.appendChild(pAnnul);
    popup.appendChild(btnAnnuler);
    btnValider.appendChild(spanValid);
    btnValider.appendChild(pValid);
    popup.appendChild(btnValider);
    popupOverlay.appendChild(popup);

    document.querySelector("body").appendChild(popupOverlay);

    btnAnnuler.addEventListener("click", () => supprAllChilds(popupOverlay));
    btnValider.addEventListener("click", () => {
        fonction();
        supprAllChilds(popupOverlay);
    })
}

function supprAllChilds(DOMElement){
    if (DOMElement.hasChildNodes()){
        for (let element of DOMElement.children){
            supprAllChilds(element);
        }
        DOMElement.remove();
    }
    else{
        DOMElement.remove();
    }
}