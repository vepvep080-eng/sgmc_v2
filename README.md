# Bureau d'Ordre — Login (Blade + Vite + Tailwind)

Fichiers front-end **prêts à intégrer** dans votre projet Laravel existant.
Aucune logique d'authentification n'est incluse — uniquement l'interface,
structurée pour brancher votre contrôleur plus tard sans toucher au markup.

## Où placer chaque fichier

Copiez ces fichiers dans votre projet Laravel en respectant les chemins
(ils correspondent déjà à la structure standard Laravel) :

```
votre-projet-laravel/
├── resources/
│   ├── css/
│   │   └── app.css                        ← remplace ou fusionne avec l'existant
│   ├── js/
│   │   └── app.js                         ← remplace ou fusionne avec l'existant
│   └── views/
│       ├── layouts/
│       │   └── guest.blade.php            ← nouveau
│       ├── partials/
│       │   ├── background-scene.blade.php ← nouveau
│       │   ├── top-bar.blade.php          ← nouveau
│       │   └── footer.blade.php           ← nouveau
│       └── auth/
│           └── login.blade.php            ← nouveau
├── tailwind.config.js                     ← remplace ou fusionne avec l'existant
├── postcss.config.js                      ← remplace ou fusionne avec l'existant
└── vite.config.js                         ← remplace ou fusionne avec l'existant
```

⚠️ Si votre projet a déjà un `tailwind.config.js` / `vite.config.js` /
`resources/js/app.js`, **ne les écrasez pas aveuglément** — fusionnez le
contenu (gardez vos couleurs/plugins existants et ajoutez les nôtres).

## Dépendances npm à installer

```bash
npm install -D tailwindcss@3.4.4 postcss autoprefixer laravel-vite-plugin
```

(`laravel-vite-plugin` est presque toujours déjà présent dans un projet
Laravel avec Vite — vérifiez `package.json` avant de le réinstaller.)

## Images

Placez vos 3 images dans `public/assets/` :

```
public/assets/logo-bureau-ordre.png
public/assets/armoiries-royaume.png
public/assets/commune-jerada.jpg
```

Les vues utilisent `asset('assets/...')`, qui pointe automatiquement vers
`public/`.

## Route (à ajouter vous-même quand vous voudrez tester la vue)

Dans `routes/web.php`, ajoutez temporairement une route d'affichage
(aucune logique, juste pour visualiser la page) :

```php
Route::view('/login', 'auth.login');
```

Le formulaire poste déjà vers `POST /login` (`action="{{ url('/login') }}"`)
et inclut `@csrf`, `old('email')` et `@error(...)` — donc le jour où vous
branchez un vrai `AuthController` (ou Laravel Breeze/Fortify), il n'y a
**aucun changement de markup à faire**, seulement la route POST et le
contrôleur à ajouter.

## Build

```bash
npm run dev     # pendant le développement
npm run build   # pour la production
```

## Cohérence avec le splash screen

La palette (`primary` #0B3D91, `secondary` #1E6F5C, `gold` #D4AF37), les
polices, les effets de verre (`sheen`, `glass-highlight`) et le système de
particules sont identiques au splash screen déjà livré. Les partials
`background-scene`, `top-bar` et `footer` sont conçus pour être réutilisés
si vous portez aussi le splash screen en Blade plus tard — demandez-le si
besoin, il n'est pas inclus dans cette livraison (vous avez demandé
uniquement la page de connexion).
