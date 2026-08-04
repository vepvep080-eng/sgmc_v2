<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_chef_de_service_only_sees_its_service_statistics(): void
    {
        $chefRole = Role::create([
            'name' => 'Chef de Service',
            'slug' => Role::CHEF_SERVICE,
        ]);

        $firstService = Service::create([
            'name' => 'Service Technique',
            'code' => 'ST',
            'is_active' => true,
        ]);

        $secondService = Service::create([
            'name' => 'Ressources Humaines',
            'code' => 'RH',
            'is_active' => true,
        ]);

        $chef = User::factory()->create([
            'role_id' => $chefRole->id,
            'service_id' => $firstService->id,
        ]);

        $this->createCourrier($firstService, 'COUR-TEST-0001');
        $this->createCourrier($secondService, 'COUR-TEST-0002');

        $this->actingAs($chef)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas(
                'statistics',
                fn (array $statistics) => $statistics['total'] === 1
            );
    }

    public function test_an_administrator_sees_global_dashboard_statistics(): void
    {
        $adminRole = Role::create([
            'name' => 'Administrateur',
            'slug' => Role::ADMIN,
        ]);

        $firstService = Service::create([
            'name' => 'Service Technique',
            'code' => 'ST',
            'is_active' => true,
        ]);

        $secondService = Service::create([
            'name' => 'Ressources Humaines',
            'code' => 'RH',
            'is_active' => true,
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $this->createCourrier($firstService, 'COUR-TEST-0001');
        $this->createCourrier($secondService, 'COUR-TEST-0002');

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas(
                'statistics',
                fn (array $statistics) => $statistics['total'] === 2
            );
    }

    private function createCourrier(Service $service, string $numero): Courrier
    {
        return Courrier::create([
            'numero' => $numero,
            'objet' => 'Courrier de test',
            'expediteur' => 'Expéditeur de test',
            'date_reception' => today(),
            'priorite' => 'normale',
            'type' => 'Administratif',
            'service_id' => $service->id,
            'statut' => 'nouveau',
        ]);
    }
}
