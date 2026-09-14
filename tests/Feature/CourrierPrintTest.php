<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourrierPrintTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_print_a_filtered_list(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $matching = $this->courrier($service, ['objet' => 'Marches publics']);
        $hiddenByFilter = $this->courrier($service, ['objet' => 'Reunion interne']);

        $response = $this->actingAs($admin)->get(route('courriers.print', ['q' => 'Marches']));

        $response->assertOk();
        $response->assertSee('Liste des courriers');
        $response->assertSee($matching->numero);
        $response->assertDontSee($hiddenByFilter->numero);
    }

    public function test_a_chef_service_prints_only_his_visible_courriers(): void
    {
        $urbanisme = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $finance = Service::create(['name' => 'Finances', 'code' => 'FIN']);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $urbanisme);
        $visible = $this->courrier($urbanisme, ['objet' => 'Dossier urbanisme']);
        $hidden = $this->courrier($finance, ['objet' => 'Dossier finances']);

        $response = $this->actingAs($chef)->get(route('courriers.print'));

        $response->assertOk();
        $response->assertSee($visible->numero);
        $response->assertDontSee($hidden->numero);
    }

    public function test_an_authorized_user_can_print_an_individual_courrier(): void
    {
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $service);
        $courrier = $this->courrier($service, [
            'description' => 'Description administrative',
            'traitement_commentaire' => 'Traitement termine',
            'traite_le' => '2026-08-05 10:30:00',
            'archived_at' => '2026-08-06 11:00:00',
        ]);

        $response = $this->actingAs($chef)->get(route('courriers.print.show', $courrier));

        $response->assertOk();
        $response->assertSee($courrier->numero);
        $response->assertSee('Description administrative');
        $response->assertSee('Traitement termine');
    }

    public function test_a_chef_service_cannot_print_an_invisible_courrier(): void
    {
        $urbanisme = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $finance = Service::create(['name' => 'Finances', 'code' => 'FIN']);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $urbanisme);
        $courrier = $this->courrier($finance);

        $this->actingAs($chef)
            ->get(route('courriers.print.show', $courrier))
            ->assertForbidden();
    }

    private function userWithRole(string $slug, ?Service $service = null): User
    {
        $role = Role::firstOrCreate(
            ['slug' => $slug],
            ['name' => str($slug)->replace('_', ' ')->title()],
        );

        return User::factory()->create([
            'role_id' => $role->id,
            'service_id' => $service?->id,
        ]);
    }

    private function courrier(Service $service, array $attributes = []): Courrier
    {
        return Courrier::create(array_merge([
            'objet' => 'Objet du courrier',
            'expediteur' => 'Expediteur',
            'date_reception' => '2026-08-05',
            'reference' => 'REF-001',
            'priorite' => 'normale',
            'type' => 'Administratif',
            'service_id' => $service->id,
            'statut' => 'nouveau',
        ], $attributes));
    }
}