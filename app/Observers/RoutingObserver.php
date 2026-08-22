<?php

namespace App\Observers;

use App\Models\Routing;
use App\Services\CourrierNotificationService;

class RoutingObserver
{
    public bool $afterCommit = true;

    public function created(Routing $routing): void
    {
        app(CourrierNotificationService::class)->routed($routing);
    }
}