<?php

namespace App\Services;

use App\Models\Courrier;
use App\Models\Department;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Logique de routage hiérarchique des courriers (Bureau d'Ordre Jerada).
 *
 * Étape 1 — Admin / Agent BO : envoi vers 1..N départements
 *   (routings.department_id rempli, service_destination_id = NULL).
 * Étape 2 — Chef de département : dispatch vers 1..N services de SON
 *   département uniquement (jamais vers un autre département).
 * Étape 3 — Chef de service : réception + traitement (aucun renvoi).
 *
 * Utilisé à 3 niveaux :
 *  1. UI Blade (selects dynamiques limités par rôle),
 *  2. FormRequest / Policy (garde-fou serveur anti-manipulation),
 *  3. Snippet Filament de référence (docs, pas de dépendance installée).
 */
class HierarchicalRoutingService
{
    /**
     * Départements que l'utilisateur peut cibler à l'Étape 1.
     * Admin/BO : tous les départements actifs. Autres rôles : aucun.
     *
     * @return Collection<int, Department>
     */
    public function targetableDepartments(User $user): Collection
    {
        if (! $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE)) {
            return collect();
        }

        return Department::active()->orderBy('name')->get();
    }

    /**
     * Services que l'utilisateur peut cibler à l'Étape 2.
     * Chef de département : services actifs de SON département uniquement.
     * Admin/BO : aucun via ce canal (ils routent vers les départements).
     *
     * @return Collection<int, Service>
     */
    public function targetableServices(User $user): Collection
    {
        if (! $user->hasRole(Role::CHEF_DEPARTEMENT)) {
            return collect();
        }

        $department = $this->managedDepartment($user);

        if ($department === null) {
            return collect();
        }

        return $department->services()->active()->orderBy('name')->get();
    }

    /**
     * Utilisateurs "Chef de Service" que l'utilisateur peut cibler.
     * Chef de département : chefs de service de SON département uniquement.
     *
     * @return Collection<int, User>
     */
    public function targetableServiceChiefs(User $user): Collection
    {
        if (! $user->hasRole(Role::CHEF_DEPARTEMENT)) {
            return collect();
        }

        $department = $this->managedDepartment($user);

        if ($department === null) {
            return collect();
        }

        $serviceIds = $department->services()->pluck('services.id');

        $chefServiceRoleId = \App\Models\Role::query()->where('slug', Role::CHEF_SERVICE)->value('id');

        return User::query()
            ->where('is_active', true)
            ->whereIn('service_id', $serviceIds)
            ->when($chefServiceRoleId, fn ($q, $roleId) => $q->where('role_id', $roleId))
            ->with('service')
            ->orderBy('name')
            ->get();
    }

    /**
     * Département piloté par ce chef (departments.manager_id = user.id).
     */
    public function managedDepartment(User $user): ?Department
    {
        return Department::query()->where('manager_id', $user->id)->first();
    }

    /**
     * Garde-fou serveur : l'envoi multi-départements (Étape 1) est-il autorisé ?
     * Non vide, existants/actifs, et expéditeur Admin/BO.
     *
     * @param  array<int>  $departmentIds
     */
    public function canSendToDepartments(User $user, array $departmentIds): bool
    {
        if (! $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE) || $departmentIds === []) {
            return false;
        }

        $valid = Department::active()->whereIn('id', $departmentIds)->pluck('id')->all();

        return count($valid) === count(array_unique($departmentIds));
    }

    /**
     * Garde-fou serveur : le dispatch multi-services (Étape 2) est-il autorisé ?
     * Tous les services doivent appartenir au département du chef expéditeur.
     *
     * @param  array<int>  $serviceIds
     */
    public function canDispatchToServices(User $user, Courrier $courrier, array $serviceIds): bool
    {
        if (! $user->hasRole(Role::CHEF_DEPARTEMENT) || $serviceIds === []) {
            return false;
        }

        $department = $this->managedDepartment($user);

        if ($department === null) {
            return false;
        }

        // Le courrier doit être en attente de sous-routage au niveau de CE département.
        $pending = $courrier->routings()
            ->where('department_id', $department->id)
            ->whereNull('service_destination_id')
            ->where('statut', \App\Models\Routing::STATUT_EN_ATTENTE)
            ->exists();

        if (! $pending) {
            return false;
        }

        $allowed = $department->services()->active()->whereIn('id', $serviceIds)->pluck('services.id')->all();

        return count($allowed) === count(array_unique($serviceIds));
    }

    /**
     * Étape de traçabilité du courrier pour l'historique :
     * recu (Étape 1) / transmis (Étape 2) / traite (Étape 3).
     */
    public function stepFor(\App\Models\Routing $routing): string
    {
        if ($routing->service_destination_id !== null) {
            return 'transmis';
        }

        if ($routing->department_id !== null) {
            return 'recu';
        }

        return 'recu';
    }
}
