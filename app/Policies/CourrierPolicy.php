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

        if ($user->hasRole(Role::CHEF_DEPARTEMENT)) {
            $department = \App\Models\Department::where('manager_id', $user->id)->first();

            if ($department === null) {
                return false;
            }

            // Visible si routé vers SON département OU rattaché à un de ses services.
            return $courrier->routings()->where('department_id', $department->id)->exists()
                || ($courrier->service_id !== null
                    && $department->services()->whereKey($courrier->service_id)->exists());
        }

        return ($user->hasRole(Role::CHEF_SERVICE)
            && $user->service_id !== null
            && ($user->service_id === $courrier->service_id
                || $courrier->routings()->where('service_destination_id', $user->service_id)->exists()))
            || ($user->hasRole(Role::CHEF_SECTION) && $user->section_id !== null && $user->section_id === $courrier->section_id)
            || ($user->hasRole(Role::EMPLOYEE) && $user->id === $courrier->assigned_user_id);
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
