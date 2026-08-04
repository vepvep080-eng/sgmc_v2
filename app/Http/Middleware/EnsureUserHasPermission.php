<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasPermission
{
    /**
     * Usage in routes: ->middleware('permission:manage_users')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active || ! $user->hasPermission($permission)) {
            abort(403, "Vous n'avez pas la permission nécessaire pour accéder à cette page.");
        }

        return $next($request);
    }
}
