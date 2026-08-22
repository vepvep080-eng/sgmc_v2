<?php

namespace App\Observers;

use App\Models\OutgoingCourrier;
use App\Services\CourrierNotificationService;

class OutgoingCourrierNotificationObserver
{
    public bool $afterCommit = true;

    public function updated(OutgoingCourrier $courrier): void
    {
        if (! $courrier->wasChanged('statut')) {
            return;
        }

        match ($courrier->statut) {
            'envoye' => app(CourrierNotificationService::class)->outgoingSent($courrier),
            'archive' => app(CourrierNotificationService::class)->outgoingArchived($courrier),
            default => null,
        };
    }
}
