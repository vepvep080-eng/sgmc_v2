/**
 * Scanner Agent — gestion du flux de numérisation pour SGMC
 * Communication avec l'agent .NET local sur http://127.0.0.1:8765
 *
 * Dépendances DOM (doivent exister dans resources/views/courriers/show.blade.php) :
 *   [data-scan-panel]      → conteneur du panel numérisation
 *   [data-scanner-status]  → élément affichant le statut courant
 *   [data-start-scan]      → bouton "Démarrer le scan"
 *   [data-courrier-id]     → ID du courrier (courrier->id), porté par [data-scan-panel]
 */

const SCANNER_AGENT_BASE = 'http://127.0.0.1:8765';

/**
 * Constantes de statut internes au widget (état affiché côté client).
 * Ne pas confondre avec les valeurs brutes renvoyées par l'API
 * (App\Models\ScanJob::PENDING/SCANNING/COMPLETED/FAILED/CANCELLED/EXPIRED,
 * en minuscules : 'pending', 'scanning', 'completed', 'failed', 'cancelled', 'expired').
 */
const ScannerStatus = {
    UNKNOWN: 'unknown',
    AGENT_OFFLINE: 'agent_offline',
    SCANNING: 'scanning',
    COMPLETED: 'completed',
    FAILED: 'failed',
    EXPIRED: 'expired',
};

/**
 * Petites traductions de secours utilisées si aucun système d'i18n JS
 * n'est disponible dans le projet. __() est un helper Blade/PHP — il
 * n'existe pas côté client, donc on ne s'appuie jamais dessus ici.
 */
const FALLBACK_STRINGS = {
    'app.start_scan': 'Démarrer le scan',
    'app.scan_pending': 'En attente du scanner',
    'app.scan_completed': 'Numérisation terminée',
    'app.scan_failed': 'Échec de la numérisation',
    'app.scan_cancelled': 'Opération annulée',
    'app.scan_expired': 'Opération expirée',
    'app.scanner_unavailable': 'Agent scanner indisponible',
};

/**
 * Traduction simple : utilise window.translations si le projet en fournit
 * un (à adapter si SGMC expose déjà un mécanisme différent), sinon retombe
 * sur FALLBACK_STRINGS, puis sur la clé elle-même en dernier recours.
 * @param {string} key
 * @returns {string}
 */
function t(key) {
    if (typeof window !== 'undefined' && window.translations && window.translations[key]) {
        return window.translations[key];
    }
    return FALLBACK_STRINGS[key] || key;
}

/**
 * Effectuer une requête fetch avec timeout via AbortController.
 * @param {string} url - URL complète à appeler.
 * @param {RequestInit} [options] - Options fetch standard (method, headers, body, ...).
 * @param {number} [timeoutMs=3000] - Délai avant abandon de la requête.
 * @returns {Promise<Response>}
 */
async function fetchWithTimeout(url, options = {}, timeoutMs = 3000) {
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), timeoutMs);
    try {
        return await fetch(url, {
            ...options,
            signal: controller.signal,
        });
    } finally {
        clearTimeout(timeoutId);
    }
}

/**
 * Déterminer si un statut est terminal (plus de polling nécessaire).
 * @param {string} status - Valeur brute venant de l'API (minuscules).
 * @returns {boolean}
 */
function isTerminalStatus(status) {
    return status === 'completed' ||
        status === 'failed' ||
        status === 'cancelled' ||
        status === 'expired';
}

/**
 * Initialiser le panneau de numérisation pour un courrier donné.
 * @param {number|string} courrierId - L'ID du courrier.
 * @returns {object} API publique du widget, utile pour des tests manuels en console.
 */
