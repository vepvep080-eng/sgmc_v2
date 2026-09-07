{{--
    Shared form fields for both "create" and "edit". The parent view
    provides $courrier (a real model on edit, a fresh `new Courrier` on
    create) so `old('field', $courrier->field)` always has a sane
    fallback. $services is provided by CourrierController@create/@edit.
--}}

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left column: numbered section cards --}}
    <div class="lg:col-span-2 flex flex-col gap-5">

        {{-- Section 1 — Informations --}}
        <section class="rounded-xl bg-white/60 dark:bg-white/[0.04] border border-white/70 dark:border-white/10 shadow-glass px-5 py-5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 shrink-0 rounded-full bg-primary-100/80 dark:bg-white/10 flex items-center justify-center text-primary-700 dark:text-primary-100">
                    <x-icon name="paperclip" class="w-4 h-4" />
                </div>
                <h3 class="font-display text-sm font-semibold text-primary-800 dark:text-primary-50">1. {{ __('app.form_section_1') }}</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-4">
                {{-- Objet --}}
                <div class="sm:col-span-2">
                    <label for="objet" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        {{ __('app.subject') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="objet" name="objet" type="text" required
                        value="{{ old('objet', $courrier->objet) }}"
                        placeholder="{{ __('app.objet_placeholder') }}"
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
                        value="{{ old('expediteur', $courrier->expediteur) }}"
                        placeholder="{{ __('app.expediteur_placeholder') }}"
                        class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('expediteur') border-red-400 @enderror"
                    />
                    @error('expediteur') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Référence --}}
                <div>
                    <label for="reference" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        {{ __('app.reference') }}
                    </label>
                    <input
                        id="reference" name="reference" type="text"
                        value="{{ old('reference', $courrier->reference) }}"
                        placeholder="{{ __('app.reference_placeholder') }}"
                        class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('reference') border-red-400 @enderror"
                    />
                    @error('reference') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Type --}}
                <div class="sm:col-span-2">
                    <label for="type" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        {{ __('app.type') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="type" name="type" type="text" required list="type-suggestions"
                        value="{{ old('type', $courrier->type) }}"
                        placeholder="{{ __('app.type_placeholder') }}"
                        class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('type') border-red-400 @enderror"
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
            </div>
        </section>

        {{-- Section 2 — Classification --}}
        <section class="rounded-xl bg-white/60 dark:bg-white/[0.04] border border-white/70 dark:border-white/10 shadow-glass px-5 py-5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 shrink-0 rounded-full bg-primary-100/80 dark:bg-white/10 flex items-center justify-center text-primary-700 dark:text-primary-100">
                    <x-icon name="inbox" class="w-4 h-4" />
                </div>
                <h3 class="font-display text-sm font-semibold text-primary-800 dark:text-primary-50">2. {{ __('app.form_section_2') }}</h3>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-4">
                {{-- Date de réception --}}
                <div>
                    <label for="date_reception" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        {{ __('app.date_reception') }} <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="date_reception" name="date_reception" type="text" required
                        data-flatpickr
                        autocomplete="off"
                        value="{{ old('date_reception', optional($courrier->date_reception)->toDateString()) }}"
                        class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('date_reception') border-red-400 @enderror"
                    />
                    @error('date_reception') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Priorité --}}
                <div>
                    <label for="priorite" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        {{ __('app.priority') }} <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="priorite" name="priorite" required
                        class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('priorite') border-red-400 @enderror"
                    >
                        @foreach (\App\Models\Courrier::PRIORITES as $value => $label)
                            <option value="{{ $value }}" @selected(old('priorite', $courrier->priorite ?? 'normale') === $value)>{{ __('app.priorities.'.$value) }}</option>
                        @endforeach
                    </select>
                    @error('priorite') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Service destinataire --}}
                <div class="sm:col-span-2">
                    <label for="service_id" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        {{ __('app.recipient_service') }} <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="service_id" name="service_id" required
                        class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('service_id') border-red-400 @enderror"
                    >
                        <option value="">{{ __('app.select_service_placeholder') }}</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" @selected((int) old('service_id', $courrier->service_id) === $service->id)>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('service_id') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        {{-- Section 3 — Description --}}
        <section class="rounded-xl bg-white/60 dark:bg-white/[0.04] border border-white/70 dark:border-white/10 shadow-glass px-5 py-5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 shrink-0 rounded-full bg-primary-100/80 dark:bg-white/10 flex items-center justify-center text-primary-700 dark:text-primary-100">
                    <x-icon name="pencil" class="w-4 h-4" />
                </div>
                <h3 class="font-display text-sm font-semibold text-primary-800 dark:text-primary-50">3. {{ __('app.form_section_3') }}</h3>
            </div>

            <div class="mt-4">
                {{-- Description --}}
                <div>
                    <label for="description" class="block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70 mb-1.5">
                        {{ __('app.description') }}
                    </label>
                    <textarea
                        id="description" name="description" rows="4"
                        placeholder="{{ __('app.description_placeholder') }}"
                        class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 resize-none @error('description') border-red-400 @enderror"
                    >{{ old('description', $courrier->description) }}</textarea>
                    @error('description') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>
    </div>

    {{-- Right column: attachment --}}
    <div class="lg:col-span-1">
        <section class="rounded-xl bg-white/60 dark:bg-white/[0.04] border border-white/70 dark:border-white/10 shadow-glass px-5 py-5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 shrink-0 rounded-full bg-primary-100/80 dark:bg-white/10 flex items-center justify-center text-primary-700 dark:text-primary-100">
                    <x-icon name="plus" class="w-4 h-4" />
                </div>
                <h3 class="font-display text-sm font-semibold text-primary-800 dark:text-primary-50">{{ __('app.form_section_4') }}</h3>
            </div>

            <div class="mt-4">
                {{-- Pièce jointe --}}
                @if (($courrier->piece_jointe ?? null) && empty($errors->has('piece_jointe')))
                    <div class="flex items-center gap-3 mb-3 rounded-xl border border-primary-500/10 dark:border-white/10 bg-primary-100/40 dark:bg-white/[0.04] px-4 py-2.5">
                        <x-icon name="paperclip" class="w-4 h-4 text-primary-500/60 dark:text-primary-100/50 shrink-0" />
                        <a href="{{ $courrier->pieceJointeUrl() }}" target="_blank" rel="noopener" class="text-sm text-primary-700 dark:text-primary-100 hover:text-gold-600 dark:hover:text-gold-400 truncate">
                            {{ __('app.current_file', ['ext' => strtoupper($courrier->pieceJointeExtension())]) }}
                        </a>
                        <label class="ml-auto flex items-center gap-1.5 text-xs text-red-600 dark:text-red-400 cursor-pointer select-none">
                            <input type="checkbox" name="remove_piece_jointe" value="1" class="rounded border-red-300" />
                            {{ __('app.delete') }}
                        </label>
                    </div>
                @endif

                <div class="relative rounded-xl border-2 border-dashed border-primary-500/20 dark:border-white/15 bg-primary-100/40 dark:bg-white/[0.03] px-5 py-7 text-center transition-colors duration-200 ease-fluent hover:border-primary-500/40 hover:bg-primary-100/55 @error('piece_jointe') border-red-400 @enderror">
                    <input
                        id="piece_jointe" name="piece_jointe" type="file"
                        accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        data-file-input
                    />
                    <div class="flex flex-col items-center gap-3 pointer-events-none">
                        <div class="w-14 h-14 rounded-full bg-primary-100/80 dark:bg-white/10 flex items-center justify-center text-primary-700 dark:text-primary-100">
                            <x-icon name="plus" class="w-6 h-6" />
                        </div>
                        <p class="text-sm text-primary-700/70 dark:text-primary-100/60">
                            <span class="font-semibold text-primary-800 dark:text-primary-100">{{ __('app.file_click_or_drag') }}</span> {{ __('app.file_or_drag') }}
                        </p>
                        <p class="text-xs text-primary-700/45 dark:text-primary-100/35">{{ __('app.file_size_hint') }}</p>
                        <span class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-white/70 dark:bg-white/10 border border-primary-500/15 dark:border-white/10 px-3.5 py-1.5 text-[11px] font-semibold text-primary-700 dark:text-primary-100 shadow-glass">
                            <x-icon name="download" class="w-3.5 h-3.5" /> {{ __('app.click_to_choose_file') }}
                        </span>
                        <p data-file-name class="text-xs font-medium text-gold-600 dark:text-gold-400 hidden"></p>
                    </div>
                </div>
                @error('piece_jointe') <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
            </div>
        </section>
    </div>
</div>