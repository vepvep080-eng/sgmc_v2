@extends('layouts.guest')

@section('title', "Connexion — Bureau d'Ordre")

@section('content')
<main class="relative min-h-screen w-full flex flex-col items-center justify-between px-6 py-10 sm:py-14 bg-canvas-50 dark:bg-midnight-950 transition-colors duration-200 ease-out overflow-hidden">

    @include('partials.background-scene')

    @include('partials.top-bar')

    {{-- Login card --}}
    <section class="relative z-10 w-full max-w-md opacity-0 animate-scaleIn sm:max-w-xl" style="animation-delay: 0.2s;">
        <div class="group relative rounded-2xl bg-white dark:bg-white/[0.06] dark:backdrop-blur-2xl border border-outline-soft/60 dark:border-white/10 shadow-card dark:shadow-glass-lg-dark overflow-hidden">

            {{-- Institutional top accent bar --}}
            <div class="h-2 w-full bg-gradient-to-r from-primary via-gold-600 to-primary" aria-hidden="true"></div>

            {{-- Header --}}
            <div class="px-7 sm:px-10 pt-8 sm:pt-10 pb-8 flex flex-col items-center text-center">
                <div class="relative w-24 h-24 sm:w-28 sm:h-28 mb-6 rounded-full bg-surface-low dark:bg-white/[0.04] border border-outline-soft/70 dark:border-white/10 shadow-md flex items-center justify-center overflow-hidden">
                    <img
                        src="{{ asset('assets/logo-bureau-ordre.png') }}"
                        alt="Emblème Bureau d'Ordre"
                        class="relative w-full h-full object-contain p-2 drop-shadow-[0_6px_20px_rgba(8,32,24,0.20)]"
                    />
                </div>

                <h1 class="font-sans font-bold text-2xl sm:text-3xl tracking-tight text-primary dark:text-primary-100">
                    Bureau d'Ordre
                </h1>
                <p dir="rtl" lang="ar" class="font-arabic font-medium text-base sm:text-lg text-ink-variant dark:text-primary-100/80 mt-2">
                    مكتب الضبط
                </p>
            </div>

            {{--
                Form skeleton only — no controller/route logic yet.
                Points to POST /login and follows Laravel conventions
                (@csrf, old(), $errors) so it drops straight into
                Breeze/Fortify/your own AuthController later with no
                markup changes.
            --}}
            <form method="POST" action="{{ route('login') }}" class="px-7 sm:px-10 pb-9 flex flex-col gap-5 text-left" novalidate>
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-[13px] font-medium text-ink dark:text-primary-100/85 mb-1.5">
                        {{ __('app.email') }}
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-outline">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2.5" />
                                <path d="M3.5 6.5 12 13 20.5 6.5" />
                            </svg>
                        </span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            autocomplete="username"
                            placeholder="prenom.nom@jerada.ma"
                            required
                            class="w-full h-11 rounded-lg bg-surface-low dark:bg-white/[0.05] border border-outline-soft/80 dark:border-white/10 pl-11 pr-4 text-sm text-ink dark:text-primary-50 placeholder:text-outline dark:placeholder:text-primary-100/35 outline-none transition-all duration-200 ease-fluent focus:bg-white dark:focus:bg-white/[0.08] focus:border-primary focus:ring-[3px] focus:ring-primary/10"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-[#BA2525] dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-[13px] font-medium text-ink dark:text-primary-100/85 mb-1.5">
                        {{ __('app.password') }}
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-outline">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="4.5" y="10.5" width="15" height="10" rx="2.5" />
                                <path d="M8 10.5V7.5a4 4 0 0 1 8 0v3" />
                            </svg>
                        </span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            placeholder="••••••••"
                            required
                            class="w-full h-11 rounded-lg bg-surface-low dark:bg-white/[0.05] border border-outline-soft/80 dark:border-white/10 pl-11 pr-11 text-sm text-ink dark:text-primary-50 placeholder:text-outline dark:placeholder:text-primary-100/35 outline-none transition-all duration-200 ease-fluent focus:bg-white dark:focus:bg-white/[0.08] focus:border-primary focus:ring-[3px] focus:ring-primary/10"
                        />
                        <button
                            type="button"
                            data-password-toggle="password"
                            aria-label="Afficher le mot de passe"
                            aria-pressed="false"
                            class="absolute inset-y-0 right-2 flex items-center p-2 rounded-lg text-outline hover:text-primary transition-colors duration-200 ease-fluent"
                        >
                            <svg data-icon="eye" viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg data-icon="eye-off" class="hidden" viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 3l18 18" />
                                <path d="M10.6 5.2A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a17.6 17.6 0 0 1-3.4 4.3M6.6 6.6C4 8.3 2 12 2 12s3.6 7 10 7c1.5 0 2.9-.4 4.1-1" />
                                <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-xs text-[#BA2525] dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me + forgot password --}}
                <div class="flex items-center justify-between text-xs sm:text-sm pt-0.5">
                    <label for="remember" class="inline-flex items-center gap-2 cursor-pointer select-none text-ink-variant dark:text-primary-100/75 hover:text-ink dark:hover:text-primary-50 transition-colors">
                        <input id="remember" name="remember" type="checkbox" class="peer sr-only" />
                        <span
                            aria-hidden="true"
                            class="w-4 h-4 rounded-[4px] border border-outline dark:border-white/25 bg-white dark:bg-white/5 flex items-center justify-center transition-all duration-200 ease-fluent peer-checked:bg-primary peer-checked:border-primary dark:peer-checked:border-primary peer-focus-visible:ring-2 peer-focus-visible:ring-gold-400/60"
                        >
                            <svg viewBox="0 0 12 10" width="10" height="8" class="opacity-0 peer-checked:opacity-100" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 5l3 3 7-7" />
                            </svg>
                        </span>
                        {{ __('app.remember_me') }}
                    </label>

                    {{-- Placeholder — no password-reset flow yet --}}
                    <span
                        role="link"
                        aria-disabled="true"
                        tabindex="-1"
                        title="Bientôt disponible"
                        class="text-outline dark:text-primary-100/40 cursor-not-allowed select-none"
                    >
                        Mot de passe oublié ?
                    </span>
                </div>

                <button
                    type="submit"
                    class="mt-1 relative w-full h-12 rounded-lg bg-primary text-white text-sm font-semibold tracking-wide shadow-[0_10px_22px_-10px_rgba(13,74,54,0.55)] transition-all duration-200 ease-fluent hover:bg-primary-600 hover:shadow-[0_14px_28px_-10px_rgba(13,74,54,0.6)] active:scale-[0.99] focus-visible:ring-2 focus-visible:ring-gold-500/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-transparent"
                >
                    {{ __('app.sign_in') }}
                </button>
            </form>

            {{-- Secure connection indicator --}}
            <div class="mx-7 sm:mx-10 mb-8 mt-1 p-3.5 bg-surface-low dark:bg-white/[0.05] border border-outline-soft/50 dark:border-white/10 rounded-lg flex items-center justify-center gap-2">
                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" class="text-primary shrink-0">
                    <path d="M12 3.5 19 6.5V11c0 5-3 8.5-7 9.5-4-1-7-4.5-7-9.5V6.5Z" />
                    <path d="M9 12l2 2 4-4" />
                </svg>
                <span class="text-[11px] font-medium tracking-wide text-ink-variant dark:text-primary-100/70">Connexion sécurisée</span>
            </div>
        </div>
    </section>

    @include('partials.footer')
</main>
@endsection
