(function () {
    var width = window.innerWidth;
    if (width > 640) {
        width = 500;
    }
    var r = width / 16 + "px";
    document.querySelector("html").style.fontSize = r;
})();