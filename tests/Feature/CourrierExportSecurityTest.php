<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use App\Services\CourrierQueryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class CourrierExportSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_access_all_list_document_endpoints(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = $this->service('URB', 'Urbanisme');
        $this->courrier($service);

        $this->actingAs($admin)
            ->get(route('courriers.export.pdf'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($admin)
            ->get(route('courriers.export.excel'))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        $this->actingAs($admin)
            ->get(route('courriers.print'))
            ->assertOk();
    }

    public function test_a_bureau_dordre_exports_only_the_courriers_in_its_existing_visibility_scope(): void
    {
        $bureau = $this->userWithRole(Role::BUREAU_ORDRE);
        $urbanisme = $this->service('URB', 'Urbanisme');
        $finance = $this->service('FIN', 'Finances');
        $urbanismeCourrier = $this->courrier($urbanisme);
        $financeCourrier = $this->courrier($finance);

        $results = app(CourrierQueryService::class)->export($bureau, []);

        $this->assertTrue($results->contains($urbanismeCourrier));
        $this->assertTrue($results->contains($financeCourrier));
    }

    public function test_a_chef_service_cannot_bypass_list_visibility_with_service_or_sort_parameters(): void
    {
        $urbanisme = $this->service('URB', 'Urbanisme');
        $finance = $this->service('FIN', 'Finances');
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $urbanisme);
        $visible = $this->courrier($urbanisme, ['objet' => 'Dossier urbanisme']);
        $hidden = $this->courrier($finance, ['objet' => 'Dossier finances']);
        $parameters = ['service_id' => $finance->id, 'sort' => 'service', 'dir' => 'asc'];

        $results = app(CourrierQueryService::class)->export($chef, $parameters);
        $this->assertFalse($results->contains($hidden));
        $this->assertCount(0, $results);

        $this->actingAs($chef)
            ->get(route('courriers.export.pdf', $parameters))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $excel = $this->actingAs($chef)
            ->get(route('courriers.export.excel', $parameters));
        $excel->assertOk();
        $this->assertCount(1, $this->spreadsheetRows($excel->streamedContent()));

        $this->actingAs($chef)
            ->get(route('courriers.print', $parameters))
            ->assertOk()
            ->assertDontSee($visible->numero)
            ->assertDontSee($hidden->numero);
    }

    public function test_a_chef_service_cannot_bypass_individual_document_authorization_by_changing_the_courrier_id(): void
    {
        $urbanisme = $this->service('URB', 'Urbanisme');
        $finance = $this->service('FIN', 'Finances');
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $urbanisme);
        $hidden = $this->courrier($finance);

        $this->actingAs($chef)
            ->get(route('courriers.export.pdf.show', $hidden))
            ->assertForbidden();

        $this->actingAs($chef)
            ->get(route('courriers.print.show', $hidden))
            ->assertForbidden();
    }

    public function test_an_authenticated_user_without_a_role_cannot_export_or_print_courriers(): void
    {
        $service = $this->service('URB', 'Urbanisme');
        $courrier = $this->courrier($service);
        $user = User::factory()->create(['service_id' => $service->id]);

        $this->assertCount(0, app(CourrierQueryService::class)->export($user, ['service_id' => $service->id]));

        $this->actingAs($user)
            ->get(route('courriers.export.pdf', ['service_id' => $service->id]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $excel = $this->actingAs($user)
            ->get(route('courriers.export.excel', ['service_id' => $service->id]));
        $excel->assertOk();
        $this->assertCount(1, $this->spreadsheetRows($excel->streamedContent()));

        $this->actingAs($user)
            ->get(route('courriers.print', ['service_id' => $service->id]))
            ->assertOk()
            ->assertDontSee($courrier->numero);
    }

    public function test_guests_cannot_access_export_or_print_endpoints(): void
    {
        $service = $this->service('URB', 'Urbanisme');
        $courrier = $this->courrier($service);

        foreach ([
            route('courriers.export.pdf'),
            route('courriers.export.excel'),
            route('courriers.print'),
            route('courriers.export.pdf.show', $courrier),
            route('courriers.print.show', $courrier),
        ] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    private function spreadsheetRows(string $content): array
    {
        $path = tempnam(sys_get_temp_dir(), 'courriers-');
        file_put_contents($path, $content);

        try {
            return IOFactory::load($path)->getActiveSheet()->toArray();
        } finally {
            @unlink($path);
        }
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

    private function service(string $code, string $name): Service
    {
        return Service::create(['name' => $name, 'code' => $code]);
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
