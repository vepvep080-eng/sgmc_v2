<?php

namespace App\Policies;

use App\Models\OutgoingCourrier;
use App\Models\Role;
use App\Models\User;

class OutgoingCourrierPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, OutgoingCourrier $courrier): bool
    {
        if ($user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE, Role::READ_ONLY)) {
            return true;
        }

        return ($user->hasRole(Role::CHEF_SERVICE)
            && $user->service_id !== null
            && $user->service_id === $courrier->service_id)
            || ($user->hasRole(Role::CHEF_SECTION) && $user->section_id !== null && $user->section_id === $courrier->section_id)
            || ($user->hasRole(Role::EMPLOYEE) && $user->id === $courrier->assigned_user_id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE);
    }

    public function update(User $user, OutgoingCourrier $courrier): bool
    {
        return $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE);
    }

    public function delete(User $user, OutgoingCourrier $courrier): bool
    {
        return $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE);
    }

    public function prepare(User $user, OutgoingCourrier $courrier): bool
    {
        return $courrier->statut === 'brouillon' && $this->canManage($user);
    }

    public function send(User $user, OutgoingCourrier $courrier): bool
    {
        return $courrier->statut === 'pret' && $this->canManage($user);
    }

    public function archive(User $user, OutgoingCourrier $courrier): bool
    {
        return $courrier->statut === 'envoye' && $this->canManage($user);
    }

    private function canManage(User $user): bool
    {
        return $user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE);
    }
}
