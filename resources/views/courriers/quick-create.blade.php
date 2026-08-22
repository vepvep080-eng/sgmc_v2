@extends('layouts.app')

@section('title', "Scanner un document — Bureau d'Ordre")
@section('page-title', 'Scanner un document')

@section('content')
    <a href="{{ route('courriers.index') }}" class="inline-flex items-center gap-1.5 text-sm text-primary-700/60 dark:text-primary-100/50 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200 ease-fluent mb-5">
        <x-icon name="arrow-left" class="w-4 h-4" />
        Retour à la liste
    </a>

    <div class="relative rounded-[24px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark px-6 sm:px-9 py-8 opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

        <h2 class="font-display font-semibold text-xl sm:text-2xl text-primary-800 dark:text-primary-50">
            Scanner un document
        </h2>
        <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
            Remplissez ces quelques champs pour créer le courrier, vous serez ensuite redirigé
            directement vers l'écran de numérisation et de routage.
        </p>

        <form method="POST" action="{{ route('courriers.store') }}" class="mt-7">
            @csrf

            {{-- Champs pré-remplis avec des valeurs par défaut raisonnables,
                 non affichés pour garder le formulaire rapide à remplir.
                 L'utilisateur peut toujours corriger ces détails plus tard
                 via "Modifier" sur la fiche du courrier. --}}
            <input type="hidden" name="date_reception" value="{{ old('date_reception', now()->toDateString()) }}">
            <input type="hidden" name="priorite" value="{{ old('priorite', 'normale') }}">
            <input type="hidden" name="type" value="{{ old('type', 'Administratif') }}">

            <div class="grid grid-cols-1 gap-5">
                {{-- Objet --}}
                <div>
                    <label for="objet" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        Objet <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="objet" name="objet" type="text" required autofocus
                        value="{{ old('objet') }}"
                        placeholder="Ex : Demande de raccordement au réseau d'assainissement"
                        class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 @error('objet') border-red-400 @enderror"
                    />
                    @error('objet') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Expéditeur --}}
                <div>
                    <label for="expediteur" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        Expéditeur <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="expediteur" name="expediteur" type="text" required
                        value="{{ old('expediteur') }}"
                        placeholder="Nom de la personne ou de l'organisme"
                        class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 @error('expediteur') border-red-400 @enderror"
                    />
                    @error('expediteur') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Service destinataire --}}
                <div>
                    <label for="service_id" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        Service destinataire <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="service_id" name="service_id" required
                        class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 @error('service_id') border-red-400 @enderror"
                    >
                        <option value="">— Sélectionner un service —</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" @selected((int) old('service_id') === $service->id)>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end gap-3">
                <a href="{{ route('courriers.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-primary-700/75 dark:text-primary-100/70 hover:bg-primary-500/8 dark:hover:bg-white/5 transition-colors duration-200 ease-fluent">
                    Annuler
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-500 via-primary-500 to-secondary-500 shadow-[0_10px_28px_-8px_rgba(11,61,145,0.5)] transition-all duration-300 ease-fluent hover:shadow-gold-glow hover:scale-[1.02] active:scale-[0.99]"
                >
                    <x-icon name="check" class="w-4 h-4" />
                    Créer et scanner
                </button>
            </div>
        </form>
    </div>
@endsection
