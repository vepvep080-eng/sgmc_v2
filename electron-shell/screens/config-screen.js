/**
 * SGMC Shell — logique de l'écran de configuration.
 * Communication avec le process principal via window.sgmcConfig (preload).
 */
'use strict';

const form = document.getElementById('config-form');
const urlInput = document.getElementById('server-url');
const errorEl = document.getElementById('error');
const statusEl = document.getElementById('status');
const testBtn = document.getElementById('test-btn');
const saveBtn = document.getElementById('save-btn');

// URL éventuellement déjà enregistrée, passée en paramètre ?url=
const preset = new URLSearchParams(window.location.search).get('url') || '';
if (preset) urlInput.value = preset;

function showError(message) {
    errorEl.textContent = message;
    errorEl.classList.remove('hidden');
}

function clearMessages() {
    errorEl.classList.add('hidden');
    statusEl.classList.add('hidden');
}

/** Normalise côté client : renvoie l'URL ou null. */
function normalizeUrl(input) {
    let value = String(input || '').trim();
    if (!value) return null;
    if (!/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\//.test(value)) value = `http://${value}`;

    try {
        const parsed = new URL(value);
        if (parsed.protocol !== 'http:' && parsed.protocol !== 'https:') return null;
        if (parsed.pathname !== '/' && parsed.pathname !== '') return null;
        return parsed.origin;
    } catch {
        return null;
    }
}

function setBusy(busy) {
    testBtn.disabled = busy;
    saveBtn.disabled = busy;
}

testBtn.addEventListener('click', async () => {
    clearMessages();

    const url = normalizeUrl(urlInput.value);
    if (!url) {
        showError('Adresse invalide. Exemple attendu : http://192.168.1.10:8000');
        return;
    }

    setBusy(true);
    statusEl.textContent = 'Test de connexion en cours…';
    statusEl.className = 'status pending';

    try {
        const result = await window.sgmcConfig.testConnection(url);

        if (result && result.ok) {
            statusEl.textContent = `✓ Serveur joignable (HTTP ${result.status})`;
            statusEl.className = 'status ok';
        } else {
            statusEl.textContent = `⚠ Serveur injoignable — ${result?.error || 'erreur inconnue'}`;
            statusEl.className = 'status warn';
        }
    } catch {
        statusEl.textContent = '⚠ Test impossible.';
        statusEl.className = 'status warn';
    } finally {
        setBusy(false);
    }
});

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearMessages();

    const url = normalizeUrl(urlInput.value);
    if (!url) {
        showError('Adresse invalide. Exemple attendu : http://192.168.1.10:8000');
        return;
    }

    setBusy(true);
    saveBtn.textContent = 'Enregistrement…';

    const result = await window.sgmcConfig.save(url);
    if (!result || !result.ok) {
        setBusy(false);
        saveBtn.textContent = 'Enregistrer et continuer';
        showError(result?.error || "Impossible d'enregistrer l'adresse.");
        return;
    }

    // Sauvegarde réussie → le process principal ouvre la fenêtre principale.
    await window.sgmcConfig.done();
});
