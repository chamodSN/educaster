console.log("Script loaded");

document.getElementById("popup").addEventListener("click", function () {
    document.querySelector(".popup").style.display = "flex";
});

document.getElementById("close").addEventListener("click", function () {
    document.querySelector(".popup").style.display = "none";
});

document.getElementById("updatePopupBtn").addEventListener("click", function () {
    document.querySelector(".updatePopup").style.display = "flex";
});

document.getElementById("updatePopupClose").addEventListener("click", function () {
    document.querySelector(".updatePopup").style.display = "none";
});


document.getElementById("deletePopupBtn").addEventListener("click", function () {
    document.querySelector(".deletePopup").style.display = "flex";
    console.log("popup");
});

document.getElementById("deletePopupClose").addEventListener("click", function () {
    document.querySelector(".deletePopup").style.display = "none";
    console.log("close");
});

function checkPassword() {
    var password = document.getElementById("password").value;
    var cnfrmpassword = document.getElementById("re-password").value;

    if (password != cnfrmpassword) {
        alert('Password missmatch');
        return false;
    }

    else {
        return true;
    }

}





