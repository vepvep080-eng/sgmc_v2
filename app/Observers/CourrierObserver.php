<?php

namespace App\Observers;

use App\Models\Courrier;
use App\Services\CourrierNotificationService;

class CourrierObserver
{
    public bool $afterCommit = true;

    public function updated(Courrier $courrier): void
    {
        if (! $courrier->wasChanged('statut')) {
            return;
        }

        match ($courrier->statut) {
            'traite' => app(CourrierNotificationService::class)->processed($courrier),
            'archive' => app(CourrierNotificationService::class)->archived($courrier),
            default => null,
        };
    }
}