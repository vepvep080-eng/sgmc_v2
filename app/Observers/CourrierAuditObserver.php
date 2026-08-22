<?php

namespace App\Observers;

use App\Models\Courrier;
use App\Services\AuditService;

class CourrierAuditObserver
{
    public function created(Courrier $courrier): void
    {
        app(AuditService::class)->record(
            'created',
            $courrier,
            $courrier->service_id,
            "Création du courrier {$courrier->numero}.",
            null,
            $this->courrierValues($courrier),
        );
    }

    public function updated(Courrier $courrier): void
    {
        app(AuditService::class)->record(
            'updated',
            $courrier,
            $courrier->service_id,
            "Modification du courrier {$courrier->numero}.",
            $this->changedOriginalValues($courrier),
            $this->changedValues($courrier),
        );

        if (! $courrier->wasChanged('statut')) {
            return;
        }

        match ($courrier->statut) {
            'traite' => $this->recordStatusEvent($courrier, 'processed', 'Traitement du courrier'),
            'archive' => $this->recordStatusEvent($courrier, 'archived', 'Archivage du courrier'),
            default => null,
        };
    }

    public function deleted(Courrier $courrier): void
    {
        app(AuditService::class)->record(
            'deleted',
            $courrier,
            $courrier->service_id,
            "Suppression du courrier {$courrier->numero}.",
            $this->courrierValues($courrier),
        );
    }

    private function recordStatusEvent(Courrier $courrier, string $action, string $label): void
    {
        app(AuditService::class)->record(
            $action,
            $courrier,
            $courrier->service_id,
            "{$label} {$courrier->numero}.",
            ['statut' => $courrier->getOriginal('statut')],
            ['statut' => $courrier->statut],
        );
    }

    private function changedOriginalValues(Courrier $courrier): array
    {
        return collect(array_keys($courrier->getChanges()))
            ->reject(fn (string $key) => $key === 'updated_at')
            ->mapWithKeys(fn (string $key) => [$key => $courrier->getOriginal($key)])
            ->all();
    }

    private function changedValues(Courrier $courrier): array
    {
        return collect($courrier->getChanges())
            ->except('updated_at')
            ->all();
    }

    private function courrierValues(Courrier $courrier): array
    {
        return $courrier->only([
            'numero',
            'objet',
            'expediteur',
            'date_reception',
            'reference',
            'priorite',
            'type',
            'service_id',
            'statut',
        ]);
    }
}
