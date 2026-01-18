function cartesManquantes(cartes){
    for (let carte of cartes){
        const textareas = carte.querySelectorAll("textarea");
        for (let textarea of textareas){
            if (textarea.value == ""){
                return true;
            }
        }
    }
    return false;
}

function disponibiliteErreur(dispo){
    const select = dispo.querySelector("select");
    let option = null;
    for (let optionS of select.querySelectorAll("option")){
        if (optionS.selected){
            option = optionS;
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
boutonCreate.addEventListener("click", (ev) => {
    const titre = document.querySelector("[name='FlashcardTitle']");
    const description = document.querySelector("[name='FlashcardDescription']");
    const allCategories = document.querySelectorAll("[name='categories[]']");
    const categories = []
    for (let categorie of allCategories){
        if (categorie.checked){
            categories.push(categorie);
        }
    }
    const cartes = document.querySelectorAll(".Carte");
    const dispo = document.querySelector(".disponibilite");
    console.log(categories);

    if (titre.value == ""){
        popupAvertissement("Le champ titre est vide. Vous ne pouvez pas créer les flashcards.");
        ev.preventDefault();
    }
    else if (description.value == ""){
        popupAvertissement("Le champ description est vide. Vous ne pouvez pas créer les flashcards.");
        ev.preventDefault();
    }
    else if (categories.length == 0){
        popupAvertissement("Vous n'avez sélectionner aucunes catégories. Veuillez en sélectionner au moins une.");
        ev.preventDefault();
    }
    else if (cartesManquantes(cartes)){
        popupAvertissement("Une carte contient une question ou une réponse vide.");
        ev.preventDefault();
    }
    else if (disponibiliteErreur(dispo)){
        popupAvertissement("Vous ne pouvez pas mettre la disponibilité en 'ami' en ne sélectionnant aucun ami. Si vous avez des amis, sélectionnez des amis. Sinon, ayez des amis.");
        ev.preventDefault();
    }
});
