// js/main.js — Global JavaScript for Educaster

// Hamburger menu
(function () {
    const btn = document.getElementById('hamburger');
    const links = document.querySelector('.nav-links');
    const auth = document.querySelector('.nav-auth');
    if (!btn) return;
    btn.addEventListener('click', () => {
        const isOpen = links && links.classList.toggle('open');
        auth && auth.classList.toggle('open');
        btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.navbar')) {
            links && links.classList.remove('open');
            auth && auth.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
        }
    });
})();

// Active nav link highlight (skipped when server already marked one .active,
// e.g. via BASE_PATH-aware PHP checks in the header partials)
(function () {
    if (document.querySelector('.nav-links a.active')) return;
    const links = document.querySelectorAll('.nav-links a');
    const path = window.location.pathname;
    links.forEach(a => {
        const href = a.getAttribute('href');
        if (href && href !== '#' && path.endsWith(href.split('/').pop())) {
            a.classList.add('active');
        }
    });
})();

// Terms page tabs
// FIX: originally the JS added/removed an "active" class on the tab
// panels, but css/terms.css only ever checked for an "open" class —
// so clicking a tab never visibly changed anything. Both sides now
// agree on ".terms-tab"/".terms-panel" + the "active" class.
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
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-6px)';
            setTimeout(() => el.remove(), 500);
        }, 6000);
    });
})();

// Generic scroll-reveal animation — any element with class="reveal"
(function () {
    const targets = document.querySelectorAll('.reveal');
    if (!targets.length) return;
    if (!('IntersectionObserver' in window)) {
        targets.forEach(el => el.classList.add('visible'));
        return;
    }
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    targets.forEach(el => observer.observe(el));
})();

// Smooth scroll for same-page anchors only (guards against bare "#")
document.querySelectorAll('a[href^="#"]').forEach(a => {
    const href = a.getAttribute('href');
    if (!href || href.length < 2) return;
    a.addEventListener('click', e => {
        const target = document.querySelector(href);
        if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
    });
});

// Animated number counters — used on home.php stats band.
// Usage: <span class="counter" data-target="120" data-suffix="+">0</span>
(function () {
    const counters = document.querySelectorAll('.counter');
    if (!counters.length) return;
    const animate = (el) => {
        const target = parseInt(el.dataset.target || '0', 10);
        const suffix = el.dataset.suffix || '';
        const step = Math.max(1, Math.ceil(target / 60));
        let current = 0;
        const tick = () => {
            current = Math.min(current + step, target);
            el.textContent = current + suffix;
            if (current < target) requestAnimationFrame(tick);
        };
        tick();
    };
    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) { animate(entry.target); obs.unobserve(entry.target); }
            });
        }, { threshold: 0.4 });
        counters.forEach(c => obs.observe(c));
    } else {
        counters.forEach(animate);
    }
})();