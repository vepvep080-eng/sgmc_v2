@extends('layouts.guest')

@section('title', "Bureau d'Ordre")

@section('content')
<main
    id="splash"
    class="relative min-h-screen w-full flex flex-col items-center justify-between px-6 py-10 sm:py-14 bg-gradient-to-br from-canvas-50 via-canvas-100 to-canvas-200 dark:from-midnight-950 dark:via-midnight-900 dark:to-midnight-800 bg-[length:220%_220%] animate-gradientShift transition-colors duration-700 ease-out overflow-hidden"
>
    @include('partials.background-scene')

    @include('partials.top-bar')

    <section class="relative z-10 w-full max-w-md sm:max-w-lg opacity-0 animate-scaleIn" style="animation-delay: 0.25s;">
        <div class="group relative rounded-[28px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl backdrop-saturate-150 border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark px-7 py-12 sm:px-14 sm:py-16 flex flex-col items-center text-center overflow-hidden">

            <div class="glass-highlight" aria-hidden="true"></div>
            <div class="sheen animate-sheen motion-reduce:animate-none" aria-hidden="true"></div>

            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-28 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <div class="relative w-28 h-28 sm:w-32 sm:h-32 mb-7 animate-floatY">
                <div class="absolute inset-0 rounded-full bg-gold-400/25 blur-2xl animate-pulseGlow motion-reduce:animate-none" aria-hidden="true"></div>
                <img
                    src="{{ asset('assets/logo-bureau-ordre.png') }}"
                    alt="Emblème Bureau d'Ordre"
                    class="relative w-full h-full object-contain drop-shadow-[0_6px_20px_rgba(11,61,145,0.28)]"
                />
            </div>

            <h1 class="font-display font-semibold text-4xl sm:text-5xl tracking-wide bg-gradient-to-r from-primary-500 via-primary-500 to-secondary-500 dark:from-primary-100 dark:via-primary-100 dark:to-secondary-200 bg-clip-text text-transparent">
                Bureau d'Ordre
            </h1>

            <div class="flex items-center gap-3 my-5 w-full max-w-[220px]">
                <span class="h-px flex-1 bg-gradient-to-r from-transparent to-gold-500/60"></span>
                <span class="w-1.5 h-1.5 rotate-45 bg-gold-500"></span>
                <span class="h-px flex-1 bg-gradient-to-l from-transparent to-gold-500/60"></span>
            </div>

            <p dir="rtl" lang="ar" class="font-arabic font-medium text-lg sm:text-xl text-secondary-700/90 dark:text-secondary-200/85 leading-loose [word-spacing:0.15em]">
                مكتب الضبط
            </p>

            <div class="mt-11 w-full flex flex-col items-center gap-6">
                <div
                    class="relative w-24 h-24"
                    role="progressbar"
                    aria-label="Chargement de l'application"
                    aria-valuemin="0"
                    aria-valuemax="100"
                    aria-valuenow="0"
                    id="progress-shell"
                >
                    <svg viewBox="0 0 100 100" class="w-full h-full -rotate-90">
                        <defs>
                            <linearGradient id="ring-gradient" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#D4AF37" />
                                <stop offset="100%" stop-color="#B08F26" />
                            </linearGradient>
                        </defs>
                        <circle cx="50" cy="50" r="42" fill="none" stroke="#0B3D91" stroke-opacity="0.08" stroke-width="6" />
                        <circle
                            id="progress-ring"
                            cx="50" cy="50" r="42" fill="none"
                            stroke="url(#ring-gradient)"
                            stroke-width="6"
                            stroke-linecap="round"
                            stroke-dasharray="263.9"
                            stroke-dashoffset="263.9"
                            class="progress-head"
                        />
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span id="progress-text" class="font-display font-semibold text-xl text-primary-700 dark:text-primary-100 tabular-nums">0%</span>
                    </div>
                </div>

                <div class="w-full max-w-xs">
                    <div class="relative h-1.5 w-full rounded-full bg-primary-500/10 dark:bg-white/10 overflow-hidden shadow-[inset_0_1px_2px_rgba(11,61,145,0.08)]">
                        <div
                            id="progress-bar"
                            class="h-full w-full origin-left scale-x-0 rounded-full bg-gradient-to-r from-primary-500 via-primary-500 to-secondary-500 shadow-[0_0_10px_rgba(212,175,55,0.35)] relative overflow-hidden"
                            style="transition: transform 220ms cubic-bezier(0.16, 1, 0.3, 1);"
                        >
                            <span class="absolute inset-y-0 left-0 w-1/3 bg-white/40 skew-x-[-20deg] animate-shimmer motion-reduce:animate-none"></span>
                        </div>
                    </div>
                    <p id="status-text" class="mt-3.5 text-xs sm:text-sm text-primary-700/70 dark:text-primary-100/70 font-medium tracking-wide" role="status" aria-live="polite">
                        Connexion au serveur…
                    </p>
                </div>
            </div>

            <div class="mt-9 inline-flex items-center gap-1.5 rounded-full bg-primary-500/8 dark:bg-white/5 border border-primary-500/15 dark:border-white/10 px-3.5 py-1">
                <span class="w-1.5 h-1.5 rounded-full bg-secondary-500"></span>
                <span class="text-[11px] font-semibold tracking-wider text-primary-700/80 dark:text-primary-100/75">v1.0.0</span>
            </div>
        </div>
    </section>

    @include('partials.footer')
</main>
@endsection
