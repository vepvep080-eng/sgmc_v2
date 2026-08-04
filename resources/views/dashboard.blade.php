@extends('layouts.app')

@section('title', "Tableau de bord — Bureau d'Ordre")
@section('page-title', 'Tableau de bord')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4 opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div>
            <h2 class="font-display font-semibold text-2xl text-primary-800 dark:text-primary-50">
                Bienvenue, {{ Auth::user()->name }}
            </h2>
            <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
                Aperçu en temps réel de l'activité du Bureau d'Ordre.
            </p>
        </div>

        @can('create', \App\Models\Courrier::class)
            <a href="{{ route('courriers.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 px-4 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_-8px_rgba(11,61,145,0.45)] transition-transform duration-200 ease-fluent hover:scale-[1.02]">
                <x-icon name="plus-simple" class="w-4 h-4" />
                Ajouter un courrier
            </a>
        @endcan
    </div>

    <div class="mt-7 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3 opacity-0 animate-fadeInUp" style="animation-delay: 0.1s;">
        <x-stat-card icon="inbox" label="Total des courriers" :value="$statistics['total']" accent="primary" />
        <x-stat-card icon="plus" label="Nouveaux" :value="$statistics['nouveaux']" accent="gold" />
        <x-stat-card icon="clock" label="En attente" :value="$statistics['en_attente']" accent="gold" />
        <x-stat-card icon="refresh" label="En cours" :value="$statistics['en_cours']" accent="secondary" />
        <x-stat-card icon="check-circle" label="Traités" :value="$statistics['traites']" accent="secondary" />
        <x-stat-card icon="archive" label="Archivés" :value="$statistics['archives']" accent="primary" />
    </div>

    <div
        data-dashboard-charts
        data-chart-data="{{ json_encode($charts) }}"
        class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-2 opacity-0 animate-fadeInUp"
        style="animation-delay: 0.15s;"
    >
        <section class="relative overflow-hidden rounded-[24px] border border-white/70 bg-white/55 px-6 py-6 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] dark:shadow-glass-lg-dark sm:px-8">
            <div class="absolute left-8 top-0 h-[2px] w-16 rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                Courriers par mois
            </h3>
            <p class="mt-1 text-xs text-primary-700/55 dark:text-primary-100/45">
                Évolution des réceptions sur les douze derniers mois.
            </p>

            <div class="mt-6 h-72">
                <canvas id="courriers-par-mois-chart" aria-label="Évolution mensuelle des courriers" role="img"></canvas>
            </div>
        </section>

        <section class="relative overflow-hidden rounded-[24px] border border-white/70 bg-white/55 px-6 py-6 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] dark:shadow-glass-lg-dark sm:px-8">
            <div class="absolute left-8 top-0 h-[2px] w-16 rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                Courriers par service
            </h3>
            <p class="mt-1 text-xs text-primary-700/55 dark:text-primary-100/45">
                Répartition des courriers par service destinataire.
            </p>

            <div class="mt-6 h-72">
                <canvas id="courriers-par-service-chart" aria-label="Répartition des courriers par service" role="img"></canvas>
            </div>
        </section>

        <section class="relative overflow-hidden rounded-[24px] border border-white/70 bg-white/55 px-6 py-6 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] dark:shadow-glass-lg-dark sm:px-8">
            <div class="absolute left-8 top-0 h-[2px] w-16 rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                Répartition par statut
            </h3>
            <p class="mt-1 text-xs text-primary-700/55 dark:text-primary-100/45">
                Situation actuelle des courriers visibles.
            </p>

            <div class="mt-6 h-72">
                <canvas id="repartition-statut-chart" aria-label="Répartition des courriers par statut" role="img"></canvas>
            </div>
        </section>

        <section class="relative overflow-hidden rounded-[24px] border border-white/70 bg-white/55 px-6 py-6 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] dark:shadow-glass-lg-dark sm:px-8">
            <div class="absolute left-8 top-0 h-[2px] w-16 rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                Répartition par priorité
            </h3>
            <p class="mt-1 text-xs text-primary-700/55 dark:text-primary-100/45">
                Niveau de priorité des courriers visibles.
            </p>

            <div class="mt-6 h-72">
                <canvas id="repartition-priorite-chart" aria-label="Répartition des courriers par priorité" role="img"></canvas>
            </div>
        </section>
    </div>

    <section class="mt-8 opacity-0 animate-fadeInUp" style="animation-delay: 0.2s;">
        <div class="relative overflow-hidden rounded-[24px] border border-white/70 bg-white/55 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] dark:shadow-glass-lg-dark">
            <div class="absolute left-8 top-0 h-[2px] w-16 rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <div class="flex items-center justify-between border-b border-white/50 px-6 py-5 dark:border-white/10 sm:px-8">
                <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                    Courriers récemment ajoutés
                </h3>

                <a href="{{ route('courriers.index') }}" class="text-xs font-semibold tracking-wide text-primary-600 transition-colors duration-200 ease-fluent hover:text-gold-600 dark:text-primary-200 dark:hover:text-gold-400">
                    Voir tout →
                </a>
            </div>

            @if ($recentCourriers->isEmpty())
                <x-empty-state
                    icon="inbox"
                    title="Aucun courrier pour le moment"
                    description="Les courriers que vous ajoutez apparaîtront ici."
                >
                    @can('create', \App\Models\Courrier::class)
                        <x-slot:action>
                            <a href="{{ route('courriers.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_-8px_rgba(11,61,145,0.45)] transition-transform duration-200 ease-fluent hover:scale-[1.02]">
                                <x-icon name="plus-simple" class="w-4 h-4" />
                                Ajouter un courrier
                            </a>
                        </x-slot:action>
                    @endcan
                </x-empty-state>
            @else
                <ul class="divide-y divide-white/40 dark:divide-white/5">
                    @foreach ($recentCourriers as $courrier)
                        <li>
                            <a href="{{ route('courriers.show', $courrier) }}" class="flex items-center gap-4 px-6 py-4 transition-colors duration-200 ease-fluent hover:bg-primary-500/[0.04] dark:hover:bg-white/[0.03] sm:px-8">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-primary-500/8 text-primary-500/60 dark:bg-white/5 dark:text-primary-100/50">
                                    <x-icon name="inbox" class="w-4 h-4" />
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium text-primary-800 dark:text-primary-50">
                                        {{ $courrier->objet }}
                                    </p>
                                    <p class="truncate text-xs text-primary-700/50 dark:text-primary-100/40">
                                        {{ $courrier->numero }} — {{ $courrier->expediteur }}
                                    </p>
                                </div>

                                <x-badge :variant="$courrier->priorityVariant()">
                                    {{ $courrier->priorityLabel() }}
                                </x-badge>

                                <span class="hidden tabular-nums text-xs text-primary-700/45 dark:text-primary-100/35 sm:block">
                                    {{ $courrier->date_reception?->locale('fr')->translatedFormat('d F Y') ?? '—' }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
@endsection

@push('scripts')
    @vite('resources/js/dashboard.js')
@endpush
