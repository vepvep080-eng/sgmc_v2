<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Service;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $department = Department::updateOrCreate(
            ['code' => 'GEN'],
            [
                'name' => 'Direction Générale',
                'description' => 'Département par défaut regroupant les services existants.',
                'is_active' => true,
            ],
        );

        // Attache tous les services orphelins (sans department_id) à ce
        // département par défaut, sans écraser un rattachement existant.
        Service::whereNull('department_id')->update([
            'department_id' => $department->id,
        ]);
    }
}
