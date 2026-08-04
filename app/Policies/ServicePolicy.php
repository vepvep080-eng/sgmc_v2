<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Service $service): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user, Service $service): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->hasRole(Role::ADMIN);
    }
}
