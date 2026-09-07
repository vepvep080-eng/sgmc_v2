/**
 * SGMC Shell — bridge IPC pour l'écran de configuration (page locale uniquement).
 *
 * Expose une API minimale via contextBridge. Ce preload n'est JAMAIS chargé
 * dans la fenêtre principale : le contenu du serveur Laravel n'a accès à
 * aucun IPC.
 */
'use strict';

const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('sgmcConfig', {
    /** Teste la joignabilité d'une URL serveur → { ok, status?, error? } */
    testConnection: (url) => ipcRenderer.invoke('config:test-url', url),

    /** Sauvegarde l'URL → { ok, url?, error? } */
    save: (url) => ipcRenderer.invoke('config:save', url),

    /** Confirme la fin de la configuration (ferme l'écran, ouvre l'application). */
    done: () => ipcRenderer.invoke('config:done'),
});
