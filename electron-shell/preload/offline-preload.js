/**
 * SGMC Shell — bridge IPC pour l'écran « serveur injoignable » (page locale
 * uniquement). Ce preload n'est jamais chargé dans la fenêtre principale.
 */
'use strict';

const { contextBridge, ipcRenderer } = require('electron');

contextBridge.exposeInMainWorld('sgmcOffline', {
    /** Réessaie de se connecter au serveur courant. */
    retry: () => ipcRenderer.invoke('offline:retry'),

    /** Ouvre l'écran de configuration du serveur. */
    changeServer: () => ipcRenderer.invoke('offline:change-server'),
});
