# SGMC Scanner Agent (.NET)

Composant local Windows qui fait le pont entre les scanners physiques et
l'application SGMC (Laravel). Il n'écoute que sur `127.0.0.1` et n'est
jamais exposé sur le réseau.

```
[Navigateur SGMC] ──fetch local──► [Agent .NET  127.0.0.1:8765] ──upload──► [Laravel serveur central]
                                            │
                                            ▼
                                    [Scanner (WIA / réseau)]
```

## Build et exécution

```bash
# Développement (depuis ce dossier)
dotnet run --project src

# Publication (exécutable autonome)
dotnet publish src -c Release -r win-x64 --self-contained
# → src/bin/Release/net8.0/win-x64/publish/src.exe
```

## Configuration (`config.json`)

Le fichier est **créé automatiquement avec les valeurs par défaut** au
premier démarrage, à côté de l'exécutable (`AppContext.BaseDirectory`) :

- en développement (`dotnet run`) : `src/bin/Debug/net8.0/config.json` ;
- en version publiée : dans le dossier de publication, à côté de `src.exe`.

L'agent regarde aussi dans le répertoire de travail courant si aucun fichier
n'existe à côté de l'exécutable (pratique pour `dotnet run`).

### Format

```json
{
  "allowedOrigins": [
    "http://localhost:8000",
    "http://127.0.0.1:8000",
    "http://192.168.1.10:8000"
  ],
  "laravelServerUrl": "http://localhost:8000"
}
```

| Clé | Rôle |
|---|---|
| `allowedOrigins` | Origines des pages web autorisées à dialoguer avec l'agent (**CORS**). Format `http://hôte[:port]`, une par entrée. La casse et le `/` final sont ignorés. |
| `laravelServerUrl` | URL de base du serveur Laravel vers laquelle l'agent téléverse les documents numérisés (sans chemin ni `/` final). |

Une entrée invalide est ignorée avec un `warning` dans les logs ; un fichier
JSON illisible est signalé mais **jamais écrasé** (valeurs par défaut
utilisées en mémoire).

### Ajouter l'IP du serveur central (déploiement LAN)

Quand l'application est servie via l'IP du PC serveur (ex.
`http://192.168.1.10:8000`), le navigateur envoie l'origine
`http://192.168.1.10:8000` — elle doit figurer dans `allowedOrigins`, sinon
la numérisation est bloquée par le navigateur.

1. Ouvrir le fichier `config.json` (voir emplacement ci-dessus).
2. Ajouter l'origine du serveur central **et** pointer `laravelServerUrl`
   vers ce même serveur :

   ```json
   {
     "allowedOrigins": [
       "http://localhost:8000",
       "http://127.0.0.1:8000",
       "http://192.168.1.10:8000"
     ],
     "laravelServerUrl": "http://192.168.1.10:8000"
   }
   ```

3. **Redémarrer l'agent.** Au démarrage, les logs affichent la liste des
   origines effectivement actives — à vérifier en cas de doute :

   ```
   Scanner Agent config file: C:\...\config.json
   CORS allowed origins (3):
     - http://localhost:8000
     - http://127.0.0.1:8000
     - http://192.168.1.10:8000
   Laravel server URL (scan upload): http://192.168.1.10:8000
   Scanner Agent listening on http://127.0.0.1:8765
   ```

> Chaque poste employé exécute sa propre instance de l'agent : `config.json`
> se configure **sur chaque PC**, pas sur le serveur central.

## Sécurité

- Binding local uniquement : `http://127.0.0.1:8765` (jamais exposé au réseau).
- Jetons de scan à usage unique, hashés côté Laravel, expirant après 15 minutes.
- L'agent ne stocke aucun identifiant SGMC.

## Endpoints locaux

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/health` | Vérification de disponibilité |
| GET | `/scanners` | Liste des scanners détectés |
| POST | `/jobs` | Enregistrement d'un job de scan (`job_id` + `token`) |
| POST | `/scan` | Exécution du scan puis téléversement vers Laravel |
| OPTIONS | `*` | Préflight CORS |

## État d'implémentation

- Détection des scanners : **WIA** (COM) et réseau — fonctionnelle sous Windows.
- Scan réel (`ScanAsync`) : **non implémenté** (stub `NotImplementedException`),
  à valider avec du matériel réel sur un poste Windows.
