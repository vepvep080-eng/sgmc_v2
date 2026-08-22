{{--
    Shared form fields for both "create" and "edit". The parent view
    provides $courrier (a real model on edit, a fresh `new Courrier` on
    create) so `old('field', $courrier->field)` always has a sane
    fallback. $services is provided by CourrierController@create/@edit.
--}}

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

    {{-- Objet --}}
    <div class="sm:col-span-2">
        <label for="objet" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
            Objet <span class="text-red-500">*</span>
        </label>
        <input
            id="objet" name="objet" type="text" required
            value="{{ old('objet', $courrier->objet) }}"
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
            value="{{ old('expediteur', $courrier->expediteur) }}"
            placeholder="Nom de la personne ou de l'organisme"
            class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 @error('expediteur') border-red-400 @enderror"
        />
        @error('expediteur') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Référence --}}
    <div>
        <label for="reference" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
            Référence
        </label>
        <input
            id="reference" name="reference" type="text"
            value="{{ old('reference', $courrier->reference) }}"
            placeholder="Référence externe (optionnel)"
            class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 @error('reference') border-red-400 @enderror"
        />
        @error('reference') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Date de réception --}}
    <div>
        <label for="date_reception" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
            Date de réception <span class="text-red-500">*</span>
        </label>
        <input
            id="date_reception" name="date_reception" type="text" required
            data-flatpickr
            autocomplete="off"
            value="{{ old('date_reception', optional($courrier->date_reception)->toDateString()) }}"
            class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 @error('date_reception') border-red-400 @enderror"
        />
        @error('date_reception') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Priorité --}}
    <div>
        <label for="priorite" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
            Priorité <span class="text-red-500">*</span>
        </label>
        <select
            id="priorite" name="priorite" required
            class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 @error('priorite') border-red-400 @enderror"
        >
            @foreach (\App\Models\Courrier::PRIORITES as $value => $label)
                <option value="{{ $value }}" @selected(old('priorite', $courrier->priorite ?? 'normale') === $value)>{{ __('app.priorities.'.$value) }}</option>
            @endforeach
        </select>
        @error('priorite') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Type --}}
    <div>
        <label for="type" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
            Type <span class="text-red-500">*</span>
        </label>
        <input
            id="type" name="type" type="text" required list="type-suggestions"
            value="{{ old('type', $courrier->type) }}"
            placeholder="Ex : Administratif"
            class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 @error('type') border-red-400 @enderror"
        />
        <datalist id="type-suggestions">
            <option value="Administratif" />
            <option value="Financier" />
            <option value="Juridique" />
            <option value="Technique" />
            <option value="Ressources Humaines" />
            <option value="Réclamation" />
            <option value="Autre" />
        </datalist>
        @error('type') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
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
                <option value="{{ $service->id }}" @selected((int) old('service_id', $courrier->service_id) === $service->id)>
                    {{ $service->name }}
                </option>
            @endforeach
        </select>
        @error('service_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Description --}}
    <div class="sm:col-span-2">
        <label for="description" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
            Description
        </label>
        <textarea
            id="description" name="description" rows="4"
            placeholder="Détails complémentaires sur le courrier (optionnel)"
            class="w-full rounded-xl bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 resize-none @error('description') border-red-400 @enderror"
        >{{ old('description', $courrier->description) }}</textarea>
        @error('description') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>

    {{-- Pièce jointe --}}
    <div class="sm:col-span-2">
        <label for="piece_jointe" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
            Pièce jointe
        </label>

        @if (($courrier->piece_jointe ?? null) && empty($errors->has('piece_jointe')))
            <div class="flex items-center gap-3 mb-3 rounded-xl border border-white/70 dark:border-white/10 bg-white/50 dark:bg-white/[0.04] px-4 py-2.5">
                <x-icon name="paperclip" class="w-4 h-4 text-primary-500/60 dark:text-primary-100/50 shrink-0" />
                <a href="{{ $courrier->pieceJointeUrl() }}" target="_blank" rel="noopener" class="text-sm text-primary-700 dark:text-primary-100 hover:text-gold-600 dark:hover:text-gold-400 truncate">
                    Fichier actuel ({{ strtoupper($courrier->pieceJointeExtension()) }})
                </a>
                <label class="ml-auto flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400 cursor-pointer select-none">
                    <input type="checkbox" name="remove_piece_jointe" value="1" class="rounded border-red-300" />
                    Supprimer
                </label>
            </div>
        @endif

        <div class="relative rounded-xl border-2 border-dashed border-primary-500/20 dark:border-white/15 bg-white/40 dark:bg-white/[0.03] px-5 py-6 text-center transition-colors duration-200 ease-fluent hover:border-gold-400/50 @error('piece_jointe') border-red-400 @enderror">
            <input
                id="piece_jointe" name="piece_jointe" type="file"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                data-file-input
            />
            <div class="flex flex-col items-center gap-2 pointer-events-none">
                <x-icon name="paperclip" class="w-5 h-5 text-primary-500/50 dark:text-primary-100/40" />
                <p class="text-sm text-primary-700/70 dark:text-primary-100/60">
                    <span class="font-semibold text-primary-700 dark:text-primary-100">Cliquez pour choisir un fichier</span> ou glissez-le ici
                </p>
                <p class="text-xs text-primary-700/45 dark:text-primary-100/35">PDF, DOCX ou image — 10 Mo maximum</p>
                <p data-file-name class="text-xs font-medium text-gold-600 dark:text-gold-400 hidden"></p>
            </div>
        </div>
        @error('piece_jointe') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
    </div>
</div>
