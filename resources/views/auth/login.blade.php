@extends('layouts.guest')

@section('title', __('app.login_system_title') . " — Bureau d'Ordre")

@section('content')
@php($rtl = app()->isLocale('ar'))
<main dir="{{ $rtl ? 'rtl' : 'ltr' }}" class="relative min-h-screen w-full flex flex-col items-center justify-center px-4 py-10 sm:px-6 sm:py-14 bg-canvas-50 dark:bg-midnight-950 transition-colors duration-200 ease-out overflow-y-auto">

    @include('partials.background-scene')

    {{-- Moroccan pattern overlay (maquette: login) --}}
    <div class="pointer-events-none absolute inset-0 opacity-40" aria-hidden="true">
        <svg class="w-full h-full text-primary-500" height="100%" width="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern height="60" id="moroccan-pattern" patternUnits="userSpaceOnUse" width="60">
                    <path d="M30 0 L60 30 L30 60 L0 30 Z" fill="none" stroke="currentColor" stroke-opacity="0.12" stroke-width="0.75"></path>
                    <circle cx="30" cy="30" fill="none" r="12" stroke="currentColor" stroke-opacity="0.15" stroke-width="0.5"></circle>
                </pattern>
            </defs>
            <rect fill="url(#moroccan-pattern)" height="100%" width="100%"></rect>
        </svg>
    </div>

    {{-- Login card (maquette hierarchy + logo + titles) --}}
    <section class="relative z-10 w-full max-w-xl opacity-0 animate-scaleIn" style="animation-delay: 0.15s;">
        <div class="relative rounded-xl bg-white dark:bg-white/[0.06] dark:backdrop-blur-2xl border border-outline-soft/60 dark:border-white/10 shadow-card dark:shadow-glass-lg-dark overflow-hidden">

            <div class="h-2 w-full bg-gradient-to-r from-primary via-secondary to-primary" aria-hidden="true"></div>

            <div class="px-6 sm:px-12 pt-8 pb-6 flex flex-col items-center text-center">
                <p class="text-[11px] sm:text-xs font-medium tracking-wider text-ink-variant dark:text-primary-100/70 text-center mb-4">{{ __('app.login_hierarchy') }}</p>
                <div class="relative w-24 h-24 sm:w-28 sm:h-28 mb-4 p-1 rounded-full bg-surface-low dark:bg-white/[0.04] border border-outline-soft/70 dark:border-white/10 shadow-md flex items-center justify-center overflow-hidden">
                    <img src="{{ asset('assets/logo-bureau-ordre.png') }}" alt="شعار مكتب الضبط - جماعة جرادة" class="relative z-10 w-full h-full object-contain" />
                </div>
                <h1 class="font-bold text-xl sm:text-2xl leading-9 tracking-tight text-primary dark:text-primary-100">{{ __('app.login_system_title') }}</h1>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-[11px] font-bold tracking-widest uppercase text-secondary-600 bg-secondary-300/40 px-2.5 py-0.5 rounded-full">Bureau d'Ordre Digital</span>
                </div>
            </div>

            {{-- Form: POST /login, backend contract unchanged --}}
            <form method="POST" action="{{ route('login') }}" class="px-6 sm:px-12 pb-8 flex flex-col gap-5" novalidate>
                @csrf

                @if ($errors->any())
                    <div role="alert" class="rounded-lg bg-error-container/60 border border-error/25 px-4 py-3 text-[13px] text-error">
                        <ul class="list-disc ms-5 space-y-1">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Email (maquette: required asterisk, icon start, LTR) --}}
                <div class="flex flex-col gap-1.5">
                    <label for="email" class="text-xs font-medium text-ink dark:text-primary-100/85 flex items-center gap-1.5">
                        <span>{{ __('app.login_email_label') }}</span>
                        <span class="text-error font-bold" aria-hidden="true">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="pointer-events-none absolute start-3 flex items-center text-outline" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2.5" /><path d="M3.5 6.5 12 13 20.5 6.5" /></svg>
                        </span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            dir="ltr"
                            value="{{ old('email') }}"
                            autocomplete="username"
                            placeholder="name@commune-jerada.ma"
                            required
                            class="w-full h-11 rounded-lg bg-surface-low dark:bg-white/[0.05] border border-outline-soft/80 dark:border-white/10 px-11 text-left text-sm text-ink dark:text-primary-50 placeholder:text-outline outline-none transition-all focus:bg-white focus:border-primary focus:ring-[3px] focus:ring-primary/10"
                        />
                    </div>
                    @error('email')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password (maquette: eye toggle left in LTR, kept via data-password-toggle) --}}
                <div class="flex flex-col gap-1.5">
                    <label for="password" class="text-xs font-medium text-ink dark:text-primary-100/85 flex items-center gap-1.5">
                        <span>{{ __('app.login_password_label') }}</span>
                        <span class="text-error font-bold" aria-hidden="true">*</span>
                    </label>
                    <div class="relative flex items-center">
                        <span class="pointer-events-none absolute start-3 flex items-center text-outline" aria-hidden="true">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4.5" y="10.5" width="15" height="10" rx="2.5" /><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3" /></svg>
                        </span>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            dir="ltr"
                            autocomplete="current-password"
                            placeholder="••••••••••••"
                            required
                            class="w-full h-11 rounded-lg bg-surface-low dark:bg-white/[0.05] border border-outline-soft/80 dark:border-white/10 px-10 text-left text-sm text-ink dark:text-primary-50 placeholder:text-outline outline-none transition-all focus:bg-white focus:border-primary focus:ring-[3px] focus:ring-primary/10"
                        />
                        <button
                            type="button"
                            data-password-toggle="password"
                            aria-pressed="false"
                            class="absolute end-2 flex items-center p-2 rounded-lg text-outline hover:text-primary transition-colors"
                        >
                            <svg data-icon="eye" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg data-icon="eye-off" class="hidden" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 3l18 18" />
                                <path d="M10.6 5.2A10.7 10.7 0 0 1 12 5c6.4 0 10 7 10 7a17.6 17.6 0 0 1-3.4 4.3M6.6 6.6C4 8.3 2 12 2 12s3.6 7 10 7c1.5 0 2.9-.4 4.1-1" />
                                <path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember + forgot (maquette) --}}
                <div class="flex items-center justify-between pt-0.5 text-xs sm:text-sm select-none">
                    <label for="remember" class="inline-flex items-center gap-2 cursor-pointer text-ink-variant dark:text-primary-100/75 hover:text-ink transition-colors">
                        <input id="remember" name="remember" type="checkbox" value="1" {{ old('remember') ? 'checked' : '' }} class="w-4 h-4 rounded accent-primary cursor-pointer" />
                        <span>{{ __('app.login_remember') }}</span>
                    </label>
                    <span role="link" aria-disabled="true" tabindex="-1" class="font-medium text-primary cursor-not-allowed select-none">{{ __('app.login_forgot') }}</span>
                </div>

                <button
                    type="submit"
                    class="mt-1 w-full h-12 rounded-lg bg-primary text-white text-sm font-bold tracking-wide shadow-md transition-all hover:bg-primary-600 active:scale-[0.99] flex items-center justify-center gap-2"
                >
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" /><path d="M10 17l5-5-5-5" /><path d="M15 12H3" /></svg>
                    <span>{{ __('app.login_submit') }}</span>
                </button>
            </form>

            {{-- Server strip (maquette) --}}
            <div class="bg-surface-container-high/60 dark:bg-white/[0.04] px-6 py-2.5 flex flex-col sm:flex-row items-center justify-between gap-1 text-[11px] text-ink-variant dark:text-primary-100/70">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-green-600 inline-block animate-pulse" aria-hidden="true"></span>
                    <span class="font-registry">{{ __('app.login_server') }}</span>
                </div>
                <div class="text-outline font-registry">{{ __('app.login_copyright', ['year' => date('Y')]) }}</div>
            </div>
        </div>
    </section>

    {{-- Footer links (maquette) --}}
    <div class="relative z-10 mt-5 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-[11px] text-ink-variant dark:text-primary-100/60">
        <span class="hover:text-primary transition-colors cursor-pointer">{{ __('app.login_footer_privacy') }}</span>
        <span class="text-outline-variant">•</span>
        <span class="hover:text-primary transition-colors cursor-pointer">{{ __('app.login_footer_guide') }}</span>
        <span class="text-outline-variant">•</span>
        <span class="hover:text-primary transition-colors cursor-pointer">{{ __('app.login_footer_support') }}</span>
    </div>
</main>
@endsection
