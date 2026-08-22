<?php

namespace App\Observers;

use App\Models\OutgoingCourrier;
use App\Services\AuditService;

class OutgoingCourrierAuditObserver
{
    public function created(OutgoingCourrier $courrier): void
    {
        app(AuditService::class)->record('created', $courrier, $courrier->service_id, "Création du courrier départ {$courrier->numero}.", null, $this->values($courrier));
    }

    public function updated(OutgoingCourrier $courrier): void
    {
        $changes = collect($courrier->getChanges())->except('updated_at')->all();

        if ($changes === []) {
            return;
        }

        $oldValues = collect(array_keys($changes))
            ->mapWithKeys(fn (string $key) => [$key => $courrier->getOriginal($key)])
            ->all();

        app(AuditService::class)->record('updated', $courrier, $courrier->service_id, "Modification du courrier départ {$courrier->numero}.", $oldValues, $changes);

        if (! $courrier->wasChanged('statut')) {
            return;
        }

        $action = match ($courrier->statut) {
            'pret' => 'prepared',
            'envoye' => 'sent',
            'archive' => 'archived',
            default => null,
        };

        if ($action !== null) {
            app(AuditService::class)->record(
                $action,
                $courrier,
                $courrier->service_id,
                "Changement de statut du courrier départ {$courrier->numero}.",
                ['statut' => $courrier->getOriginal('statut')],
                ['statut' => $courrier->statut],
            );
        }
    }

    public function deleted(OutgoingCourrier $courrier): void
    {
        app(AuditService::class)->record('deleted', $courrier, $courrier->service_id, "Suppression du courrier départ {$courrier->numero}.", $this->values($courrier));
    }

    private function values(OutgoingCourrier $courrier): array
    {
        return $courrier->only(['numero', 'objet', 'destinataire', 'organisme_destinataire', 'date_envoi', 'mode_envoi', 'reference_postale', 'priorite', 'statut', 'service_id']);
    }
}
