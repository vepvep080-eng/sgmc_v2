<?php

namespace App\Services;

use App\Models\Courrier;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardMetricsService
{
    /**
     * Build every dashboard metric from the authenticated user's
     * visible courriers.
     */
    public function for(User $user): array
    {
        $visibleCourriers = Courrier::query()
            ->visibleTo($user);

        $courriers = (clone $visibleCourriers)
            ->with([
                'currentRouting',
                'service:id,name',
            ])
            ->get();

        $pendingCourriers = $courriers->filter(
            fn (Courrier $courrier) => $courrier->currentRouting?->isPending()
        );

        $pendingIds = $pendingCourriers->pluck('id');

        $nouveauxCourriers = $courriers->filter(
            fn (Courrier $courrier) => $courrier->statut === 'nouveau'
                && ! $pendingIds->contains($courrier->id)
        );

        return [
            'statistics' => [
                'total' => $courriers->count(),
                'nouveaux' => $nouveauxCourriers->count(),
                'en_attente' => $pendingCourriers->count(),
                'en_cours' => $courriers->where('statut', 'en_cours')->count(),
                'traites' => $courriers->where('statut', 'traite')->count(),
                'archives' => $courriers->where('statut', 'archive')->count(),
            ],

            'charts' => [
                'par_mois' => $this->courriersParMois($courriers),
                'par_service' => $this->courriersParService($courriers),
                'par_statut' => [
                    'labels' => [
                        __('app.statuses.nouveau'),
                        __('app.pending'),
                        __('app.statuses.en_cours'),
                        __('app.statuses.traite'),
                        __('app.statuses.archive'),
                    ],
                    'data' => [
                        $nouveauxCourriers->count(),
                        $pendingCourriers->count(),
                        $courriers->where('statut', 'en_cours')->count(),
                        $courriers->where('statut', 'traite')->count(),
                        $courriers->where('statut', 'archive')->count(),
                    ],
                ],
                'par_priorite' => [
                    'labels' => [
                        __('app.priorities.faible'),
                        __('app.priorities.normale'),
                        __('app.priorities.urgente'),
                        __('app.priorities.tres_urgente'),
                    ],
                    'data' => [
                        0,
                        $courriers->where('priorite', 'normale')->count(),
                        $courriers->where('priorite', 'urgente')->count(),
                        $courriers->where('priorite', 'tres_urgente')->count(),
                    ],
                ],
            ],

            'recentCourriers' => (clone $visibleCourriers)
                ->latest()
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Build a twelve-month timeline without database-specific SQL.
     */
    private function courriersParMois(Collection $courriers): array
    {
        $months = collect(range(11, 0))
            ->map(fn (int $offset) => now()->startOfMonth()->subMonths($offset));

        $countsByMonth = $courriers
            ->filter(fn (Courrier $courrier) => $courrier->date_reception !== null)
            ->countBy(
                fn (Courrier $courrier) => $courrier->date_reception->format('Y-m')
            );

        return [
            'labels' => $months
                ->map(fn ($month) => $month->locale(app()->getLocale())->translatedFormat('M Y'))
                ->all(),
            'data' => $months
                ->map(fn ($month) => $countsByMonth->get($month->format('Y-m'), 0))
                ->all(),
        ];
    }

    /**
     * Group the visible courriers by their current destination service.
     */
    private function courriersParService(Collection $courriers): array
    {
        $services = $courriers
            ->filter(fn (Courrier $courrier) => $courrier->service !== null)
            ->groupBy('service_id')
            ->map(fn (Collection $items) => [
                'label' => $items->first()->service->name,
                'value' => $items->count(),
            ])
            ->sortBy('label')
            ->values();

        return [
            'labels' => $services->pluck('label')->all(),
            'data' => $services->pluck('value')->all(),
        ];
    }
}
