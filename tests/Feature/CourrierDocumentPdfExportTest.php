<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourrierDocumentPdfExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authorized_user_can_export_an_individual_courrier_pdf(): void
    {
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $admin = $this->userWithRole(Role::ADMIN);
        $courrier = $this->courrier($service, ['description' => 'Description administrative']);

        $response = $this->actingAs($admin)->get(route('courriers.export.pdf.show', $courrier));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_a_user_without_view_permission_cannot_export_an_individual_courrier_pdf(): void
    {
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $courrier = $this->courrier($service);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('courriers.export.pdf.show', $courrier))
            ->assertForbidden();
    }

    public function test_a_chef_service_cannot_export_another_services_courrier(): void
    {
        $urbanisme = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $finance = Service::create(['name' => 'Finances', 'code' => 'FIN']);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $urbanisme);
        $courrier = $this->courrier($finance);

        $this->actingAs($chef)
            ->get(route('courriers.export.pdf.show', $courrier))
            ->assertForbidden();
    }

    public function test_the_existing_list_pdf_export_remains_functional(): void
    {
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $admin = $this->userWithRole(Role::ADMIN);
        $this->courrier($service);

        $response = $this->actingAs($admin)->get(route('courriers.export.pdf'));

        $response->assertOk();
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_the_existing_excel_export_remains_functional(): void
    {
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $admin = $this->userWithRole(Role::ADMIN);
        $this->courrier($service);

        $response = $this->actingAs($admin)->get(route('courriers.export.excel'));

        $response->assertOk();
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    public function test_the_existing_individual_print_view_remains_functional(): void
    {
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $admin = $this->userWithRole(Role::ADMIN);
        $courrier = $this->courrier($service);

        $this->actingAs($admin)
            ->get(route('courriers.print.show', $courrier))
            ->assertOk()
            ->assertSee($courrier->numero);
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