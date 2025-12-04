
const paramTimer = document.querySelector("#timer");
const timerP = document.querySelector(".timerP");
const timerV = document.querySelector("[name='timerValue']");

function fpHidden(ev){
      
    timerP.hidden = !timerP.hidden;
    timerV.hidden = !timerV.hidden;
}


paramTimer.addEventListener("click", fpHidden);


