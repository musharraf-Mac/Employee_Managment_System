function closef() {
    window.history.back();
}
function showTerms() {
    const termElem = document.getElementById("terms");
    termElem.classList.remove("termsh");
    termElem.classList.add("animate__zoomIn");
}
function closeTerms() {
    const termElem = document.getElementById("terms");
    termElem.classList.remove("animate__zoomIn");
    termElem.classList.add("animate__zoomOut");
    termElem.addEventListener("animationend", function handler() {
        termElem.classList.add("termsh");                      // hide after animation
        termElem.classList.remove("animate__zoomOut");
        termElem.removeEventListener("animationend", handler);
    }, { once: true });
}
    
