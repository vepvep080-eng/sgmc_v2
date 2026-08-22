<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\Role;
use App\Models\User;

class AuditLogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->hasRole(Role::ADMIN);
    }

    public function update(User $user, AuditLog $auditLog): bool
    {
        return false;
    }

    public function delete(User $user, AuditLog $auditLog): bool
    {
        return false;
    }
}
