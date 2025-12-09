window.addEventListener("load", () => {
    if("serviceWorker" in navigator){
        navigator.serviceWorker.register("sw.js");
    }
})

window.addEventListener("DOMContentLoaded", () => {
    initCheckboxSVG();
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
                });
                checkboxDiv.appendChild(checkbox_image);
            });
        });
    }
}

function resetCheckboxSVG() {
    const checkboxs = document.querySelectorAll(".checkbox");

    checkboxs.forEach(checkboxDiv => {

        checkboxDiv.querySelectorAll("svg").forEach(svg => svg.remove());

        //const input = checkboxDiv.querySelector("input[type='checkbox']");

    });

}

download = async function(id){
    if(id != null){
        const result = await (await fetch("/getFlashcardData.php?id=" + id)).text();
        localforage.setItem(JSON.parse(result)["id"], JSON.parse(result));
        console.log(id + " downloaded")
    }
}