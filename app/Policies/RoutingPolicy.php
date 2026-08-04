<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\Routing;
use App\Models\User;

class RoutingPolicy
{
    public function redirect(User $user): bool
    {
        return $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE);
    }

    public function accept(User $user, Routing $routing): bool
    {
        return $this->canActOnRouting($user, $routing);
    }

    public function reject(User $user, Routing $routing): bool
    {
        return $this->canActOnRouting($user, $routing);
    }

    public function process(User $user, Routing $routing): bool
    {
        return $user->hasRole(Role::CHEF_SERVICE)
            && $user->service_id !== null
            && $user->service_id === $routing->service_destination_id
            && $routing->statut === Routing::STATUT_ACCEPTE
            && $routing->courrier->statut === 'en_cours';
    }

    protected function canActOnRouting(User $user, Routing $routing): bool
    {
        if ($user->hasRole(Role::ADMIN)) {
            return true;
        }

        return $user->hasRole(Role::CHEF_SERVICE)
            && $routing->isPending()
            && $user->service_id !== null
            && $user->service_id === $routing->service_destination_id;
    }
}
