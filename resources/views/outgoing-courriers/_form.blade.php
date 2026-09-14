<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left column: numbered section cards --}}
    <div class="lg:col-span-2 flex flex-col gap-5">

        {{-- Section 1 — Informations --}}
        <section class="rounded-xl bg-white/60 dark:bg-white/[0.04] border border-white/70 dark:border-white/10 shadow-glass px-5 py-5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 shrink-0 rounded-lg bg-primary-500 dark:bg-primary-600 text-white flex items-center justify-center text-sm font-bold">1</div>
                <div class="flex flex-col">
                    <h3 class="font-display text-sm font-semibold text-primary-800 dark:text-primary-50">{{ __('app.general_info_heading') }}</h3>
                    <span class="text-[11px] text-primary-700/50 dark:text-primary-100/40">{{ __('app.objet') }} · {{ __('app.priority') }} · {{ __('app.status') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-4">
                <div class="sm:col-span-2">
                    <label for="objet" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_objet') }} <span class="text-red-500">*</span></label>
                    <input id="objet" name="objet" value="{{ old('objet', $courrier->objet) }}" required maxlength="255" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('objet') border-red-400 @enderror">
                    @error('objet') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="priorite" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_priorite') }} <span class="text-red-500">*</span></label>
                    <select id="priorite" name="priorite" required class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('priorite') border-red-400 @enderror">
                        @foreach (\App\Models\OutgoingCourrier::PRIORITES as $value => $label)
                            <option value="{{ $value }}" @selected(old('priorite', $courrier->priorite ?: 'normale') === $value)>{{ __('app.priorities.'.$value) }}</option>
                        @endforeach
                    </select>
                    @error('priorite') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="statut" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_statut') }} <span class="text-red-500">*</span></label>
                    <select id="statut" name="statut" required class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('statut') border-red-400 @enderror">
                        @foreach (\App\Models\OutgoingCourrier::STATUTS as $value => $label)
                            <option value="{{ $value }}" @selected(old('statut', $courrier->statut ?: 'brouillon') === $value)>{{ __('app.statuses.'.$value) }}</option>
                        @endforeach
                    </select>
                    @error('statut') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_description') }}</label>
                    <textarea id="description" name="description" rows="4" maxlength="5000" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 resize-none">{{ old('description', $courrier->description) }}</textarea>
                </div>
            </div>
        </section>

        {{-- Section 2 — Destinataire --}}
        <section class="rounded-xl bg-white/60 dark:bg-white/[0.04] border border-white/70 dark:border-white/10 shadow-glass px-5 py-5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 shrink-0 rounded-lg bg-primary-500 dark:bg-primary-600 text-white flex items-center justify-center text-sm font-bold">2</div>
                <div class="flex flex-col">
                    <h3 class="font-display text-sm font-semibold text-primary-800 dark:text-primary-50">{{ __('app.recipient_heading') }}</h3>
                    <span class="text-[11px] text-primary-700/50 dark:text-primary-100/40">{{ __('app.field_destinataire') }} · {{ __('app.service') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-4">
                @foreach (['destinataire' => __('app.field_destinataire'), 'organisme_destinataire' => __('app.field_organisme_destinataire')] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">{{ $label }} <span class="text-red-500">*</span></label>
                        <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $courrier->{$field}) }}" required maxlength="255" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error($field) border-red-400 @enderror">
                        @error($field) <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endforeach

                <div class="sm:col-span-2">
                    <label for="adresse_destinataire" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_adresse_destinataire') }}</label>
                    <textarea id="adresse_destinataire" name="adresse_destinataire" rows="2" maxlength="500" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 resize-none">{{ old('adresse_destinataire', $courrier->adresse_destinataire) }}</textarea>
                </div>
            </div>
        </section>

        {{-- Section 3 — Envoi --}}
        <section class="rounded-xl bg-white/60 dark:bg-white/[0.04] border border-white/70 dark:border-white/10 shadow-glass px-5 py-5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 shrink-0 rounded-lg bg-primary-500 dark:bg-primary-600 text-white flex items-center justify-center text-sm font-bold">3</div>
                <div class="flex flex-col">
                    <h3 class="font-display text-sm font-semibold text-primary-800 dark:text-primary-50">{{ __('app.sending_classification_heading') }}</h3>
                    <span class="text-[11px] text-primary-700/50 dark:text-primary-100/40">{{ __('app.field_date_envoi') }} · {{ __('app.field_mode_envoi') }} · {{ __('app.field_reference_postale') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mt-4">
                <div>
                    <label for="date_envoi" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_date_envoi') }} <span class="text-red-500">*</span></label>
                    <input id="date_envoi" name="date_envoi" type="date" value="{{ old('date_envoi', $courrier->date_envoi?->format('Y-m-d')) }}" required class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 @error('date_envoi') border-red-400 @enderror">
                    @error('date_envoi') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="mode_envoi" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_mode_envoi') }} <span class="text-red-500">*</span></label>
                    <select id="mode_envoi" name="mode_envoi" required class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        @foreach (\App\Models\OutgoingCourrier::MODES_ENVOI as $mode)
                            <option value="{{ $mode }}" @selected(old('mode_envoi', $courrier->mode_envoi) === $mode)>{{ __('app.modes.'.$mode) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="reference_postale" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_reference_postale') }}</label>
                    <input id="reference_postale" name="reference_postale" value="{{ old('reference_postale', $courrier->reference_postale) }}" maxlength="100" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                </div>

                <div>
                    <label for="service_id" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_service') }} <span class="text-red-500">*</span></label>
                    <select id="service_id" name="service_id" required class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-3 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                        <option value="">{{ __('app.select_service_placeholder') }}</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" @selected((string) old('service_id', $courrier->service_id) === (string) $service->id)>{{ $service->name }}</option>
                        @endforeach
                    </select>
                    @error('service_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>
    </div>

    {{-- Right column: attachment --}}
    <div class="lg:col-span-1">
        <section class="rounded-xl bg-white/60 dark:bg-white/[0.04] border border-white/70 dark:border-white/10 shadow-glass px-5 py-5">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 shrink-0 rounded-lg bg-primary-500 dark:bg-primary-600 text-white flex items-center justify-center text-sm font-bold">
                    <x-icon name="plus" class="w-4 h-4" />
                </div>
                <h3 class="font-display text-sm font-semibold text-primary-800 dark:text-primary-50">{{ __('app.attachment_heading') }}</h3>
            </div>

            <div class="mt-4">
                @if ($courrier->attachment)
                    <div class="mb-3 flex items-center gap-3 rounded-xl border border-primary-500/10 dark:border-white/10 bg-primary-100/40 dark:bg-white/[0.04] px-4 py-2.5">
                        <x-icon name="paperclip" class="w-4 h-4 shrink-0 text-primary-500/60 dark:text-primary-100/50" />
                        <a href="{{ $courrier->attachmentUrl() }}" class="text-sm truncate text-primary-700 dark:text-primary-100 hover:text-gold-600 dark:hover:text-gold-400">{{ __('app.current_file', ['ext' => strtoupper($courrier->attachmentExtension())]) }}</a>
                    </div>
                @endif

                <div class="relative rounded-xl border-2 border-dashed border-primary-500/20 dark:border-white/15 bg-primary-100/40 dark:bg-white/[0.03] px-5 py-7 text-center transition-colors duration-200 ease-fluent hover:border-primary-500/40 hover:bg-primary-100/55 @error('attachment') border-red-400 @enderror">
                    <input id="attachment" name="attachment" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" data-file-input class="absolute inset-0 w-full h-full opacity-0 cursor-pointer file:mr-4 file:rounded-lg file:border-0 file:bg-primary-500/10 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-primary-700">
                    <div class="flex flex-col items-center gap-3 pointer-events-none">
                        <div class="w-14 h-14 rounded-full bg-primary-100/80 dark:bg-white/10 flex items-center justify-center text-primary-700 dark:text-primary-100">
                            <x-icon name="plus" class="w-6 h-6" />
                        </div>
                        <p class="text-sm text-primary-700/70 dark:text-primary-100/60">
                            <span class="font-semibold text-primary-800 dark:text-primary-100">{{ __('app.file_click_or_drag') }}</span> {{ __('app.file_or_drag') }}
                        </p>
                        <p class="text-xs text-primary-700/45 dark:text-primary-100/35">{{ __('app.attachment_hint') }}</p>
                        <span class="mt-1 inline-flex items-center gap-1.5 rounded-lg bg-white/70 dark:bg-white/10 border border-primary-500/15 dark:border-white/10 px-3.5 py-1.5 text-[11px] font-semibold text-primary-700 dark:text-primary-100 shadow-glass">
                            <x-icon name="download" class="w-3.5 h-3.5" /> {{ __('app.click_to_choose_file') }}
                        </span>
                        <p data-file-name class="text-xs font-medium text-gold-600 dark:text-gold-400 hidden"></p>
                    </div>
                </div>
                @error('attachment') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </section>
    </div>
</div>