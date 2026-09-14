<?php

namespace App\Policies;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\Routing;
use App\Models\User;
use App\Services\HierarchicalRoutingService;

class RoutingPolicy
{
    public function redirect(User $user): bool
    {
        // Seuls Admin/BO (Étape 1) et Chefs de département (Étape 2) routent.
        // Les chefs de service TRAITENT (process), ils ne re-routent pas.
        return $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE, Role::CHEF_DEPARTEMENT);
    }

    /**
     * Envoi hiérarchique : Admin/BO -> N départements (Étape 1),
     * Chef département -> N services de SON département (Étape 2).
     * Bloque aussi : chef -> autre département, chef -> chef, service -> renvoi.
     *
     * @param  array<int>|null  $departmentIds  cibles Étape 1
     * @param  array<int>|null  $serviceIds     cibles Étape 2
     */
    public function routeHierarchically(
        User $user,
        Courrier $courrier,
        ?array $departmentIds = null,
        ?array $serviceIds = null,
        ?HierarchicalRoutingService $hierarchy = null,
    ): bool {
        $hierarchy ??= app(HierarchicalRoutingService::class);

        if ($user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE)) {
            // Étape 1 : départements uniquement, jamais de service direct
            // (sinon on court-circuite le chef de département).
            return $departmentIds !== null
                && $serviceIds === null
                && $hierarchy->canSendToDepartments($user, $departmentIds);
        }

        if ($user->hasRole(Role::CHEF_DEPARTEMENT)) {
            // Étape 2 : services de SON département uniquement.
            return $serviceIds !== null
                && $departmentIds === null
                && $hierarchy->canDispatchToServices($user, $courrier, $serviceIds);
        }

        return false;
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

