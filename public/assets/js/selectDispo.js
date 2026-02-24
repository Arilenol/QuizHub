const selectDispo = document.querySelector("#disponibilite");
const tabAmi = document.querySelectorAll('input[name="amiDispo[]"]');
const tous = document.querySelector('input[name="amiDispo[]"][value="tous"]');

function syncFriendsVisibility(){
    if (!selectDispo) {
        return;
    }
    const shouldShowFriends = selectDispo.value === "ami";
    for (let ami of tabAmi){
        ami.closest("label").hidden = !shouldShowFriends;
    }
}

function dispoChange(ev){
    if (ev.target.options[ev.target.selectedIndex].value === "ami"){
        for (let ami of tabAmi){
            ami.closest("label").hidden = false;
        }
    }
    else{
        for (let ami of tabAmi){
            ami.closest("label").hidden = true;
        }
        for (let ami of tabAmi){
            ami.checked = false;
        }
        if (tous){
            tous.checked = true;
        }
    }
}

function checkBoxClick(ev){
    if (!tous){
        return;
    }
    if (ev.target.value == "tous"){
        for (let ami of tabAmi){
            ami.checked = false;
        }
        ev.target.checked = true;
    }
    else{
        tous.checked = false;
    }
    let oneCheck = false;
    for (let ami of tabAmi){
        if (ami.checked){
            oneCheck = true;
        }
    }
    if (!oneCheck){
        tous.checked = true;
    }
}

if (selectDispo){
    selectDispo.addEventListener("change", dispoChange);
}
for (let ami of tabAmi){
    ami.addEventListener("click",checkBoxClick);
}
syncFriendsVisibility();
