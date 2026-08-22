<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="color-scheme" content="light dark" />
    <title>@yield('title', "Bureau d'Ordre")</title>
    <meta name="description" content="Bureau d'Ordre — Commune de Jerada" />
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="#EEF1F6" />
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#050B18" />
    <script>
        (() => {
            if (localStorage.getItem('sgmc-theme') === 'dark') document.documentElement.classList.add('dark');
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Inter:wght@400;500;600;700&family=Noto+Naskh+Arabic:wght@500;600;700&display=swap"
        rel="stylesheet"
    />
    <link rel="icon" href="data:," />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased overflow-hidden">
    @yield('content')
    @stack('scripts')
</body>
</html>
