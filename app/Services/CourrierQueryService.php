<?php

namespace App\Services;

use App\Models\Courrier;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CourrierQueryService
{
    public function paginate(User $user, array $filters): LengthAwarePaginator
    {
        return $this->sortedQuery($user, $filters)
            ->with('service')
            ->paginate(10)
            ->withQueryString();
    }

    public function export(User $user, array $filters): Collection
    {
        return $this->sortedQuery($user, $filters)
            ->with('service')
            ->get();
    }

    public function typesFor(User $user): Collection
    {
        return $this->visibleCourriers($user)
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');
    }

    public function servicesFor(User $user): Collection
    {
        return Service::query()
            ->where('is_active', true)
            ->whereIn(
                'id',
                $this->visibleCourriers($user)
                    ->whereNotNull('service_id')
                    ->select('service_id')
                    ->distinct()
            )
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function filters(array $filters): array
    {
        return Arr::only($filters, [
            'q',
            'statut',
            'priorite',
            'type',
            'service_id',
            'date_from',
            'date_to',
        ]);
    }

    public function sort(array $filters): string
    {
        return in_array($filters['sort'] ?? null, Courrier::SORTABLE_COLUMNS, true)
            ? $filters['sort']
            : 'date_reception';
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
                Service::query()
                    ->select('name')
                    ->whereColumn('services.id', 'courriers.service_id'),
                $direction
            );
        } else {
            $query->orderBy($sort, $direction);
        }

        return $query->orderBy('courriers.id', $direction);
    }

    private function visibleCourriers(User $user): Builder
    {
        return Courrier::query()->visibleTo($user);
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->search($filters['q'] ?? null)
            ->statut($filters['statut'] ?? null)
            ->priorite($filters['priorite'] ?? null)
            ->when(filled($filters['type'] ?? null), fn (Builder $query) => $query->where('type', $filters['type']))
            ->when(filled($filters['service_id'] ?? null), fn (Builder $query) => $query->where('service_id', $filters['service_id']))
            ->when(filled($filters['date_from'] ?? null), fn (Builder $query) => $query->whereDate('date_reception', '>=', $filters['date_from']))
            ->when(filled($filters['date_to'] ?? null), fn (Builder $query) => $query->whereDate('date_reception', '<=', $filters['date_to']));
    }
}
