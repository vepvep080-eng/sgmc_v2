<?php

namespace App\Services;

use App\Models\OutgoingCourrier;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class OutgoingCourrierQueryService
{
    public function paginate(User $user, array $filters): LengthAwarePaginator
    {
        return $this->sortedQuery($user, $filters)
            ->with('service:id,name')
            ->paginate(10)
            ->withQueryString();
    }

    public function export(User $user, array $filters): Collection
    {
        return $this->sortedQuery($user, $filters)->with('service:id,name')->get();
    }

    public function report(User $user, array $filters): array
    {
        $courriers = $this->export($user, $filters);

        return [
            'total' => $courriers->count(),
            'byStatus' => $courriers->countBy('statut'),
            'byPriority' => $courriers->countBy('priorite'),
            'byService' => $courriers->groupBy('service_id')->map(fn (Collection $items) => ['label' => $items->first()->service?->name ?? '-', 'value' => $items->count()])->values(),
            'byMode' => $courriers->countBy('mode_envoi'),
            'byMonth' => $courriers->filter(fn ($courrier) => $courrier->date_envoi)->countBy(fn ($courrier) => $courrier->date_envoi->format('Y-m')),
        ];
    }

    public function servicesFor(User $user): Collection
    {
        return Service::query()
            ->active()
            ->whereIn('id', $this->visibleCourriers($user)->select('service_id')->distinct())
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function filters(array $filters): array
    {
        return Arr::only($filters, ['q', 'statut', 'priorite', 'mode_envoi', 'service_id', 'date_from', 'date_to']);
    }

    public function sort(array $filters): string
    {
        return in_array($filters['sort'] ?? null, OutgoingCourrier::SORTABLE_COLUMNS, true)
            ? $filters['sort']
            : 'date_envoi';
    }

    public function direction(array $filters): string
    {
        return ($filters['dir'] ?? null) === 'asc' ? 'asc' : 'desc';
    }

    private function sortedQuery(User $user, array $filters): Builder
    {
        $sort = $this->sort($filters);
        $direction = $this->direction($filters);
        $query = $this->applyFilters($this->visibleCourriers($user), $filters);

        if ($sort === 'service') {
            $query->orderBy(
                Service::query()->select('name')->whereColumn('services.id', 'outgoing_courriers.service_id'),
                $direction,
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        return $query->orderBy('outgoing_courriers.id', $direction);
    }

    private function visibleCourriers(User $user): Builder
    {
        return OutgoingCourrier::query()->visibleTo($user);
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['q'] ?? null)
            ->when(filled($filters['statut'] ?? null), fn (Builder $query) => $query->where('statut', $filters['statut']))
            ->when(filled($filters['priorite'] ?? null), fn (Builder $query) => $query->where('priorite', $filters['priorite']))
            ->when(filled($filters['mode_envoi'] ?? null), fn (Builder $query) => $query->where('mode_envoi', $filters['mode_envoi']))
            ->when(filled($filters['service_id'] ?? null), fn (Builder $query) => $query->where('service_id', $filters['service_id']))
            ->when(filled($filters['date_from'] ?? null), fn (Builder $query) => $query->whereDate('date_envoi', '>=', $filters['date_from']))
            ->when(filled($filters['date_to'] ?? null), fn (Builder $query) => $query->whereDate('date_envoi', '<=', $filters['date_to']));
    }
}
