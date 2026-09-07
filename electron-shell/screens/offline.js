/**
 * SGMC Shell — écran « serveur injoignable ».
 * L'URL et la raison arrivent en paramètres de requête (?url=…&reason=…).
 * Les actions passent par window.sgmcOffline (preload offline-preload.js).
 */
'use strict';

const params = new URLSearchParams(window.location.search);

document.getElementById('target-url').textContent = params.get('url') || '(non configurée)';
document.getElementById('reason').textContent = params.get('reason') || 'Le serveur est injoignable.';

document.getElementById('retry-btn').addEventListener('click', () => {
    window.sgmcOffline.retry();
});

document.getElementById('config-btn').addEventListener('click', () => {
    window.sgmcOffline.changeServer();
});
