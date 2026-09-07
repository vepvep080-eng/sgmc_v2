/**
 * SGMC Shell — point d'entrée Electron.
 *
 * Shell minimal : une fenêtre charge l'URL du serveur Laravel central
 * (configurable — voir config.js). Aucune logique métier ici et aucun
 * pont IPC exposé au contenu distant : les seuls bridges sont attachés
 * aux fenêtres 100 % locales (configuration, écran hors-ligne).
 *
 * Fenêtres :
 *   - principale : contenu du serveur (pas de preload, sandbox active) ;
 *   - config     : écran local « adresse du serveur » (config-preload.js) ;
 *   - offline    : écran local « serveur injoignable » (offline-preload.js).
 */
'use strict';

const path = require('path');
const { app, BrowserWindow, Menu, dialog, ipcMain, net, shell } = require('electron');

const config = require('./config');

const APP_TITLE = "SGMC — Bureau d'Ordre";
const PARTITION = 'persist:sgmc'; // cookies de session persistés sur disque
const BG_COLOR = '#0B3D91';
const TEST_TIMEOUT_MS = 3000;

let mainWindow = null;
let configWindow = null;
let offlineWindow = null;

let currentServerUrl = null;
let isQuitting = false;

// Fermeture de la fenêtre hors-ligne :
//   - programmatique (Réessayer / Configurer…) → action différée à « closed » ;
//   - manuelle (croix) → l'utilisateur ferme le shell → l'application quitte.
let pendingOfflineClose = false;
let pendingOfflineAction = null;

/* ------------------------------------------------------------------ */
/* Fenêtre principale                                                  */
/* ------------------------------------------------------------------ */

function createMainWindow(serverUrl) {
    currentServerUrl = serverUrl;
    closeOffline('dismiss');

    mainWindow = new BrowserWindow({
        width: 1280,
        height: 820,
        minWidth: 1024,
        minHeight: 680,
        title: APP_TITLE,
        icon: path.join(__dirname, 'build', 'icon.png'),
        backgroundColor: BG_COLOR,
        show: false,
        webPreferences: {
            partition: PARTITION,
            contextIsolation: true,
            nodeIntegration: false,
            sandbox: true,
            spellcheck: false,
        },
    });

    mainWindow.once('ready-to-show', () => mainWindow.show());

    // Le titre de l'application ne doit pas être écrasé par les pages distantes.
    mainWindow.webContents.on('page-title-updated', (event) => event.preventDefault());

    mainWindow.webContents.on(
        'did-fail-load',
        (_event, errorCode, errorDescription, _validatedURL, isMainFrame) => {
            // -3 = ERR_ABORTED : navigation interrompue volontairement, on ignore.
            if (isMainFrame && errorCode !== -3) {
                showOffline(errorDescription);
            }
        },
    );

    // Les liens externes s'ouvrent dans le navigateur système, pas dans le shell.
    mainWindow.webContents.setWindowOpenHandler(({ url }) => {
        if (/^https?:/i.test(url)) shell.openExternal(url);
        return { action: 'deny' };
    });

    mainWindow.on('closed', () => {
        mainWindow = null;
    });

    mainWindow.loadURL(serverUrl).catch(() => showOffline("Le serveur n'a pas répondu."));
}

/** (Re)charge l'application principale sur l'URL du serveur. */
function startWithServer(serverUrl) {
    if (mainWindow && !mainWindow.isDestroyed()) {
        currentServerUrl = serverUrl;
        closeOffline('dismiss');

        mainWindow.loadURL(serverUrl)
            .then(() => {
                if (mainWindow && !mainWindow.isDestroyed()) mainWindow.show();
            })
            .catch(() => showOffline("Le serveur n'a pas répondu."));
    } else {
        createMainWindow(serverUrl);
    }
}

function reloadMainWindow() {
    if (!currentServerUrl) return;
    startWithServer(currentServerUrl);
}

/* ------------------------------------------------------------------ */
/* Écran « serveur injoignable » (fenêtre locale dédiée)               */
/* ------------------------------------------------------------------ */

