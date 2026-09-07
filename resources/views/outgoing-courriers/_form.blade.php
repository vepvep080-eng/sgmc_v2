<div class="grid grid-cols-1 gap-x-7 gap-y-6 md:grid-cols-2">
    <div class="md:col-span-2">
        <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">{{ __('app.general_info_heading') }}</h3>
    </div>
    <div class="md:col-span-2">
        <label for="objet" class="mb-1.5 block text-xs font-medium text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_objet') }} <span class="text-red-500">*</span></label>
        <input id="objet" name="objet" value="{{ old('objet', $courrier->objet) }}" required maxlength="255" class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-900 outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50 @error('objet') border-red-400 @enderror">
        @error('objet') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="priorite" class="mb-1.5 block text-xs font-medium text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_priorite') }} <span class="text-red-500">*</span></label>
        <select id="priorite" name="priorite" required class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-900 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50 @error('priorite') border-red-400 @enderror">
            @foreach (\App\Models\OutgoingCourrier::PRIORITES as $value => $label)
                <option value="{{ $value }}" @selected(old('priorite', $courrier->priorite ?: 'normale') === $value)>{{ __('app.priorities.'.$value) }}</option>
            @endforeach
        </select>
        @error('priorite') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="statut" class="mb-1.5 block text-xs font-medium text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_statut') }} <span class="text-red-500">*</span></label>
        <select id="statut" name="statut" required class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-900 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50 @error('statut') border-red-400 @enderror">
            @foreach (\App\Models\OutgoingCourrier::STATUTS as $value => $label)
                <option value="{{ $value }}" @selected(old('statut', $courrier->statut ?: 'brouillon') === $value)>{{ __('app.statuses.'.$value) }}</option>
            @endforeach
        </select>
        @error('statut') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div class="md:col-span-2">
        <label for="description" class="mb-1.5 block text-xs font-medium text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_description') }}</label>
        <textarea id="description" name="description" rows="4" maxlength="5000" class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-900 outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">{{ old('description', $courrier->description) }}</textarea>
    </div>

    <div class="md:col-span-2 mt-2 border-t border-white/50 pt-6 dark:border-white/10"><h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">{{ __('app.recipient_heading') }}</h3></div>
    @foreach (['destinataire' => __('app.field_destinataire'), 'organisme_destinataire' => __('app.field_organisme_destinataire')] as $field => $label)
        <div>
            <label for="{{ $field }}" class="mb-1.5 block text-xs font-medium text-primary-700/75 dark:text-primary-100/70">{{ $label }} <span class="text-red-500">*</span></label>
            <input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $courrier->{$field}) }}" required maxlength="255" class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-900 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50 @error($field) border-red-400 @enderror">
            @error($field) <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    @endforeach
    <div class="md:col-span-2">
        <label for="adresse_destinataire" class="mb-1.5 block text-xs font-medium text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_adresse_destinataire') }}</label>
        <textarea id="adresse_destinataire" name="adresse_destinataire" rows="2" maxlength="500" class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-900 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">{{ old('adresse_destinataire', $courrier->adresse_destinataire) }}</textarea>
    </div>

    <div class="md:col-span-2 mt-2 border-t border-white/50 pt-6 dark:border-white/10"><h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">{{ __('app.sending_classification_heading') }}</h3></div>
    <div>
        <label for="date_envoi" class="mb-1.5 block text-xs font-medium text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_date_envoi') }} <span class="text-red-500">*</span></label>
        <input id="date_envoi" name="date_envoi" type="date" value="{{ old('date_envoi', $courrier->date_envoi?->format('Y-m-d')) }}" required class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-900 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50 @error('date_envoi') border-red-400 @enderror">
        @error('date_envoi') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="mode_envoi" class="mb-1.5 block text-xs font-medium text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_mode_envoi') }} <span class="text-red-500">*</span></label>
        <select id="mode_envoi" name="mode_envoi" required class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-900 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">
            @foreach (\App\Models\OutgoingCourrier::MODES_ENVOI as $mode)
                <option value="{{ $mode }}" @selected(old('mode_envoi', $courrier->mode_envoi) === $mode)>{{ __('app.modes.'.$mode) }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="reference_postale" class="mb-1.5 block text-xs font-medium text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_reference_postale') }}</label>
        <input id="reference_postale" name="reference_postale" value="{{ old('reference_postale', $courrier->reference_postale) }}" maxlength="100" class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-900 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">
    </div>
    <div>
        <label for="service_id" class="mb-1.5 block text-xs font-medium text-primary-700/75 dark:text-primary-100/70">{{ __('app.field_service') }} <span class="text-red-500">*</span></label>
        <select id="service_id" name="service_id" required class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-900 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">
            <option value="">{{ __('app.select_service_placeholder') }}</option>
            @foreach ($services as $service)
                <option value="{{ $service->id }}" @selected((string) old('service_id', $courrier->service_id) === (string) $service->id)>{{ $service->name }}</option>
            @endforeach
        </select>
        @error('service_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2 mt-2 border-t border-white/50 pt-6 dark:border-white/10">
        <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">{{ __('app.attachment_heading') }}</h3>
        @if ($courrier->attachment)
            <a href="{{ $courrier->attachmentUrl() }}" class="mt-3 inline-flex text-sm font-semibold text-primary-700 dark:text-primary-100 hover:text-gold-600">{{ __('app.current_file', ['ext' => strtoupper($courrier->attachmentExtension())]) }}</a>
        @endif
        <input id="attachment" name="attachment" type="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" class="mt-3 block w-full rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-700 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-500/10 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-primary-700 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-100">
        <p class="mt-1.5 text-xs text-primary-700/45 dark:text-primary-100/35">{{ __('app.attachment_hint') }}</p>
        @error('attachment') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
</div>