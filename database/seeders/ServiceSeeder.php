<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Secrétariat Général', 'code' => 'SG'],
            ['name' => 'Ressources Humaines', 'code' => 'RH'],
            ['name' => 'Urbanisme', 'code' => 'URB'],
            ['name' => 'Comptabilité', 'code' => 'COMPTA'],
            ['name' => 'Finances', 'code' => 'FIN'],
            ['name' => 'Marchés Publics', 'code' => 'MP'],
            ['name' => 'État Civil', 'code' => 'EC'],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['code' => $service['code']],
                $service + ['is_active' => true]
            );
        }
    }
}
