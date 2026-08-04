<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('manage_users');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasPermission('manage_users');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('manage_users');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasPermission('manage_users');
    }

    public function delete(User $user, User $model): bool
    {
        // Prevent an admin from deleting their own account by accident.
        return $user->hasPermission('manage_users') && $user->id !== $model->id;
    }
}
