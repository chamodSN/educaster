// js/quiz.js — Quiz countdown timer
(function () {
    const display = document.getElementById('time');
    const timer = document.getElementById('timer');
    const form = document.getElementById('quizForm');
    if (!display) return;

    let remaining = 30 * 60; // 30 minutes

    const interval = setInterval(() => {
        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        display.textContent = (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;

        if (remaining <= 120) timer.classList.add('urgent');
        if (remaining <= 0) { clearInterval(interval); if (form) form.submit(); }
        remaining--;
    }, 1000);
})();