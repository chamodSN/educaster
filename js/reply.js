document.getElementById("replyPopupBtn").addEventListener("click", function () {
    document.querySelector(".replyPopup").style.display = "flex";
    console.log("CLICK");
});

document.getElementById("replyPopupClose").addEventListener("click", function () {
    document.querySelector(".replyPopup").style.display = "none";
    console.log("CLOSE CLICK")
});