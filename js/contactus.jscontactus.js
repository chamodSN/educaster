// simple form UX
const form = document.getElementById("contactForm");

if (form) {
    form.addEventListener("submit", () => {
        alert("Thank you! Your inquiry has been sent.");
    });
}

// animate cards on scroll
const cards = document.querySelectorAll(".intro-card");

window.addEventListener("scroll", () => {
    cards.forEach(card => {
        const pos = card.getBoundingClientRect().top;
        if (pos < window.innerHeight - 100) {
            card.style.opacity = 1;
            card.style.transform = "translateY(0)";
        }
    });
});