export function initScannerPanel(courrierId) {
    const scanPanel = document.querySelector('[data-scan-panel]');
    if (!scanPanel) return null;

    // Création des éléments DOM si inexistants
    let statusDisplay = scanPanel.querySelector('[data-scanner-status]');
    if (!statusDisplay) {
        statusDisplay = document.createElement('div');
        statusDisplay.setAttribute('data-scanner-status', '');
        statusDisplay.className = 'status-unknown';
        scanPanel.insertBefore(statusDisplay, scanPanel.firstChild);
    }

    let startBtn = scanPanel.querySelector('[data-start-scan]');
    if (!startBtn) {
        startBtn = document.createElement('button');
        startBtn.setAttribute('data-start-scan', '');
        startBtn.className = 'btn-scan-start';
        scanPanel.insertBefore(startBtn, scanPanel.firstChild);
    }
    startBtn.textContent = t('app.start_scan');

    // État interne fermé sur ce panneau
    let currentJobId = null;
    let currentToken = null;
    let pollingTimer = null;
    let currentStatus = ScannerStatus.UNKNOWN;

    /**
     * Vérifier si l'agent .NET local répond.
     * @returns {Promise<boolean>}
     */
    async function checkAgentHealth() {
        try {
            const response = await fetchWithTimeout(`${SCANNER_AGENT_BASE}/health`, {
                method: 'GET',
                headers: { Accept: 'application/json' },
            });
            return response.ok;
        } catch (e) {
            return false;
        }
    }

    /**
     * Créer un nouveau ScanJob via Laravel.
     * @returns {Promise<{job: object, token: string}>}
     */
    async function createScanJob() {
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const response = await fetchWithTimeout(`/courriers/${courrierId}/scan-jobs`, {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfMeta ? csrfMeta.content : '',
            },
            body: JSON.stringify({}),
        });

        if (!response.ok) {
            throw new Error('Erreur création job');
        }

        const data = await response.json();
        // Le contrôleur retourne { job: {public_id, status, expires_at}, token }
        currentJobId = data.job?.public_id || data.data?.public_id || data.public_id;
        currentToken = data.token || '';

        if (!currentJobId || !currentToken) {
            throw new Error('Réponse de création de job invalide');
        }

        return { job: data.job || data.data || data, token: currentToken };
    }

    /**
     * Enregistrer le job auprès de l'agent .NET local.
     * Champs en PascalCase pour matcher RegisterJobPayload côté C#
     * (AgentServer.cs accepte aussi camelCase grâce à
     * PropertyNameCaseInsensitive = true, mais on reste cohérent avec
     * le contrat d'origine).
     * @param {string} jobId
     * @param {string} token - Le vrai token retourné par createScanJob(), jamais une valeur vide.
     * @param {string|number} courrierIdValue
     * @returns {Promise<boolean>}
     */
    async function registerWithAgent(jobId, token, courrierIdValue) {
        try {
            const response = await fetchWithTimeout(`${SCANNER_AGENT_BASE}/jobs`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    JobId: jobId,
                    Token: token,
                    CourrierId: String(courrierIdValue),
                    ExpiresMinutes: 15,
                }),
            });
            return response.ok;
        } catch (e) {
            console.error('Erreur enregistrement agent:', e);
            return false;
        }
    }

    /**
     * Mettre à jour l'affichage du statut à partir de la valeur brute API.
     * @param {string} jobStatus - 'pending' | 'scanning' | 'completed' | 'failed' | 'cancelled' | 'expired'
     */
    function updateStatus(jobStatus) {
        switch (jobStatus) {
            case 'pending':
                statusDisplay.textContent = t('app.scan_pending');
                statusDisplay.className = 'status-pending';
                currentStatus = ScannerStatus.SCANNING;
                break;
            case 'scanning':
                statusDisplay.textContent = 'Numérisation en cours';
                statusDisplay.className = 'status-scanning';
                currentStatus = ScannerStatus.SCANNING;
                break;
            case 'completed':
                statusDisplay.textContent = t('app.scan_completed');
                statusDisplay.className = 'status-completed';
                currentStatus = ScannerStatus.COMPLETED;
                break;
            case 'failed':
                statusDisplay.textContent = t('app.scan_failed');
                statusDisplay.className = 'status-failed';
                currentStatus = ScannerStatus.FAILED;
                break;
            case 'cancelled':
                statusDisplay.textContent = t('app.scan_cancelled');
                statusDisplay.className = 'status-cancelled';
                currentStatus = ScannerStatus.EXPIRED;
                break;
            case 'expired':
                statusDisplay.textContent = t('app.scan_expired');
                statusDisplay.className = 'status-expired';
                currentStatus = ScannerStatus.EXPIRED;
                break;
            default:
                statusDisplay.textContent = 'Statut inconnu';
                statusDisplay.className = 'status-unknown';
                currentStatus = ScannerStatus.UNKNOWN;
                break;
        }
    }

    /**
     * Poller le statut du scan toutes les 3 secondes jusqu'à un état terminal.
     */
    function startPolling() {
        if (pollingTimer) clearInterval(pollingTimer);
        pollingTimer = setInterval(async () => {
            try {
                const response = await fetchWithTimeout(
                    `/courriers/${courrierId}/scan-jobs/${currentJobId}`,
                    { method: 'GET', headers: { Accept: 'application/json' } },
                );

                if (!response.ok) return;

                const data = await response.json();
                const jobStatus = data?.data?.status || data?.status;

                updateStatus(jobStatus);

                if (isTerminalStatus(jobStatus)) {
                    clearInterval(pollingTimer);
                    pollingTimer = null;
                }
            } catch (e) {
                console.error('Erreur polling:', e);
            }
        }, 3000);
    }

    /**
     * Démarrer le flux de numérisation complet :
     * création du job → enregistrement auprès de l'agent → polling.
     */
    async function startScan() {
        // Empêcher double-clic pendant une opération déjà en cours ou terminée
        if (currentStatus === ScannerStatus.SCANNING || currentStatus === ScannerStatus.COMPLETED) {
            return;
        }

        statusDisplay.textContent = 'Vérification du scanner...';
        statusDisplay.className = 'status-scanning';

        // 1. Vérifier que l'agent local répond avant de créer un job Laravel inutilement
        const agentOnline = await checkAgentHealth();
        if (!agentOnline) {
            statusDisplay.textContent = t('app.scanner_unavailable');
            statusDisplay.className = 'status-agent-offline';
            currentStatus = ScannerStatus.AGENT_OFFLINE;
            return;
        }

        // 2. Créer le ScanJob côté Laravel (obtient job + vrai token)
        try {
            await createScanJob();
        } catch (e) {
            console.error('Erreur création ScanJob:', e);
            statusDisplay.textContent = 'Erreur création job';
            statusDisplay.className = 'status-failed';
            currentStatus = ScannerStatus.FAILED;
            return;
        }

        // 3. Enregistrer le job (avec le vrai token) auprès de l'agent .NET
        const registered = await registerWithAgent(currentJobId, currentToken, courrierId);
        if (!registered) {
            statusDisplay.textContent = t('app.scanner_unavailable');
            statusDisplay.className = 'status-agent-offline';
            currentStatus = ScannerStatus.AGENT_OFFLINE;
            return;
        }

        // 4. Démarrer le suivi de statut
        currentStatus = ScannerStatus.SCANNING;
        statusDisplay.textContent = 'Démarrage du scan...';
        statusDisplay.className = 'status-scanning';
        startPolling();
    }

    startBtn.addEventListener('click', () => startScan());

    return {
        checkAgentHealth,
        createScanJob,
        registerWithAgent,
        startScan,
        updateStatus,
        ScannerStatus,
    };
}

/**
 * Auto-initialisation : si un panneau [data-scan-panel][data-courrier-id]
 * est présent sur la page au chargement du DOM, on l'initialise directement
 * sans que le reste de l'application ait besoin d'appeler initScannerPanel()
 * manuellement.
 */
if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', () => {
        const panel = document.querySelector('[data-scan-panel][data-courrier-id]');
        if (panel) {
            const courrierId = panel.getAttribute('data-courrier-id');
            initScannerPanel(courrierId);
        }
    });
}

/* ---------- Fin de scanner.js ---------- */
