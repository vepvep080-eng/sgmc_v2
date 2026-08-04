@extends('layouts.app')

@section('title', "Services — Bureau d'Ordre")
@section('page-title', 'Services')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div>
            <h2 class="font-display font-semibold text-2xl text-primary-800 dark:text-primary-50">Services</h2>
            <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
                Gérez les services destinataires des courriers.
            </p>
        </div>
        @can('create', App\Models\Service::class)
            <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-r from-primary-500 to-secondary-500 rounded-xl px-5 py-2.5 shadow-[0_10px_24px_-8px_rgba(11,61,145,0.45)] hover:scale-[1.02] transition-transform duration-200 ease-fluent">
                <x-icon name="plus-simple" class="w-4 h-4" />
                Ajouter un service
            </a>
        @endcan
    </div>

    <form method="GET" class="mt-6 opacity-0 animate-fadeInUp" style="animation-delay: 0.08s;">
        <div class="relative max-w-sm">
            <input
                type="text"
                name="q"
                value="{{ $filters['q'] ?? '' }}"
                placeholder="Rechercher un service..."
                class="w-full rounded-xl border border-white/60 dark:border-white/10 bg-white/60 dark:bg-white/5 px-4 py-2.5 text-sm text-primary-800 dark:text-primary-50 placeholder:text-primary-700/40 dark:placeholder:text-primary-100/30 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
            />
        </div>
    </form>

    <div class="mt-6 opacity-0 animate-fadeInUp" style="animation-delay: 0.12s;">
        <div class="relative rounded-[24px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark overflow-hidden">
            <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            @if ($services->isEmpty())
                <x-empty-state
                    icon="building"
                    title="Aucun service"
                    description="Ajoutez un service pour commencer à router les courriers."
                >
                    @can('create', App\Models\Service::class)
                        <x-slot:action>
                            <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-r from-primary-500 to-secondary-500 rounded-xl px-5 py-2.5 shadow-[0_10px_24px_-8px_rgba(11,61,145,0.45)] hover:scale-[1.02] transition-transform duration-200 ease-fluent">
                                <x-icon name="plus-simple" class="w-4 h-4" />
                                Ajouter un service
                            </a>
                        </x-slot:action>
                    @endcan
                </x-empty-state>
            @else
                <ul class="divide-y divide-white/40 dark:divide-white/5">
                    @foreach ($services as $service)
                        <li class="flex items-center gap-4 px-6 sm:px-8 py-4">
                            <div class="w-9 h-9 rounded-xl bg-primary-500/8 dark:bg-white/5 flex items-center justify-center text-primary-500/60 dark:text-primary-100/50 shrink-0">
                                <x-icon name="building" class="w-4 h-4" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-primary-800 dark:text-primary-50 truncate">{{ $service->name }}</p>
                                <p class="text-xs text-primary-700/50 dark:text-primary-100/40 truncate">
                                    {{ $service->code }} — {{ $service->users_count }} utilisateur(s), {{ $service->courriers_count }} courrier(s)
                                </p>
                            </div>
                            <x-badge :variant="$service->is_active ? 'green' : 'gray'">
                                {{ $service->is_active ? 'Actif' : 'Inactif' }}
                            </x-badge>
                            @can('update', $service)
                                <a href="{{ route('services.edit', $service) }}" class="text-xs font-semibold text-primary-600 dark:text-primary-200 hover:text-gold-600 dark:hover:text-gold-400 transition-colors duration-200 ease-fluent">
                                    Modifier
                                </a>
                            @endcan
                            @can('delete', $service)
                                <form method="POST" action="{{ route('services.destroy', $service) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600/80 hover:text-red-600 transition-colors duration-200 ease-fluent">
                                        Supprimer
                                    </button>
                                </form>
                            @endcan
                        </li>
                    @endforeach
                </ul>

                <div class="px-6 sm:px-8 py-4 border-t border-white/50 dark:border-white/10">
                    {{ $services->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
