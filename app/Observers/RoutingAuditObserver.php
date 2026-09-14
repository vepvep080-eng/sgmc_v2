<?php

namespace App\Observers;

use App\Models\Routing;
use App\Services\AuditService;

class RoutingAuditObserver
{
    public function created(Routing $routing): void
    {
        $courrier = $routing->courrier;
        $step = $routing->service_destination_id !== null ? 'transmis' : 'recu';
        $destination = $routing->service_destination_id !== null
            ? ($routing->serviceDestination?->name ?? '#'.$routing->service_destination_id)
            : ($routing->department?->name ?? ($routing->department_id ? '#'.$routing->department_id : '—'));

        app(AuditService::class)->record(
            'routed',
            $courrier,
            $routing->service_destination_id,
            "Routage du courrier {$courrier->numero} ({$step} → {$destination}) par {$routing->routedBy?->name}.",
            null,
            [
                'etape' => $step,
                'service_source_id' => $routing->service_source_id,
                'service_destination_id' => $routing->service_destination_id,
                'department_id' => $routing->department_id,
                'department_source_id' => $routing->serviceSource?->department_id,
                'department_destination_id' => $routing->serviceDestination?->department_id
                    ?? $routing->department_id,
                'recipient_user_id' => $routing->recipient_user_id,
                'routed_by' => $routing->routed_by,
                'commentaire' => $routing->commentaire,
            ],
        );
    }

    public function updated(Routing $routing): void
    {
        if (! $routing->wasChanged('statut')) {
            return;
        }

        $action = match ($routing->statut) {
            Routing::STATUT_ACCEPTE => 'received',
            Routing::STATUT_REFUSE => 'rejected',
            default => null,
        };

        if ($action === null) {
            return;
        }

        $description = $action === 'received'
            ? "Réception du courrier {$routing->courrier->numero}."
            : "Rejet du courrier {$routing->courrier->numero}.";

        app(AuditService::class)->record(
            $action,
            $routing->courrier,
            $routing->service_destination_id,
            $description,
            ['statut' => $routing->getOriginal('statut')],
            [
                'statut' => $routing->statut,
                'commentaire' => $routing->commentaire,
            ],
        );
    }
}