function showOffline(reason) {
    if (!currentServerUrl) return;

    if (mainWindow && !mainWindow.isDestroyed()) mainWindow.hide();

    const search = new URLSearchParams({
        url: currentServerUrl,
        reason: reason || 'Le serveur est injoignable.',
    }).toString();

    if (offlineWindow && !offlineWindow.isDestroyed()) {
        offlineWindow.loadFile('screens/offline.html', { search });
        offlineWindow.show();
        return;
    }

    offlineWindow = new BrowserWindow({
        width: 560,
        height: 480,
        title: APP_TITLE,
        icon: path.join(__dirname, 'build', 'icon.png'),
        backgroundColor: BG_COLOR,
        resizable: false,
        minimizable: false,
        maximizable: false,
        show: false,
        webPreferences: {
            preload: path.join(__dirname, 'preload', 'offline-preload.js'),
            contextIsolation: true,
            nodeIntegration: false,
            sandbox: true,
            spellcheck: false,
        },
    });

    offlineWindow.once('ready-to-show', () => offlineWindow.show());

    // Fermeture manuelle (croix) = l'utilisateur ferme le shell.
    offlineWindow.on('close', (event) => {
        if (!isQuitting && !pendingOfflineClose) {
            event.preventDefault();
            app.quit(); // before-quit positionne isQuitting → fermeture possible.
        }
    });

    offlineWindow.on('closed', () => {
        offlineWindow = null;

        const action = pendingOfflineAction;
        pendingOfflineClose = false;
        pendingOfflineAction = null;

        if (action === 'retry') reloadMainWindow();
        else if (action === 'config') openConfigWindow();
        // 'dismiss' / null : rien à faire.
    });

    offlineWindow.loadFile('screens/offline.html', { search });
}

/**
 * Ferme la fenêtre hors-ligne de façon contrôlée.
 * @param {'retry'|'config'|'dismiss'} action action différée après fermeture
 */
function closeOffline(action) {
    if (!offlineWindow || offlineWindow.isDestroyed()) {
        offlineWindow = null;
        return;
    }
    pendingOfflineClose = true;
    pendingOfflineAction = action;
    offlineWindow.close();
}

/* ------------------------------------------------------------------ */
/* Écran de configuration (fenêtre locale dédiée)                      */
/* ------------------------------------------------------------------ */

function openConfigWindow() {
    if (configWindow && !configWindow.isDestroyed()) {
        configWindow.focus();
        return;
    }

    configWindow = new BrowserWindow({
        width: 520,
        height: 640,
        title: APP_TITLE,
        icon: path.join(__dirname, 'build', 'icon.png'),
        backgroundColor: BG_COLOR,
        resizable: false,
        minimizable: false,
        maximizable: false,
        show: false,
        webPreferences: {
            preload: path.join(__dirname, 'preload', 'config-preload.js'),
            contextIsolation: true,
            nodeIntegration: false,
            sandbox: true,
            spellcheck: false,
        },
    });

    configWindow.once('ready-to-show', () => configWindow.show());

    configWindow.on('closed', () => {
        configWindow = null;

        // Premier lancement annulé : aucune URL configurée, rien à afficher.
        if (!config.resolveServerUrl() && !mainWindow) app.quit();
    });

    configWindow.setMenuBarVisibility(false);
    configWindow.loadFile('screens/config-screen.html', {
        search: new URLSearchParams({ url: config.getSavedServerUrl() || '' }).toString(),
    });
}

/* ------------------------------------------------------------------ */
/* IPC (uniquement invoqué depuis les fenêtres locales)                */
/* ------------------------------------------------------------------ */

