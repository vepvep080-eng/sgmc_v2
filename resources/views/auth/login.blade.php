@extends('layouts.guest')

@section('title', "Connexion — Bureau d'Ordre")

@section('content')
<main class="relative min-h-screen w-full flex flex-col items-center justify-between px-6 py-10 sm:py-14 bg-canvas-50 dark:bg-midnight-950 transition-colors duration-200 ease-out overflow-hidden">

    @include('partials.background-scene')

    @include('partials.top-bar')

    {{-- Login card --}}
    <section class="relative z-10 w-full max-w-md opacity-0 animate-scaleIn" style="animation-delay: 0.2s;">
        <div class="group relative rounded-[30px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl backdrop-saturate-150 border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark px-7 py-10 sm:px-11 sm:py-12 flex flex-col items-center text-center overflow-hidden">

            {{-- glass reflection layers --}}
            <div class="glass-highlight" aria-hidden="true"></div>
            <div class="sheen animate-sheen motion-reduce:animate-none" aria-hidden="true"></div>

            {{-- gold hairline accent along the top --}}
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-28 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            {{-- Application logo --}}
            <div class="relative w-20 h-20 mb-5 animate-floatY">
                <div class="absolute inset-0 rounded-full bg-gold-400/25 blur-2xl animate-pulseGlow motion-reduce:animate-none" aria-hidden="true"></div>
                <img
                    src="{{ asset('assets/logo-bureau-ordre.png') }}"
                    alt="Emblème Bureau d'Ordre"
                    class="relative w-full h-full object-contain drop-shadow-[0_6px_20px_rgba(11,61,145,0.28)]"
                />
            </div>

            {{-- Title --}}
            <h1 class="font-display font-semibold text-3xl sm:text-4xl tracking-wide bg-gradient-to-r from-primary-500 via-primary-500 to-secondary-500 dark:from-primary-100 dark:via-primary-100 dark:to-secondary-200 bg-clip-text text-transparent">
                Bureau d'Ordre
            </h1>
            <p dir="rtl" lang="ar" class="font-arabic font-medium text-base sm:text-lg text-secondary-700/90 dark:text-secondary-200/85 mt-1.5">
                مكتب الضبط
            </p>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6 w-full max-w-[200px]">
                <span class="h-px flex-1 bg-gradient-to-r from-transparent to-gold-500/60"></span>
                <span class="w-1.5 h-1.5 rotate-45 bg-gold-500"></span>
                <span class="h-px flex-1 bg-gradient-to-l from-transparent to-gold-500/60"></span>
            </div>

            {{--
                Form skeleton only — no controller/route logic yet.
                Points to POST /login and follows Laravel conventions
                (@csrf, old(), $errors) so it drops straight into
                Breeze/Fortify/your own AuthController later with no
                markup changes.
            --}}
            <form method="POST" action="{{ route('login') }}" class="w-full flex flex-col gap-4 text-left" novalidate>
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        {{ __('app.email') }}
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-primary-500/50 dark:text-primary-100/40">
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
                            class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 pl-11 pr-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 shadow-[inset_0_1px_2px_rgba(11,61,145,0.06)] outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 focus:bg-white/80 dark:focus:bg-white/[0.1]"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div>
                    <label for="password" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        {{ __('app.password') }}
                    </label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-primary-500/50 dark:text-primary-100/40">
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
                            class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 pl-11 pr-11 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 shadow-[inset_0_1px_2px_rgba(11,61,145,0.06)] outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 focus:bg-white/80 dark:focus:bg-white/[0.1]"
                        />
                        <button
                            type="button"
                            data-password-toggle="password"
                            aria-label="Afficher le mot de passe"
                            aria-pressed="false"
                            class="absolute inset-y-0 right-2 flex items-center p-2 rounded-lg text-primary-500/50 dark:text-primary-100/40 hover:text-primary-700 dark:hover:text-primary-100 hover:bg-primary-500/8 dark:hover:bg-white/10 transition-colors duration-200 ease-fluent"
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
                        <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me + forgot password --}}
                <div class="flex items-center justify-between text-xs sm:text-sm pt-1">
                    <label for="remember" class="inline-flex items-center gap-2 cursor-pointer select-none text-primary-700/75 dark:text-primary-100/70">
                        <input id="remember" name="remember" type="checkbox" class="peer sr-only" />
                        <span
                            aria-hidden="true"
                            class="w-4 h-4 rounded-[5px] border border-primary-500/30 dark:border-white/20 bg-white/60 dark:bg-white/5 flex items-center justify-center transition-all duration-200 ease-fluent peer-checked:bg-gradient-to-br peer-checked:from-primary-500 peer-checked:to-secondary-500 peer-checked:border-transparent peer-focus-visible:ring-2 peer-focus-visible:ring-gold-400/50"
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
                        class="text-primary-500/40 dark:text-primary-100/30 cursor-not-allowed select-none"
                    >
                        Mot de passe oublié ?
                    </span>
                </div>

                <button
                    type="submit"
                    class="mt-2 relative w-full rounded-xl bg-gradient-to-r from-primary-500 via-primary-500 to-secondary-500 text-white text-sm font-semibold tracking-wide py-3.5 shadow-[0_10px_30px_-8px_rgba(11,61,145,0.5)] transition-all duration-300 ease-fluent hover:shadow-gold-glow hover:scale-[1.02] active:scale-[0.99] focus-visible:ring-2 focus-visible:ring-gold-400/60"
                >
                    {{ __('app.sign_in') }}
                </button>
            </form>

            {{-- Secure connection indicator --}}
            <div class="mt-6 inline-flex items-center gap-1.5 text-secondary-600/80 dark:text-secondary-300/70">
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3.5 19 6.5V11c0 5-3 8.5-7 9.5-4-1-7-4.5-7-9.5V6.5Z" />
                    <path d="M9 12l2 2 4-4" />
                </svg>
                <span class="text-[11px] font-medium tracking-wide">Connexion sécurisée</span>
            </div>
        </div>
    </section>

    @include('partials.footer')
</main>
@endsection
