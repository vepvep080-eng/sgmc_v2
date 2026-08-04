/**
 * Bureau d'Ordre — shared front-end interactions
 * Commune de Jerada
 *
 * UI-only: no authentication logic lives here. The login form posts
 * natively to the Laravel route once it's wired up on the backend.
 */

import flatpickr from 'flatpickr';
import { French } from 'flatpickr/dist/l10n/fr.js';
import 'flatpickr/dist/flatpickr.min.css';

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

/* ---------------------------------------------------- */
/* Date pickers — Flatpickr, French locale, dd/mm/yyyy     */
/* shown to the user, submitted to Laravel as Y-m-d.       */
/* Applies to any input marked with [data-flatpickr].       */
/* ---------------------------------------------------- */
function initDatePickers() {
  const dateInputs = document.querySelectorAll('[data-flatpickr]');
  if (!dateInputs.length) return;

  dateInputs.forEach((input) => {
    // Preserve every existing Tailwind class on the visible input that
    // Flatpickr creates (altInput) so styling stays identical.
    const originalClasses = input.className;

    flatpickr(input, {
      locale: French,
      dateFormat: 'Y-m-d', // what actually gets submitted to Laravel (kept in the original, hidden input)
      altInput: true,
      altFormat: 'd/m/Y', // what the user sees
      altInputClass: originalClasses,
      allowInput: true,
      disableMobile: true, // force the Flatpickr UI even on mobile, so locale/digits stay consistent
    });
  });
}

/* ---------------------------------------------------- */
/* Splash screen — circular ring + linear bar + redirect  */
/* No-ops automatically on pages without #progress-ring.  */
/* ---------------------------------------------------- */
const CIRCLE_CIRCUMFERENCE = 263.9; // 2 * PI * r(42), matches the SVG in splash.blade.php
const SPLASH_REDIRECT_PATH = '/login';
const SPLASH_DURATION_MS = 3000;
const SPLASH_EXIT_TRANSITION_MS = 620;
const SPLASH_READY_HOLD_MS = 700;

const STATUS_STEPS = [
  { at: 0, fr: 'Connexion au serveur…' },
  { at: 25, fr: 'Chargement des modules…' },
  { at: 55, fr: 'Vérification des données…' },
  { at: 85, fr: 'Finalisation…' },
  { at: 100, fr: 'Prêt' }
];

function initSplashSequence() {
  const progressRing = document.getElementById('progress-ring');
  const splash = document.getElementById('splash');
  if (!progressRing || !splash) return; // not on the splash page — nothing to do

  const progressShell = document.getElementById('progress-shell');
  const progressText = document.getElementById('progress-text');
  const progressBar = document.getElementById('progress-bar');
  const statusText = document.getElementById('status-text');

  progressRing.style.strokeDasharray = String(CIRCLE_CIRCUMFERENCE);
  progressRing.style.strokeDashoffset = String(CIRCLE_CIRCUMFERENCE);

  function statusLabelFor(percent) {
    let label = STATUS_STEPS[0].fr;
    for (const step of STATUS_STEPS) {
      if (percent >= step.at) label = step.fr;
    }
    return label;
  }

  function setProgress(percent) {
    const clamped = Math.max(0, Math.min(100, percent));
    const rounded = Math.round(clamped);

    progressRing.style.strokeDashoffset = String(CIRCLE_CIRCUMFERENCE * (1 - clamped / 100));
    if (progressBar) progressBar.style.transform = `scaleX(${clamped / 100})`;
    if (progressText) progressText.textContent = `${rounded}%`;
    if (progressShell) progressShell.setAttribute('aria-valuenow', String(rounded));
    if (statusText) statusText.textContent = statusLabelFor(rounded);

    return rounded;
  }

  function showReadyState() {
    if (statusText) {
      statusText.innerHTML =
        'Prêt <span class="inline-flex items-center justify-center ml-1 align-middle text-secondary-600 dark:text-secondary-300 font-semibold">✓</span>';
      statusText.classList.add('text-secondary-600', 'dark:text-secondary-300');
    }
    if (progressBar) {
      progressBar.classList.remove('from-primary-500', 'via-primary-500', 'to-secondary-500');
      progressBar.classList.add('from-secondary-500', 'via-secondary-500', 'to-secondary-500');
    }
    progressRing.setAttribute('stroke', '#1E6F5C');
    if (progressShell) {
      progressShell.setAttribute('aria-valuenow', '100');
      progressShell.setAttribute('aria-label', 'Chargement terminé');
    }
  }

  function redirectToLogin() {
    splash.classList.add('splash-exit');
    window.setTimeout(() => {
      window.location.href = SPLASH_REDIRECT_PATH;
    }, SPLASH_EXIT_TRANSITION_MS);
  }

  const start = performance.now();

  function tick(now) {
    const elapsed = now - start;
    const t = Math.min(1, elapsed / SPLASH_DURATION_MS);
    const eased = 1 - Math.pow(1 - t, 3.2);
    setProgress(eased * 100);

    if (t < 1) {
      window.requestAnimationFrame(tick);
    } else {
      setProgress(100);
      showReadyState();
      window.setTimeout(redirectToLogin, SPLASH_READY_HOLD_MS);
    }
  }

  window.requestAnimationFrame(tick);
}

