<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Services\CourrierQueryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourrierPdfExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authorized_user_can_download_a_filtered_pdf(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);

        $matching = $this->courrier($service, ['objet' => 'Marchأ© public', 'statut' => 'nouveau']);
        $this->courrier($service, ['objet' => 'Rأ©union interne', 'statut' => 'archive']);

        $results = app(CourrierQueryService::class)->export($admin, ['q' => 'Marchأ©']);

        $this->assertCount(1, $results);
        $this->assertTrue($results->first()->is($matching));

        $response = $this->actingAs($admin)->get(route('courriers.export.pdf', ['q' => 'Marchأ©']));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_a_chef_service_cannot_export_courriers_outside_his_visible_scope(): void
    {
        $urbanisme = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $finance = Service::create(['name' => 'Finances', 'code' => 'FIN']);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $urbanisme);

        $visible = $this->courrier($urbanisme, ['objet' => 'Dossier urbanisme']);
        $hidden = $this->courrier($finance, ['objet' => 'Dossier finances']);

        $results = app(CourrierQueryService::class)->export($chef, []);

        $this->assertTrue($results->contains($visible));
        $this->assertFalse($results->contains($hidden));

        $response = $this->actingAs($chef)->get(route('courriers.export.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    private function userWithRole(string $slug, ?Service $service = null): User
    {
        $role = Role::create([
            'name' => str($slug)->replace('_', ' ')->title(),
            'slug' => $slug,
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'service_id' => $service?->id,
        ]);
    }

    private function courrier(Service $service, array $attributes = []): Courrier
    {
        return Courrier::create(array_merge([
            'objet' => 'Objet du courrier',
            'expediteur' => 'Expأ©diteur',
            'date_reception' => '2026-08-05',
            'reference' => 'REF-001',
            'priorite' => 'normale',
            'type' => 'Administratif',
            'service_id' => $service->id,
            'statut' => 'nouveau',
        ], $attributes));
    }
}
