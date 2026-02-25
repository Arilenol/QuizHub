const selectDispo = document.querySelector("#disponibilite");
const amiList = document.querySelector(".ami-list");
const tabAmi = document.querySelectorAll('input[name="amiDispo[]"]');
const tous = document.querySelector('input[name="amiDispo[]"][value="tous"]');
let friendSearch = null;
let noResultMessage = null;

function syncSearchState(){
    if (!selectDispo || !friendSearch) {
        return;
    }
    const isDisabled = selectDispo.disabled;
    friendSearch.disabled = isDisabled;
    if (isDisabled){
        friendSearch.value = "";
        applyFriendFilter();
    }
}

function initFriendSearch(){
    if (!selectDispo || !amiList) {
        return;
    }
    const hasFriendOptions = amiList.querySelector('label.friends input[name="amiDispo[]"]');
    if (!hasFriendOptions) {
        return;
    }

    let controls = document.querySelector(".dispo-controls");
    if (!controls){
        controls = document.createElement("div");
        controls.className = "dispo-controls";
        selectDispo.before(controls);
        controls.appendChild(selectDispo);
    }

    friendSearch = document.querySelector(".friend-search");
    if (!friendSearch) {
        friendSearch = document.createElement("input");
        friendSearch.type = "text";
        friendSearch.className = "friend-search";
        friendSearch.placeholder = "Rechercher un ami...";
        friendSearch.autocomplete = "off";
        controls.appendChild(friendSearch);
    }

    noResultMessage = amiList.querySelector(".friend-search-empty");
    if (!noResultMessage){
        noResultMessage = document.createElement("p");
        noResultMessage.className = "no-content friend-search-empty";
        noResultMessage.textContent = "Aucun resultat";
        noResultMessage.hidden = true;
        amiList.after(noResultMessage);
    }

    friendSearch.addEventListener("input", applyFriendFilter);
    syncSearchState();

    const observer = new MutationObserver(syncSearchState);
    observer.observe(selectDispo, { attributes: true, attributeFilter: ["disabled"] });
}

function applyFriendFilter(){
    if (!amiList) {
        return;
    }

    const labels = amiList.querySelectorAll("label.friends");
    const search = friendSearch ? friendSearch.value.trim().toLowerCase() : "";
    let hasFriendMatch = false;
    let allFriendsLabel = null;

    for (let label of labels){
        const input = label.querySelector('input[name="amiDispo[]"]');
        if (!input) {
            continue;
        }

        if (input.value === "tous"){
            allFriendsLabel = label;
            continue;
        }

        const name = label.textContent.trim().toLowerCase();
        const visible = search === "" || name.includes(search);
        label.hidden = !visible;
        if (visible){
            hasFriendMatch = true;
        }
    }

    if (allFriendsLabel){
        allFriendsLabel.hidden = search !== "" && !hasFriendMatch;
    }

    if (noResultMessage){
        noResultMessage.hidden = search === "" || hasFriendMatch;
    }
}

function syncFriendsVisibility(){
    if (!selectDispo) {
        return;
    }
    const shouldShowFriends = selectDispo.value === "ami";
    if (amiList){
        amiList.hidden = !shouldShowFriends;
    }
    if (friendSearch){
        friendSearch.hidden = !shouldShowFriends;
        if (!shouldShowFriends){
            friendSearch.value = "";
        }
    }
    for (let ami of tabAmi){
        const label = ami.closest("label");
        if (label){
            label.hidden = !shouldShowFriends;
        }
    }
    const noFriendMessages = amiList ? amiList.querySelectorAll(".no-content") : [];
    for (let msg of noFriendMessages){
        msg.hidden = !shouldShowFriends;
    }
    if (noResultMessage){
        noResultMessage.hidden = true;
    }
    if (shouldShowFriends){
        applyFriendFilter();
    }
    syncSearchState();
}

function dispoChange(ev){
    if (ev.target.options[ev.target.selectedIndex].value === "ami"){
        syncFriendsVisibility();
    }
    else{
        for (let ami of tabAmi){
            ami.checked = false;
        }
        if (tous){
            tous.checked = true;
        }
        syncFriendsVisibility();
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
initFriendSearch();
syncFriendsVisibility();

