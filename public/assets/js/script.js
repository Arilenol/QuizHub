window.addEventListener("DOMContentLoaded", () => {
    const checkboxs = document.querySelectorAll(".checkbox");
    if(checkboxs.length > 0){
        fetch("assets/images/checkbox.svg")
        .then(response => response.text())
        .then(data => {
            checkboxs.forEach(checkbox => {
                const parser = new DOMParser();
                const svgDoc = parser.parseFromString(data, "image/svg+xml");
                const checkbox_image = svgDoc.documentElement;
                checkbox_image.innerHTML = data;
                checkbox_image.setAttribute("activated", "false");
                checkbox_image.addEventListener("click", () => {
                    const isActive = checkbox_image.getAttribute("activated") === "true";
                    checkbox_image.setAttribute("activated", isActive ? "false" : "true");
                    const rect = checkbox_image.querySelector("rect");
                    const circle = checkbox_image.querySelector("circle");
                    const checkbox = checkbox_image.parentElement.querySelector("input");
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
                checkbox.appendChild(checkbox_image);
            });
        });
    }
    
});