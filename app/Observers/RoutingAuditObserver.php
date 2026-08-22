<?php

namespace App\Observers;

use App\Models\Routing;
use App\Services\AuditService;

class RoutingAuditObserver
{
    public function created(Routing $routing): void
    {
        app(AuditService::class)->record(
            'routed',
            $routing->courrier,
            $routing->service_destination_id,
            "Routage du courrier {$routing->courrier->numero}.",
            null,
            [
                'service_source_id' => $routing->service_source_id,
                'service_destination_id' => $routing->service_destination_id,
                'department_source_id' => $routing->serviceSource?->department_id,
                'department_destination_id' => $routing->serviceDestination?->department_id,
                'recipient_user_id' => $routing->recipient_user_id,
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
