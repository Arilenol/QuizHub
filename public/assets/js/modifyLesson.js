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
                });
                

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


});