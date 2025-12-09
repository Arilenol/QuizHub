async function loadData() {
    if(window.location.pathname == "/index.html" || window.location.pathname == "/"){
        const downloaded = await localforage.keys();
        for(creationName of downloaded){
            await addCreation(creationName);
        }
    }
}

async function addCreation(name) {
    const newCreations = document.querySelector(".newCreations");
    const data = await localforage.getItem(name);
    if(data){
        const quizHTML = `
            <article onclick="window.location.href='/${data.type}.html?id=${data.id}'" class="quiz">
                <div class="quiz-cat">
                    ${data.categories.map(cat => `<span class="category">${cat}</span>`).join('')}
                </div>
                <p class="quiz-genre">${data.type}</p>
                <br>
                <p class="quiz-title">${data.name}</p>
                <br>
                <p class="quiz-description">${data.description}</p>
                <br><br>
                <div class="quiz-footer">
                    <p class="quiz-auteur">Par : ${data.creator}</p>
                </div>
            </article>
        `;
        newCreations.insertAdjacentHTML('beforeend', quizHTML);
    }
}

async function loadFlashcardData(){
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get("id");
    const questionH2 = document.querySelector("#question");
    const answerH2 = document.querySelector("#answer");
    const previousButton = document.querySelector("#previous");
    const nextButton = document.querySelector("#next");
    if(id && window.location.pathname == "/flashcard.html"){
        const data = (await localforage.getItem(id))["data"];
        const nbQuestion = parseInt(urlParams.get("nbQuestion")) || 0;
        questionH2.textContent = data[nbQuestion]["question"];
        answerH2.textContent = data[nbQuestion]["answer"];
        if(nbQuestion > 0){
            previousButton.classList.toggle("disabled");
            previousButton.addEventListener("click", () => {
                window.location.href = window.location.pathname + `?id=${id}&nbQuestion=${nbQuestion-1}`;
            });
        }
        if(nbQuestion < data.length-1){
            nextButton.addEventListener("click", () => {
                window.location.href = window.location.pathname + `?id=${id}&nbQuestion=${nbQuestion+1}`;
            });
        }
        else{
            nextButton.querySelector("p").textContent = "Terminé";
            nextButton.addEventListener("click", () => {
                window.location.href = "/";
            })
        }
        
    }
    else if(!id && window.location.pathname == "/flashcard.html"){
        window.location.href = "/";
    }
}

loadData();
loadFlashcardData();

