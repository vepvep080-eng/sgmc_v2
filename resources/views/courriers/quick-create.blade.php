@extends('layouts.app')

@section('title', "Scanner un document — Bureau d'Ordre")
@section('page-title', __('app.scan_a_document'))

@section('content')
    <a href="{{ route('courriers.index') }}" class="inline-flex items-center gap-1.5 text-sm text-primary-700/60 dark:text-primary-100/50 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200 ease-fluent mb-5">
        <x-icon name="arrow-left" class="w-4 h-4" />
        {{ __('app.back_to_list') }}
    </a>

    <div class="relative rounded-[24px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark px-6 sm:px-9 py-8 opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

        {{-- En-tête : pastille icône + titre + badge de conformité (référence Stitch) --}}
        <div class="flex flex-wrap items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-primary-100/80 dark:bg-primary-500/15 border border-primary-500/15 dark:border-white/10 flex items-center justify-center shrink-0">
                <x-icon name="archive" class="w-6 h-6 text-primary-700 dark:text-primary-100" />
            </div>
            <div class="flex-1 min-w-[240px]">
                <div class="flex items-center gap-3 flex-wrap">
                    <h2 class="font-display font-semibold text-xl sm:text-2xl text-primary-800 dark:text-primary-50">
                        {{ __('app.scan_a_document') }}
                    </h2>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary-500/12 dark:bg-secondary-500/15 text-secondary-700 dark:text-secondary-300 text-[11px] font-semibold tracking-wide">
                        {{ __('app.national_standard_badge') }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
                    {{ __('app.quick_create_description') }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('courriers.store') }}" class="mt-7">
            @csrf

            {{-- Champs pré-remplis avec des valeurs par défaut raisonnables,
                 non affichés pour garder le formulaire rapide à remplir.
                 L'utilisateur peut toujours corriger ces détails plus tard
                 via "Modifier" sur la fiche du courrier. --}}
            <input type="hidden" name="date_reception" value="{{ old('date_reception', now()->toDateString()) }}">
            <input type="hidden" name="priorite" value="{{ old('priorite', 'normale') }}">
            <input type="hidden" name="type" value="{{ old('type', 'Administratif') }}">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- القسم 1 : معلومات المراسلة --}}
                <div class="lg:col-span-2 rounded-lg border border-primary-500/10 dark:border-white/10 bg-white/60 dark:bg-white/[0.04] p-5 sm:p-6">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="w-8 h-8 rounded-full bg-primary-500/10 dark:bg-primary-500/20 text-primary-700 dark:text-primary-100 text-sm font-bold flex items-center justify-center shrink-0">1</span>
                        <h3 class="font-display font-semibold text-primary-800 dark:text-primary-50">{{ __('app.form_section_1') }}</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-5">
                        {{-- Objet --}}
                        <div>
                            <label for="objet" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                                {{ __('app.subject') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="objet" name="objet" type="text" required autofocus
                                value="{{ old('objet') }}"
                                placeholder="{{ __('app.subject_placeholder') }}"
                                class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('objet') border-red-400 @enderror"
                            />
                            @error('objet') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        {{-- Expéditeur --}}
                        <div>
                            <label for="expediteur" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                                {{ __('app.sender') }} <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="expediteur" name="expediteur" type="text" required
                                value="{{ old('expediteur') }}"
                                placeholder="{{ __('app.sender_placeholder') }}"
                                class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('expediteur') border-red-400 @enderror"
                            />
                            @error('expediteur') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- القسم 2 : التصنيف والتوجيه الإداري --}}
                <div class="rounded-lg border border-primary-500/10 dark:border-white/10 bg-white/60 dark:bg-white/[0.04] p-5 sm:p-6 self-start">
                    <div class="flex items-center gap-3 mb-5">
                        <span class="w-8 h-8 rounded-full bg-primary-500/10 dark:bg-primary-500/20 text-primary-700 dark:text-primary-100 text-sm font-bold flex items-center justify-center shrink-0">2</span>
                        <h3 class="font-display font-semibold text-primary-800 dark:text-primary-50">{{ __('app.form_section_2') }}</h3>
                    </div>

                    <label for="service_id" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        {{ __('app.recipient_service') }} <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="service_id" name="service_id" required
                        class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('service_id') border-red-400 @enderror"
                    >
                        <option value="">{{ __('app.select_service_placeholder') }}</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" @selected((int) old('service_id') === $service->id)>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-end gap-3 border-t border-primary-500/10 dark:border-white/10 pt-6">
                <a href="{{ route('courriers.index') }}" class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-primary-700 dark:text-primary-100 bg-white/60 dark:bg-white/[0.06] border border-primary-500/15 dark:border-white/10 shadow-glass hover:bg-white/80 dark:hover:bg-white/[0.1] transition-all duration-200 ease-fluent">
                    {{ __('app.cancel') }}
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold text-white bg-primary shadow-md transition-all hover:bg-primary-600 hover:scale-[1.02] active:scale-[0.99]"
                >
                    <x-icon name="check" class="w-4 h-4" />
                    {{ __('app.create_and_scan') }}
                </button>
            </div>
        </form>
    </div>
@endsection
