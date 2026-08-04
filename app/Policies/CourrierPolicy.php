<?php

namespace App\Policies;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\User;

class CourrierPolicy
{
    public function viewAny(User $user): bool
    {
        // The list itself is scoped per-service via Courrier::scopeVisibleTo()
        // in CourrierController@index, so any authenticated user may reach it.
        return true;
    }

    public function view(User $user, Courrier $courrier): bool
    {
        if ($user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE, Role::READ_ONLY)) {
            return true;
        }

        return $user->hasRole(Role::CHEF_SERVICE)
            && $user->service_id !== null
            && $user->service_id === $courrier->service_id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE);
    }

    public function update(User $user, Courrier $courrier): bool
    {
        return $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE);
    }

    public function delete(User $user, Courrier $courrier): bool
    {
        return $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE);
    }

    /**
     * Archiving is restricted to a processed courrier and to
     * Bureau d'Ordre agents or administrators.
     */
    public function archive(User $user, Courrier $courrier): bool
    {
        return $courrier->statut === 'traite'
            && $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE);
    }
}
