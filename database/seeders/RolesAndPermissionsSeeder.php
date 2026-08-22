<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            Permission::CREATE => 'Créer',
            Permission::READ => 'Consulter',
            Permission::UPDATE => 'Modifier',
            Permission::DELETE => 'Supprimer',
            Permission::EXPORT => 'Exporter',
            Permission::MANAGE_USERS => 'Gérer les utilisateurs',
        ];

        $permissionModels = [];
        foreach ($permissions as $slug => $name) {
            $permissionModels[$slug] = Permission::firstOrCreate(
                ['slug' => $slug],
                ['name' => $name]
            );
        }

        $roles = [
            Role::ADMIN => [
                'name' => 'Administrateur',
                'description' => 'Accès complet à l\'application.',
                'permissions' => array_keys($permissions),
            ],
            Role::BUREAU_ORDRE => [
                'name' => 'Agent du Bureau d\'Ordre',
                'description' => 'Saisie et gestion quotidienne des courriers.',
                'permissions' => [
                    Permission::CREATE,
                    Permission::READ,
                    Permission::UPDATE,
                    Permission::EXPORT,
                ],
            ],
            Role::CHEF_SERVICE => [
                'name' => 'Chef de Service',
                'description' => 'Suivi des courriers transférés à son service.',
                'permissions' => [
                    Permission::READ,
                    Permission::UPDATE,
                    Permission::EXPORT,
                ],
            ],
            Role::CHEF_SECTION => [
                'name' => 'Chef de Section',
                'description' => 'Distribution et suivi des courriers dans sa section.',
                'permissions' => [Permission::READ, Permission::UPDATE],
            ],
            Role::EMPLOYEE => [
                'name' => 'Employé',
                'description' => 'Consultation des courriers qui lui sont confiés.',
                'permissions' => [Permission::READ],
            ],
            Role::READ_ONLY => [
                'name' => 'Utilisateur en lecture seule',
                'description' => 'Consultation uniquement, aucune modification.',
                'permissions' => [
                    Permission::READ,
                ],
            ],
        ];

        foreach ($roles as $slug => $data) {
            $role = Role::firstOrCreate(
                ['slug' => $slug],
                ['name' => $data['name'], 'description' => $data['description']]
            );

            $ids = array_map(
                fn (string $permSlug) => $permissionModels[$permSlug]->id,
                $data['permissions']
            );

            $role->permissions()->sync($ids);
        }
    }
}
