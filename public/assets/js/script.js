window.addEventListener("load", () => {
    if("serviceWorker" in navigator){
        navigator.serviceWorker.register("sw.js");
    }
})

document.addEventListener("DOMContentLoaded", () => {
    initCheckboxSVG();
    initHeaderMenu();
    loadDownloadEvent();
});

function initCheckboxSVG(){
    const checkboxs = document.querySelectorAll(".checkbox");
    if(checkboxs.length > 0){
        fetch("assets/images/checkbox.svg")
        .then(response => response.text())
        .then(data => {
            checkboxs.forEach(checkboxDiv => {
                const parser = new DOMParser();
                const svgDoc = parser.parseFromString(data, "image/svg+xml");
                const checkbox_image = svgDoc.documentElement;
                const checkbox = checkboxDiv.querySelector("input");
                checkbox_image.innerHTML = data;
                checkbox_image.setAttribute("activated", checkbox.checked);
                const rect = checkbox_image.querySelector("rect");
                const circle = checkbox_image.querySelector("circle");
                if(checkbox.checked){
                    rect.setAttribute("fill", "#0AB1BD");
                    rect.setAttribute("stroke", "#007881");
                    circle.setAttribute("cx", 158);
                }
                else{
                    rect.setAttribute("fill", "#FFB143");
                    rect.setAttribute("stroke", "#FF9F17");
                    circle.setAttribute("cx", 58);
                }
                checkbox_image.addEventListener("click", () => {
                    if(!checkbox.disabled){
                        const isActive = checkbox_image.getAttribute("activated") === "true";
                        checkbox_image.setAttribute("activated", isActive ? "false" : "true");
                        checkbox.checked = !isActive;
                        if(!isActive){
                            rect.setAttribute("fill", "#0AB1BD");
                            rect.setAttribute("stroke", "#007881");
                            circle.setAttribute("cx", 158);
                        }
                        else{
                            rect.setAttribute("fill", "#FFB143");
                            rect.setAttribute("stroke", "#FF9F17");
                            circle.setAttribute("cx", 58);
                        }
                    }
                });
                checkboxDiv.appendChild(checkbox_image);
            });
        });
    }
}

function loadDownloadEvent(){
    const downloadButton = document.querySelectorAll(".download-button");
    downloadButton.forEach(element => {
        element.addEventListener("click", (e) => {download(e, element.value)});
    });
}

function initHeaderMenu(){
    const openButton = document.querySelector("#openMenu");
    const closeButton = document.querySelector("#closeMenu");
    const menu = document.querySelector("#menu");

    openButton.addEventListener("click", () => {
        menu.style.left = "0px";
    });

    closeButton.addEventListener("click", () => {
        menu.style.left = "-100dvw";
    });
    
}

function resetCheckboxSVG() {
    const checkboxs = document.querySelectorAll(".checkbox");

    checkboxs.forEach(checkboxDiv => {

        checkboxDiv.querySelectorAll("svg").forEach(svg => svg.remove());

        //const input = checkboxDiv.querySelector("input[type='checkbox']");

    });
}

async function download(e, id){
    e.stopPropagation();
    if(id != null){
        const result = await (await fetch("/getFlashcardData.php?id=" + id)).text();
        localforage.setItem(JSON.parse(result)["id"], JSON.parse(result));
        alert("La flashcard à bien été télécharger")
    }
}