function registerIpc() {
    // Test de joignabilité du serveur (écran de configuration).
    ipcMain.handle('config:test-url', async (_event, rawUrl) => {
        const url = config.normalizeServerUrl(rawUrl);
        if (!url) return { ok: false, error: 'Adresse invalide.' };

        try {
            const response = await net.fetch(url, {
                method: 'GET',
                signal: AbortSignal.timeout(TEST_TIMEOUT_MS),
            });
            // Toute réponse HTTP prouve que le serveur est joignable.
            return { ok: true, status: response.status };
        } catch (error) {
            return { ok: false, error: error.message || 'Serveur injoignable.' };
        }
    });

    // Sauvegarde de l'URL du serveur (config.json dans userData).
    ipcMain.handle('config:save', (_event, rawUrl) => {
        try {
            const saved = config.saveServerUrl(rawUrl);
            return { ok: true, url: saved };
        } catch {
            return { ok: false, error: 'Adresse invalide.' };
        }
    });

    // Confirmation post-sauvegarde : on ouvre / recharge l'application.
    ipcMain.handle('config:done', () => {
        const url = config.resolveServerUrl();

        if (configWindow && !configWindow.isDestroyed()) configWindow.close();
        if (url) startWithServer(url);

        return { ok: true };
    });

    // Écran hors-ligne.
    ipcMain.handle('offline:retry', () => {
        closeOffline('retry');
        return { ok: true };
    });

    ipcMain.handle('offline:change-server', () => {
        closeOffline('config');
        return { ok: true };
    });
}

/* ------------------------------------------------------------------ */
/* Menu de l'application                                               */
/* ------------------------------------------------------------------ */

function buildMenu() {
    const template = [
        {
            label: 'Fichier',
            submenu: [
                { label: 'Recharger', accelerator: 'CmdOrCtrl+R', click: () => reloadMainWindow() },
                { label: 'Configurer le serveur…', accelerator: 'CmdOrCtrl+Shift+S', click: () => openConfigWindow() },
                { type: 'separator' },
                { label: 'Quitter', accelerator: 'CmdOrCtrl+Q', role: 'quit' },
            ],
        },
        {
            label: 'Affichage',
            submenu: [
                { role: 'zoomIn', label: 'Zoom avant' },
                { role: 'zoomOut', label: 'Zoom arrière' },
                { role: 'resetZoom', label: 'Taille normale' },
                { type: 'separator' },
                { role: 'togglefullscreen', label: 'Plein écran' },
                { role: 'toggleDevTools', label: 'Outils de développement' },
            ],
        },
        {
            label: 'Aide',
            submenu: [
                {
                    label: 'À propos de SGMC',
                    click: () => {
                        dialog.showMessageBox({
                            type: 'info',
                            title: APP_TITLE,
                            message: "SGMC — Bureau d'Ordre",
                            detail: [
                                `Shell desktop v${app.getVersion()}`,
                                `Serveur : ${currentServerUrl || 'non configuré'}`,
                                '',
                                'Commune de Jerada',
                            ].join('\n'),
                        });
                    },
                },
            ],
        },
    ];

    Menu.setApplicationMenu(Menu.buildFromTemplate(template));
}

/* ------------------------------------------------------------------ */
/* Cycle de vie de l'application                                       */
/* ------------------------------------------------------------------ */

app.setName('SGMC'); // userData = %APPDATA%\SGMC (config.json, cookies, …)

const gotSingleInstanceLock = app.requestSingleInstanceLock();
if (!gotSingleInstanceLock) {
    // Une instance tourne déjà : l'événement « second-instance » la remet en avant.
    app.quit();
} else {
    app.on('before-quit', () => {
        isQuitting = true;
    });

    app.on('second-instance', () => {
        if (mainWindow && !mainWindow.isDestroyed()) {
            if (mainWindow.isMinimized()) mainWindow.restore();
            mainWindow.show();
            mainWindow.focus();
        } else if (configWindow && !configWindow.isDestroyed()) {
            configWindow.focus();
        }
    });

    app.whenReady().then(() => {
        registerIpc();
        buildMenu();

        const serverUrl = config.resolveServerUrl();
        if (serverUrl) {
            createMainWindow(serverUrl);
        } else {
            // Aucune adresse connue → écran de configuration.
            openConfigWindow();
        }

        app.on('activate', () => {
            // macOS : clic sur l'icône du dock.
            if (!mainWindow && !configWindow && !offlineWindow) {
                const url = config.resolveServerUrl();
                if (url) createMainWindow(url);
                else openConfigWindow();
            }
        });
    });

    app.on('window-all-closed', () => {
        app.quit();
    });
}
