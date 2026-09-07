/**
 * SGMC Shell — résolution et persistance de l'URL du serveur Laravel central.
 *
 * Ordre de priorité (du plus fort au plus faible) :
 *   1. Argument CLI        --server-url=http://192.168.1.10:8000
 *   2. Variable d'env.     SGMC_SERVER_URL=http://192.168.1.10:8000
 *   3. Fichier de config   %APPDATA%\SGMC\config.json  { "serverUrl": "..." }
 *   4. null                → l'application ouvre l'écran de configuration
 *
 * Le fichier de configuration est un simple JSON lisible/éditable à la main
 * (aucune dépendance type electron-store n'est nécessaire).
 */
'use strict';

const fs = require('fs');
const path = require('path');
const { app } = require('electron');

const CONFIG_FILENAME = 'config.json';

/** Chemin complet du fichier config.json (dans le dossier userData). */
function configFilePath() {
    return path.join(app.getPath('userData'), CONFIG_FILENAME);
}

function readConfig() {
    try {
        const parsed = JSON.parse(fs.readFileSync(configFilePath(), 'utf8'));
        return parsed && typeof parsed === 'object' ? parsed : {};
    } catch {
        // Fichier absent ou corrompu → config vide (l'écran de configuration
        // se réaffichera au prochain lancement).
        return {};
    }
}

function writeConfig(values) {
    const dir = app.getPath('userData');
    fs.mkdirSync(dir, { recursive: true });
    fs.writeFileSync(configFilePath(), `${JSON.stringify(values, null, 4)}\n`, 'utf8');
}

/**
 * Normalise une URL de serveur saisie :
 *   - trim, ajout de « http:// » si aucun scheme ;
 *   - http/https uniquement, pas de chemin ni de query/hash (URL de base) ;
 *   - renvoie l'origine (ex. « http://192.168.1.10:8000 ») ou null si invalide.
 */
function normalizeServerUrl(input) {
    if (typeof input !== 'string') return null;

    let value = input.trim();
    if (!value) return null;
    if (!/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\//.test(value)) value = `http://${value}`;

    let parsed;
    try {
        parsed = new URL(value);
    } catch {
        return null;
    }

    if (parsed.protocol !== 'http:' && parsed.protocol !== 'https:') return null;
    if (parsed.pathname !== '/' && parsed.pathname !== '') return null;
    if (parsed.search || parsed.hash) return null;

    return parsed.origin;
}

/** URL fournie en ligne de commande (--server-url=…), sinon null. */
function fromCommandLine() {
    for (const arg of process.argv) {
        if (arg.startsWith('--server-url=')) {
            return normalizeServerUrl(arg.slice('--server-url='.length));
        }
    }
    return null;
}

/** URL fournie via la variable d'environnement SGMC_SERVER_URL, sinon null. */
function fromEnvironment() {
    return normalizeServerUrl(process.env.SGMC_SERVER_URL || '');
}

/** URL enregistrée dans config.json, sinon null. */
function getSavedServerUrl() {
    return normalizeServerUrl(readConfig().serverUrl || '');
}

/** Résout l'URL du serveur selon l'ordre de priorité. Renvoie null si aucune. */
function resolveServerUrl() {
    return fromCommandLine() || fromEnvironment() || getSavedServerUrl() || null;
}

/** Sauvegarde l'URL (normalisée) dans config.json et la renvoie. */
function saveServerUrl(url) {
    const normalized = normalizeServerUrl(url);
    if (!normalized) throw new Error('invalid_url');

    const values = readConfig();
    values.serverUrl = normalized;
    writeConfig(values);

    return normalized;
}

/** Vrai si l'URL active vient d'une surcharge (CLI/env) non persistée. */
function isOverridden() {
    return Boolean(fromCommandLine() || fromEnvironment());
}

module.exports = {
    CONFIG_FILENAME,
    configFilePath,
    normalizeServerUrl,
    getSavedServerUrl,
    resolveServerUrl,
    saveServerUrl,
    isOverridden,
};
