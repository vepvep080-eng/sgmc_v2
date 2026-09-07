# SGMC — Shell Desktop (Electron)

Shell Electron léger pour **SGMC (Bureau d'Ordre — Commune de Jerada)**.

Electron n'exécute **ni PHP ni MySQL** : la fenêtre charge simplement
l'application Laravel hébergée sur le serveur central du réseau local.
L'agent scanner .NET continue de tourner localement sur chaque poste
(`http://127.0.0.1:8765`), exactement comme en version web.

```
┌──────────────────────┐         ┌────────────────────────────────┐
│  Poste employé       │  HTTP   │  PC serveur central (LAN)      │
│  ┌────────────────┐  │ ──────► │  Laravel + MySQL (port 8000)   │
│  │ Shell Electron │  │         └────────────────────────────────┘
│  │ (cette app)    │  │
│  └────────────────┘  │         ┌────────────────────────────────┐
│  Agent scanner .NET  │ ──────► │  Scanner (127.0.0.1:8765)      │
└──────────────────────┘  local  └────────────────────────────────┘
```

## Prérequis

- **Node.js 18+** (LTS recommandé) — pour le développement et le build uniquement.
- Le serveur central doit exécuter Laravel et écouter sur le réseau :

  ```bash
  php artisan serve --host=0.0.0.0 --port=8000
  ```

  (ou tout serveur web PHP : Apache/Nginx + PHP-FPM). Le port 8000 doit être
  autorisé en entrée dans le pare-feu Windows du poste serveur.

## Développement

```bash
cd electron-shell
npm install
npm start
```

Au premier lancement, un écran de configuration demande l'adresse du serveur
(ex. `http://192.168.1.10:8000`). Le bouton **Tester la connexion** vérifie la
joignabilité avant d'enregistrer.

## Configuration de l'adresse serveur

Ordre de priorité (du plus fort au plus faible) :

1. Argument de ligne de commande : `--server-url=http://192.168.1.10:8000`
2. Variable d'environnement : `SGMC_SERVER_URL=http://192.168.1.10:8000`
3. Fichier de configuration : `%APPDATA%\SGMC\config.json`

   ```json
   { "serverUrl": "http://192.168.1.10:8000" }
   ```

4. Écran de configuration interactif (premier lancement).

Pour changer l'adresse plus tard sans réinstaller : menu
**Fichier → Configurer le serveur…** (`Ctrl+Shift+S`), ou modifier directement
`config.json`, ou le supprimer pour repasser par l'écran de configuration.

## Icône

Le logo officiel SGMC (vert/blanc/doré) est fourni manuellement :
`build/icon.png` (PNG **512×512**). `electron-builder` le convertit
automatiquement en `.ico` Windows lors du packaging — il est ainsi utilisé
partout : fenêtre, barre des tâches, raccourcis (bureau + menu Démarrer) et
installateur.

Pour remplacer le logo : déposer le nouveau fichier en 512×512 à
`build/icon.png` puis relancer `npm run dist`. L'écran de configuration
affiche ce même fichier (`screens/config-screen.html` → `../build/icon.png`).

## Packaging Windows (.exe)

```bash
npm run dist
```

Produit : `dist/SGMC-Setup-1.0.0.exe` (installateur **NSIS x64**) :

- installation par utilisateur (aucun droit administrateur requis) ;
- raccourcis bureau + menu Démarrer nommés **SGMC** ;
- dossier d'installation modifiable au moment de l'installation.

Chaque employé installe ensuite ce `.exe` et renseigne (une seule fois)
l'adresse du serveur au premier lancement.

## Session et cookies

La fenêtre utilise la partition persistante `persist:sgmc` : les cookies
(stockés dans `%APPDATA%\SGMC\Partitions\sgmc`) survivent aux redémarrages.
Avec « Rester connecté » coché sur la page de connexion Laravel (cookie
`remember_web`), l'utilisateur reste connecté d'un lancement à l'autre. La
déconnexion classique dans l'application fonctionne normalement.

## Dépannage

| Symptôme | Solution |
|---|---|
| Écran « Serveur injoignable » | Vérifier que le poste serveur est allumé, que Laravel tourne et que le pare-feu autorise le port 8000. Bouton **Réessayer**. |
| Changement d'IP du serveur | Menu **Fichier → Configurer le serveur…**, ou éditer `%APPDATA%\SGMC\config.json`. |
| Réinitialisation complète | Fermer l'application puis supprimer le dossier `%APPDATA%\SGMC`. |
| Exports PDF / Excel | La boîte de dialogue de téléchargement standard s'ouvre (fichiers dompdf / maatwebsite servis par Laravel). |
| Deux lancements simultanés | L'instance existante est remise au premier plan (verrou d'instance unique). |
| L'app ne démarre pas en dev | Vérifier `npm install` puis relancer `npm start` depuis `electron-shell/`. |

## Points d'attention côté serveur (hors périmètre de ce shell)

1. **CORS de l'agent scanner .NET** :
   `scanner-agent-dotnet/src/ScannerAgent/AgentServer.cs` n'autorise que
   `http://localhost:8000` et `http://127.0.0.1:8000` (`AllowedOrigins`).
   Quand les postes accèdent au serveur via son IP (ex.
   `http://192.168.1.10:8000`), l'origine change : il faudra ajouter cette
   origine à `AllowedOrigins` pour que la numérisation continue de fonctionner
   depuis le shell comme depuis un navigateur.
2. **APP_URL / Vite** : vérifier que les assets buildés (manifeste Vite) sont
   référencés relativement, sinon définir `ASSET_URL` côté Laravel.
3. **Pare-feu** du poste serveur : autoriser le port 8000 en entrée.
