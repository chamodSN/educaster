// js/main.js — Global JavaScript for Educaster

// Hamburger menu
(function () {
    const btn = document.getElementById('hamburger');
    const links = document.querySelector('.nav-links');
    const auth = document.querySelector('.nav-auth');
    if (!btn) return;
    btn.addEventListener('click', () => {
        links && links.classList.toggle('open');
        auth && auth.classList.toggle('open');
    });
    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.navbar')) {
            links && links.classList.remove('open');
            auth && auth.classList.remove('open');
        }
    });
})();

// Active nav link highlight
(function () {
    const links = document.querySelectorAll('.nav-links a');
    const path = window.location.pathname;
    links.forEach(a => {
        if (path.includes(a.getAttribute('href').replace('/educaster/', ''))) {
            a.classList.add('active');
        }
    });
})();

// Terms page tabs
(function () {
    const tabs = document.querySelectorAll('.terms-tab');
    const panels = document.querySelectorAll('.terms-panel');
    if (!tabs.length) return;
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            panels.forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            const target = document.getElementById(tab.dataset.target);
            if (target) target.classList.add('active');
        });
    });
})();

// Auto-dismiss alerts
(function () {
    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        }, 5000);
    });
})();

// Scroll reveal animation
(function () {
    const targets = document.querySelectorAll(
        '.course-card, .prog-card, .intro-card, .stat-card, .value-card, .info-card'
    );
    if (!targets.length) return;
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    targets.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(28px)';
        observer.observe(el);
    });
})();

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const target = document.querySelector(a.getAttribute('href'));
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
    });
});