<!DOCTYPE html>
@php($rtl = app()->isLocale('ar'))
<html lang="{{ app()->getLocale() }}" dir="{{ $rtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="color-scheme" content="light dark" />
    <title>@yield('title', __('app.app_name'))</title>
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="#EEF1F6" />
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#050B18" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
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
<body class="{{ $rtl ? 'font-arabic' : 'font-sans' }} antialiased min-h-screen bg-canvas-50 dark:bg-midnight-950">

    <div class="flex min-h-screen">

        {{-- Mobile backdrop --}}
        <div
            data-sidebar-backdrop
            class="hidden fixed inset-0 z-30 bg-midnight-950/50 backdrop-blur-sm lg:hidden"
            aria-hidden="true"
        ></div>

        {{-- Sidebar --}}
        <aside
            id="app-sidebar"
            data-sidebar
            class="fixed inset-y-0 {{ $rtl ? 'right-0 translate-x-full lg:translate-x-0 border-l' : 'left-0 -translate-x-full lg:translate-x-0 border-r' }} z-40 w-72 transition-transform duration-300 ease-fluent bg-white/60 dark:bg-white/[0.04] backdrop-blur-2xl border-white/60 dark:border-white/10 flex flex-col"
        >
            <div class="flex items-center gap-3 px-6 py-5 border-b border-white/50 dark:border-white/10">
                <img src="{{ asset('assets/logo-bureau-ordre.png') }}" alt="Bureau d'Ordre" class="w-10 h-10 object-contain drop-shadow-[0_4px_10px_rgba(11,61,145,0.25)]" />
                <div class="leading-tight">
                    <p class="font-display font-semibold text-base text-primary-800 dark:text-primary-50">Bureau d'Ordre</p>
                    <p dir="rtl" lang="ar" class="font-arabic text-[11px] text-secondary-700/70 dark:text-secondary-300/60">مكتب الضبط</p>
                </div>
                <button type="button" data-sidebar-close class="ml-auto lg:hidden p-2 -mr-2 text-primary-500/60 hover:text-primary-700 dark:text-primary-100/50 dark:hover:text-primary-100" aria-label="Fermer le menu">
                    <x-icon name="x" class="w-5 h-5" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-4 py-5 flex flex-col gap-1">
                <p class="px-3 mb-1.5 text-[10px] font-semibold tracking-widest uppercase text-primary-700/40 dark:text-primary-100/35">
                    Général
                </p>

                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 ease-fluent {{ request()->routeIs('dashboard') ? 'bg-primary-500/10 text-primary-700 dark:bg-white/10 dark:text-primary-50' : 'text-primary-700/70 dark:text-primary-100/65 hover:bg-primary-500/6 dark:hover:bg-white/5' }}"
                >
                    <x-icon name="home" class="w-[18px] h-[18px] {{ request()->routeIs('dashboard') ? 'text-gold-500' : 'opacity-60' }}" />
                    {{ __('app.dashboard') }}
                </a>

                <p class="px-3 mt-4 mb-1.5 text-[10px] font-semibold tracking-widest uppercase text-primary-700/40 dark:text-primary-100/35">
                    Courrier
                </p>

                <a
                    href="{{ route('courriers.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 ease-fluent {{ request()->routeIs('courriers.*') ? 'bg-primary-500/10 text-primary-700 dark:bg-white/10 dark:text-primary-50' : 'text-primary-700/70 dark:text-primary-100/65 hover:bg-primary-500/6 dark:hover:bg-white/5' }}"
                >
                    <x-icon name="inbox" class="w-[18px] h-[18px] {{ request()->routeIs('courriers.*') ? 'text-gold-500' : 'opacity-60' }}" />
                    Courriers arrivés
                </a>
                <a
                    href="{{ route('courriers-depart.index') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 ease-fluent {{ request()->routeIs('courriers-depart.*') ? 'bg-primary-500/10 text-primary-700 dark:bg-white/10 dark:text-primary-50' : 'text-primary-700/70 dark:text-primary-100/65 hover:bg-primary-500/6 dark:hover:bg-white/5' }}"
                >
                    <x-icon name="archive" class="w-[18px] h-[18px] {{ request()->routeIs('courriers-depart.*') ? 'text-gold-500' : 'opacity-60' }}" />
                    Courriers départ
                </a>

                <a
                    href="{{ route('courriers.quick-create') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 ease-fluent {{ request()->routeIs('courriers.quick-create') ? 'bg-primary-500/10 text-primary-700 dark:bg-white/10 dark:text-primary-50' : 'text-primary-700/70 dark:text-primary-100/65 hover:bg-primary-500/6 dark:hover:bg-white/5' }}"
                >
                    <x-icon name="archive" class="w-[18px] h-[18px] {{ request()->routeIs('courriers.quick-create') ? 'text-gold-500' : 'opacity-60' }}" />
                    Scanner un document
                </a>
                @can('viewAny', \App\Models\AuditLog::class)
                    <a
                        href="{{ route('audit-logs.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors duration-200 ease-fluent {{ request()->routeIs('audit-logs.*') ? 'bg-primary-500/10 text-primary-700 dark:bg-white/10 dark:text-primary-50' : 'text-primary-700/70 dark:text-primary-100/65 hover:bg-primary-500/6 dark:hover:bg-white/5' }}"
                    >
                        <x-icon name="clock" class="w-[18px] h-[18px] {{ request()->routeIs('audit-logs.*') ? 'text-gold-500' : 'opacity-60' }}" />
                        {{ __('app.audit_log') }}
                    </a>
                @endcan


                <span
                    role="link"
                    aria-disabled="true"
                    tabindex="-1"
                    title="Bientôt disponible"
                    class="hidden"
                >
                    <x-icon name="archive" class="w-[18px] h-[18px] opacity-50" />
                    Courriers départ
                </span><span
                    role="link"
                    aria-disabled="true"
                    tabindex="-1"
                    title="Bientôt disponible"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-primary-700/35 dark:text-primary-100/25 cursor-not-allowed select-none"
                >
                    <x-icon name="building" class="w-[18px] h-[18px] opacity-50" />
                    Utilisateurs
                </span>
            </nav>

            <div class="px-4 py-4 border-t border-white/50 dark:border-white/10">
                <div class="flex items-center gap-3 px-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-primary-800 dark:text-primary-50 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-primary-700/50 dark:text-primary-100/40 truncate">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 text-xs font-semibold tracking-wide px-3.5 py-2.5 rounded-xl bg-primary-500/8 dark:bg-white/5 border border-primary-500/15 dark:border-white/10 text-primary-700/80 dark:text-primary-100/75 hover:bg-primary-500/12 dark:hover:bg-white/10 transition-colors duration-200 ease-fluent">
                        <x-icon name="logout" class="w-4 h-4" />
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main column --}}
        <div class="flex-1 flex flex-col min-w-0 {{ $rtl ? 'lg:pr-72' : 'lg:pl-72' }}">

            {{-- Topbar --}}
            <header class="sticky top-0 z-20 backdrop-blur-xl bg-white/50 dark:bg-midnight-900/50 border-b border-white/50 dark:border-white/10">
                <div class="px-5 sm:px-8 py-4 flex items-center gap-4">
                    <button type="button" data-sidebar-open class="lg:hidden p-2 -ml-2 text-primary-700/70 dark:text-primary-100/60" aria-label="Ouvrir le menu">
                        <x-icon name="menu" class="w-5 h-5" />
                    </button>

                    <h1 class="font-display font-semibold text-lg sm:text-xl text-primary-800 dark:text-primary-50">
                        @yield('page-title', __('app.dashboard'))
                    </h1>

                    <div class="{{ $rtl ? 'mr-auto' : 'ml-auto' }} flex items-center rounded-lg border border-primary-500/15 bg-white/60 text-xs font-semibold text-primary-700 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-100">
                        <a href="{{ route('language.update', 'fr') }}" class="px-2.5 py-1.5 {{ app()->isLocale('fr') ? 'bg-primary-500 text-white' : '' }}">{{ __('app.french') }}</a>
                        <a href="{{ route('language.update', 'ar') }}" class="px-2.5 py-1.5 {{ app()->isLocale('ar') ? 'bg-primary-500 text-white' : '' }}">ع</a>
                    </div>

                    <button type="button" data-theme-toggle class="rounded-lg p-2 text-primary-700/65 transition-colors hover:bg-primary-500/8 hover:text-primary-700 dark:text-primary-100/60 dark:hover:bg-white/10" aria-label="{{ __('app.theme_toggle') }}" title="{{ __('app.theme_toggle') }}">
                        <span data-theme-light aria-hidden="true">☀</span>
                        <span data-theme-dark class="hidden" aria-hidden="true">☾</span>
                    </button>

                    @php($unreadNotifications = Auth::user()->unreadNotifications()->count())
                    <a href="{{ route('notifications.index') }}" class="relative rounded-lg p-2 text-primary-700/65 transition-colors hover:bg-primary-500/8 hover:text-primary-700 dark:text-primary-100/60 dark:hover:bg-white/10 dark:hover:text-primary-50" aria-label="Notifications">
                        <x-icon name="bell" class="h-5 w-5" />
                        @if ($unreadNotifications)
                            <span class="absolute -right-1 -top-1 min-w-5 rounded-full bg-gold-500 px-1 text-center text-[10px] font-bold leading-5 text-primary-900">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                        @endif
                    </a>

                    <div class="flex items-center gap-2 text-xs text-primary-700/50 dark:text-primary-100/40">
                        <x-icon name="building" class="w-4 h-4" />
                        Commune de Jerada
                    </div>
                </div>
            </header>

            <main class="flex-1 px-5 sm:px-8 py-8">
                <div class="max-w-6xl mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <x-flash-messages />
    <x-confirm-modal />

    @stack('scripts')
</body>
</html>
