/**
 * Bureau d'Ordre — shared front-end interactions
 * Commune de Jerada
 *
 * UI-only: no authentication logic lives here. The login form posts
 * natively to the Laravel route once it's wired up on the backend.
 */
/* ---------------------------------------------------- */
/* Floating particles — pure CSS animation, JS only      */
/* generates randomized positions/timings once on load.  */
/* ---------------------------------------------------- */
function particleCount(width) {
    if (width < 480) return 6;
    if (width < 900) return 10;
    return 16;
}
function initParticles(containerId = 'particle-field') {
    const container = document.getElementById(containerId);
    if (!container) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    const fragment = document.createDocumentFragment();
    const count = particleCount(window.innerWidth);

    for (let i = 0; i < count; i += 1) {
        const particle = document.createElement('span');
        particle.className = 'particle';

        const size = (Math.random() * 3.2 + 2).toFixed(1);
        const left = (Math.random() * 100).toFixed(1);
        const duration = (Math.random() * 10 + 16).toFixed(1);
        const delay = (Math.random() * -24).toFixed(1);
        const drift = (Math.random() * 60 - 30).toFixed(0);
        const opacity = (Math.random() * 0.3 + 0.2).toFixed(2);
        const scale = (Math.random() * 0.6 + 0.7).toFixed(2);

        particle.style.setProperty('--p-size', `${size}px`);
        particle.style.setProperty('--p-left', `${left}%`);
        particle.style.setProperty('--p-duration', `${duration}s`);
        particle.style.setProperty('--p-delay', `${delay}s`);
        particle.style.setProperty('--p-drift', `${drift}px`);
        particle.style.setProperty('--p-opacity', opacity);
        particle.style.setProperty('--p-scale', scale);

        fragment.appendChild(particle);
    }

    container.appendChild(fragment);
}
/* ---------------------------------------------------- */
/* Password show/hide toggle — pure UI, no validation     */
/* ---------------------------------------------------- */
function initPasswordToggles() {
    document.querySelectorAll('[data-password-toggle]').forEach((button) => {
        const targetId = button.getAttribute('data-password-toggle');
        const input = document.getElementById(targetId);
        if (!input) return;

        button.addEventListener('click', () => {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            button.setAttribute('aria-pressed', String(isHidden));
            button.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
            button.querySelectorAll('[data-icon]').forEach((icon) => icon.classList.toggle('hidden'));
        });
    });
}
document.addEventListener('DOMContentLoaded', () => {
    initParticles('particle-field');
    initPasswordToggles();
});
