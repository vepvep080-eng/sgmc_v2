<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Tests\TestCase;

class CourrierExcelExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_user_can_download_a_filtered_excel_export(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $matching = $this->courrier($service, ['objet' => "March\xC3\xA9 public"]);
        $this->courrier($service, ['objet' => "R\xC3\xA9union interne"]);

        $response = $this->actingAs($admin)->get(route('courriers.export.excel', ['q' => "March\xC3\xA9"]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $content = $response->streamedContent();
        $this->assertStringStartsWith('PK', $content);

        $rows = $this->spreadsheetRows($content);

        $this->assertSame([
            "Num\xC3\xA9ro", 'Objet', "Exp\xC3\xA9diteur", 'Service', "Date de r\xC3\xA9ception", "Priorit\xC3\xA9", 'Statut', 'Date de traitement', "Date d'archivage",
        ], $rows[0]);
        $this->assertCount(2, $rows);
        $this->assertSame($matching->numero, $rows[1][0]);
        $this->assertSame("March\xC3\xA9 public", $rows[1][1]);
    }

    public function test_a_chef_service_cannot_export_courriers_outside_his_visible_scope(): void
    {
        $urbanisme = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $finance = Service::create(['name' => 'Finances', 'code' => 'FIN']);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $urbanisme);
        $visible = $this->courrier($urbanisme, ['objet' => 'Dossier urbanisme']);
        $hidden = $this->courrier($finance, ['objet' => 'Dossier finances']);

        $response = $this->actingAs($chef)->get(route('courriers.export.excel'));

        $response->assertOk();
        $numbers = collect($this->spreadsheetRows($response->streamedContent()))->skip(1)->pluck(0);

        $this->assertTrue($numbers->contains($visible->numero));
        $this->assertFalse($numbers->contains($hidden->numero));
    }

    public function test_the_existing_pdf_export_remains_functional(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $this->courrier($service);

        $response = $this->actingAs($admin)->get(route('courriers.export.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
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
            'expediteur' => "Exp\xC3\xA9diteur",
            'date_reception' => '2026-08-05',
            'reference' => 'REF-001',
            'priorite' => 'normale',
            'type' => 'Administratif',
            'service_id' => $service->id,
            'statut' => 'nouveau',
        ], $attributes));
    }
}