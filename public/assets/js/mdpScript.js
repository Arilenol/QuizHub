//constante
const eye = document.getElementById("eyeMdp");
const eye1 = document.getElementById("eyeVerif");
const mdp = document.getElementById("password");
const mdpVerif = document.getElementById("passwordVerif");

//listener
eye.addEventListener("click", () => {
    mdp.type = mdp.type === "password" ? "text" : "password";
    eye.classList.toggle("fa-eye");
    eye.classList.toggle("fa-eye-slash");
});

if (eye1!==null){
    eye1.addEventListener("click", () => {
        mdpVerif.type = mdpVerif.type === "password" ? "text" : "password";
        eye1.classList.toggle("fa-eye");
        eye1.classList.toggle("fa-eye-slash");
    });
}
