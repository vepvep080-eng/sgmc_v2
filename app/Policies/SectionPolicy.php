<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\Section;
use App\Models\User;

class SectionPolicy
{
    public function viewAny(User $user): bool { return $user->hasRole(Role::ADMIN, Role::CHEF_SERVICE); }
    public function create(User $user): bool { return $user->hasRole(Role::ADMIN); }
    public function update(User $user, Section $section): bool { return $user->hasRole(Role::ADMIN); }
    public function delete(User $user, Section $section): bool { return $user->hasRole(Role::ADMIN); }
}
