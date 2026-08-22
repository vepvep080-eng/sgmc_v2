<?php

namespace Tests\Feature;

use App\Models\OutgoingCourrier;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutgoingCourrierExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_exports_print_and_report_respect_service_visibility(): void
    {
        $urbanisme = Service::create(['name' => 'Urbanisme', 'code' => 'URB']); $finance = Service::create(['name' => 'Finance', 'code' => 'FIN']);
        $role = Role::create(['name' => 'Chef', 'slug' => Role::CHEF_SERVICE]); $chef = User::factory()->create(['role_id' => $role->id, 'service_id' => $urbanisme->id]);
        $creator = User::factory()->create();
        $visible = OutgoingCourrier::create(['objet'=>'Visible','destinataire'=>'A','organisme_destinataire'=>'Org','date_envoi'=>'2026-08-12','mode_envoi'=>'Ordinaire','priorite'=>'normale','service_id'=>$urbanisme->id,'created_by'=>$creator->id]);
        $hidden = OutgoingCourrier::create(['objet'=>'Hidden','destinataire'=>'B','organisme_destinataire'=>'Org','date_envoi'=>'2026-08-12','mode_envoi'=>'Ordinaire','priorite'=>'normale','service_id'=>$finance->id,'created_by'=>$creator->id]);
        $this->actingAs($chef)->get(route('courriers-depart.export.pdf', ['service_id'=>$finance->id]))->assertOk()->assertHeader('content-type','application/pdf');
        $this->actingAs($chef)->get(route('courriers-depart.export.excel', ['service_id'=>$finance->id]))->assertOk();
        $this->actingAs($chef)->get(route('courriers-depart.print'))->assertOk()->assertSee($visible->numero)->assertDontSee($hidden->numero);
        $this->actingAs($chef)->get(route('courriers-depart.report'))->assertOk()->assertSee('1');
    }

    public function test_guests_and_inactive_users_cannot_access_outputs(): void
    {
        foreach (['courriers-depart.export.pdf','courriers-depart.export.excel','courriers-depart.print','courriers-depart.report'] as $route) $this->get(route($route))->assertRedirect(route('login'));
        $inactive = User::factory()->create(['is_active'=>false]);
        $this->actingAs($inactive)->get(route('courriers-depart.report'))->assertRedirect(route('login'));
    }
}