/* ---------------------------------------------------- */
/* Sidebar — mobile off-canvas drawer                     */
/* ---------------------------------------------------- */
function initSidebar() {
  const sidebar = document.querySelector('[data-sidebar]');
  const backdrop = document.querySelector('[data-sidebar-backdrop]');
  const openButtons = document.querySelectorAll('[data-sidebar-open]');
  const closeButtons = document.querySelectorAll('[data-sidebar-close]');
  if (!sidebar || !backdrop) return;

  function open() {
    sidebar.classList.remove('-translate-x-full');
    backdrop.classList.remove('hidden');
  }

  function close() {
    sidebar.classList.add('-translate-x-full');
    backdrop.classList.add('hidden');
  }

  openButtons.forEach((btn) => btn.addEventListener('click', open));
  closeButtons.forEach((btn) => btn.addEventListener('click', close));
  backdrop.addEventListener('click', close);
}

/* ---------------------------------------------------- */
/* Shared delete-confirmation modal                        */
/* Trigger buttons: [data-confirm-delete][data-url][data-message] */
/* ---------------------------------------------------- */
function initConfirmModal() {
  const modal = document.getElementById('confirm-delete-modal');
  if (!modal) return;

  const form = modal.querySelector('[data-confirm-delete-form]');
  const messageEl = modal.querySelector('[data-modal-message]');
  const backdrop = modal.querySelector('[data-modal-backdrop]');
  const closeButtons = modal.querySelectorAll('[data-modal-close]');

  function openModal(url, message) {
    form.action = url;
    if (message) messageEl.textContent = message;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
  }

  function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
  }

  document.querySelectorAll('[data-confirm-delete]').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      openModal(trigger.dataset.url, trigger.dataset.message);
    });
  });

  closeButtons.forEach((btn) => btn.addEventListener('click', closeModal));
  backdrop?.addEventListener('click', closeModal);

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
  });
}

/* ---------------------------------------------------- */
/* Toast notifications — auto-dismiss + manual close       */
/* ---------------------------------------------------- */
function initToasts() {
  document.querySelectorAll('[data-toast]').forEach((toast) => {
    const delay = Number(toast.dataset.autoDismiss || 4000);

    const dismiss = () => {
      toast.style.transition = 'opacity 300ms ease, transform 300ms ease';
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-8px)';
      window.setTimeout(() => toast.remove(), 300);
    };

    toast.querySelectorAll('[data-dismiss-toast]').forEach((btn) => btn.addEventListener('click', dismiss));
    window.setTimeout(dismiss, delay);
  });
}

/* ---------------------------------------------------- */
/* File input — shows the chosen file name                */
/* ---------------------------------------------------- */
function initFileInputs() {
  document.querySelectorAll('[data-file-input]').forEach((input) => {
    const wrapper = input.closest('div')?.parentElement;
    const nameEl = wrapper?.querySelector('[data-file-name]');
    if (!nameEl) return;

    input.addEventListener('change', () => {
      if (input.files?.length) {
        nameEl.textContent = input.files[0].name;
        nameEl.classList.remove('hidden');
      } else {
        nameEl.classList.add('hidden');
      }
    });
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initParticles('particle-field');
  initPasswordToggles();
  initDatePickers();
  initSplashSequence();
  initSidebar();
  initConfirmModal();
  initToasts();
  initFileInputs();
});
