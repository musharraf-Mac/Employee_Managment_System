function closef() {
    window.history.back();
}

function showTerms() {
    const modal = document.getElementById("termsModal");
    const modalContent = modal.querySelector(".terms-modal");
    modal.classList.add("active");
    modalContent.classList.remove("animate__zoomOut");
    modalContent.classList.add("animate__zoomIn");
}

function closeTerms() {
    const modal = document.getElementById("termsModal");
    const modalContent = modal.querySelector(".terms-modal");
    modalContent.classList.remove("animate__zoomIn");
    modalContent.classList.add("animate__zoomOut");
    modalContent.addEventListener("animationend", function handler() {
        modal.classList.remove("active");
        modalContent.classList.remove("animate__zoomOut");
    }, { once: true });
}

// Close modal when clicking outside
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("termsModal");
    if (modal) {
        modal.addEventListener("click", function(e) {
            if (e.target === modal) {
                closeTerms();
            }
        });
    }
});
    
