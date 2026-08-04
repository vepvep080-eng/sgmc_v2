@csrf

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">

    <div class="sm:col-span-2">
        <label for="name" class="block text-xs font-semibold uppercase tracking-wide text-primary-700/50 dark:text-primary-100/40 mb-1.5">
            Nom complet
        </label>
        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $user->name ?? '') }}"
            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/60 dark:bg-white/[0.04] border border-white/60 dark:border-white/10 text-primary-800 dark:text-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500/30 @error('name') ring-2 ring-rose-500/40 @enderror"
            required
            autofocus
        />
        @error('name')
            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="email" class="block text-xs font-semibold uppercase tracking-wide text-primary-700/50 dark:text-primary-100/40 mb-1.5">
            Adresse e-mail
        </label>
        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email', $user->email ?? '') }}"
            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/60 dark:bg-white/[0.04] border border-white/60 dark:border-white/10 text-primary-800 dark:text-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500/30 @error('email') ring-2 ring-rose-500/40 @enderror"
            required
        />
        @error('email')
            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-primary-700/50 dark:text-primary-100/40 mb-1.5">
            Mot de passe
            @isset($user)
                <span class="normal-case font-normal text-primary-700/35 dark:text-primary-100/25">(laisser vide pour ne pas changer)</span>
            @endisset
        </label>
        <input
            type="password"
            id="password"
            name="password"
            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/60 dark:bg-white/[0.04] border border-white/60 dark:border-white/10 text-primary-800 dark:text-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500/30 @error('password') ring-2 ring-rose-500/40 @enderror"
            @if (! isset($user)) required @endif
        />
        @error('password')
            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wide text-primary-700/50 dark:text-primary-100/40 mb-1.5">
            Confirmer le mot de passe
        </label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/60 dark:bg-white/[0.04] border border-white/60 dark:border-white/10 text-primary-800 dark:text-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
            @if (! isset($user)) required @endif
        />
    </div>

    <div>
        <label for="role_id" class="block text-xs font-semibold uppercase tracking-wide text-primary-700/50 dark:text-primary-100/40 mb-1.5">
            Rôle
        </label>
        <select
            id="role_id"
            name="role_id"
            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/60 dark:bg-white/[0.04] border border-white/60 dark:border-white/10 text-primary-800 dark:text-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500/30 @error('role_id') ring-2 ring-rose-500/40 @enderror"
            required
        >
            <option value="" disabled {{ old('role_id', $user->role_id ?? '') ? '' : 'selected' }}>Sélectionner un rôle</option>
            @foreach ($roles as $role)
                <option value="{{ $role->id }}" @selected(old('role_id', $user->role_id ?? '') == $role->id)>
                    {{ $role->name }}
                </option>
            @endforeach
        </select>
        @error('role_id')
            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="service_id" class="block text-xs font-semibold uppercase tracking-wide text-primary-700/50 dark:text-primary-100/40 mb-1.5">
            Service
        </label>
        <select
            id="service_id"
            name="service_id"
            class="w-full px-4 py-2.5 rounded-xl text-sm bg-white/60 dark:bg-white/[0.04] border border-white/60 dark:border-white/10 text-primary-800 dark:text-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500/30 @error('service_id') ring-2 ring-rose-500/40 @enderror"
        >
            <option value="">— Aucun —</option>
            @foreach ($services as $service)
                <option value="{{ $service->id }}" @selected(old('service_id', $user->service_id ?? '') == $service->id)>
                    {{ $service->name }}
                </option>
            @endforeach
        </select>
        <p class="mt-1.5 text-xs text-primary-700/40 dark:text-primary-100/30">Requis uniquement pour un Chef de Service.</p>
        @error('service_id')
            <p class="mt-1.5 text-xs text-rose-600 dark:text-rose-300">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center">
        <label class="flex items-center gap-2.5 cursor-pointer select-none mt-5">
            <input
                type="checkbox"
                name="is_active"
                value="1"
                {{ old('is_active', $user->is_active ?? true) ? 'checked' : '' }}
                class="w-4 h-4 rounded border-white/60 dark:border-white/10 text-primary-600 focus:ring-primary-500/30"
            />
            <span class="text-sm text-primary-700/80 dark:text-primary-100/70">Compte actif</span>
        </label>
    </div>

</div>
