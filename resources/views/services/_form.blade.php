<div>
    <label for="name" class="block text-sm font-medium text-primary-800 dark:text-primary-50 mb-1.5">Nom du service</label>
    <input
        type="text"
        id="name"
        name="name"
        value="{{ old('name', $service->name ?? '') }}"
        required
        class="w-full rounded-xl border border-white/60 dark:border-white/10 bg-white/60 dark:bg-white/5 px-4 py-2.5 text-sm text-primary-800 dark:text-primary-50 placeholder:text-primary-700/40 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
    />
    @error('name')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="code" class="block text-sm font-medium text-primary-800 dark:text-primary-50 mb-1.5">Code</label>
    <input
        type="text"
        id="code"
        name="code"
        value="{{ old('code', $service->code ?? '') }}"
        required
        maxlength="20"
        class="w-full rounded-xl border border-white/60 dark:border-white/10 bg-white/60 dark:bg-white/5 px-4 py-2.5 text-sm text-primary-800 dark:text-primary-50 uppercase focus:outline-none focus:ring-2 focus:ring-primary-500/30"
    />
    @error('code')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<div>
    <label for="description" class="block text-sm font-medium text-primary-800 dark:text-primary-50 mb-1.5">Description (optionnel)</label>
    <textarea
        id="description"
        name="description"
        rows="3"
        class="w-full rounded-xl border border-white/60 dark:border-white/10 bg-white/60 dark:bg-white/5 px-4 py-2.5 text-sm text-primary-800 dark:text-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
    >{{ old('description', $service->description ?? '') }}</textarea>
    @error('description')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>

<label class="flex items-center gap-2.5 text-sm text-primary-800 dark:text-primary-50">
    <input
        type="checkbox"
        name="is_active"
        value="1"
        {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}
        class="w-4 h-4 rounded border-white/60 text-primary-500 focus:ring-primary-500/30"
    />
    Service actif
</label>
