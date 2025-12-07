const boutonCat = document.querySelector("#hiddenCategories");
const divCat = document.querySelector(".categoriesList");

function cacherCategories(ev) {
    ev.preventDefault();
    
    if(divCat.hidden == true){
        ev.target.textContent = "◀";
        divCat.hidden = false;
    }
    else{
        ev.target.textContent = "▼";
        divCat.hidden = true;
    }
}

boutonCat.addEventListener("click", cacherCategories);