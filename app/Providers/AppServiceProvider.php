<?php

namespace App\Providers;

use App\Models\Courrier;
use App\Models\OutgoingCourrier;
use App\Models\Routing;
use App\Observers\CourrierAuditObserver;
use App\Observers\CourrierObserver;
use App\Observers\OutgoingCourrierAuditObserver;
use App\Observers\OutgoingCourrierNotificationObserver;
use App\Observers\RoutingAuditObserver;
use App\Observers\RoutingObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Courrier::observe(CourrierObserver::class);
        Courrier::observe(CourrierAuditObserver::class);
        OutgoingCourrier::observe(OutgoingCourrierAuditObserver::class);
        OutgoingCourrier::observe(OutgoingCourrierNotificationObserver::class);
        Routing::observe(RoutingObserver::class);
        Routing::observe(RoutingAuditObserver::class);
    }
}
