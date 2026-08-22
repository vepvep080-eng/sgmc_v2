<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\OutgoingCourrier;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutgoingCourrierWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authorized_user_can_complete_the_outgoing_workflow_and_it_is_audited(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $courrier = $this->courrier($this->service('URB', 'Urbanisme'));

        $this->actingAs($admin)->post(route('courriers-depart.prepare', $courrier))->assertRedirect();
        $this->actingAs($admin)->post(route('courriers-depart.send', $courrier))->assertRedirect();
        $this->actingAs($admin)->post(route('courriers-depart.archive', $courrier))->assertRedirect();

        $this->assertSame('archive', $courrier->fresh()->statut);
        $this->assertDatabaseHas('audit_logs', ['action' => 'prepared', 'auditable_type' => OutgoingCourrier::class, 'auditable_id' => $courrier->id, 'user_id' => $admin->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'sent', 'auditable_type' => OutgoingCourrier::class, 'auditable_id' => $courrier->id, 'user_id' => $admin->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'archived', 'auditable_type' => OutgoingCourrier::class, 'auditable_id' => $courrier->id, 'user_id' => $admin->id]);
    }

    public function test_invalid_or_unauthorized_workflow_transitions_are_rejected(): void
    {
        $service = $this->service('URB', 'Urbanisme');
        $admin = $this->userWithRole(Role::ADMIN);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $service);
        $courrier = $this->courrier($service);

        $this->actingAs($admin)->post(route('courriers-depart.send', $courrier))->assertForbidden();
        $this->actingAs($chef)->post(route('courriers-depart.prepare', $courrier))->assertForbidden();
        $this->assertSame('brouillon', $courrier->fresh()->statut);
    }

    public function test_a_chef_service_cannot_view_or_download_another_services_outgoing_courrier(): void
    {
        $urbanisme = $this->service('URB', 'Urbanisme');
        $finance = $this->service('FIN', 'Finances');
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $urbanisme);
        $courrier = $this->courrier($finance);

        $this->actingAs($chef)->get(route('courriers-depart.show', $courrier))->assertForbidden();
        $this->actingAs($chef)->get(route('courriers-depart.attachments.show', $courrier))->assertForbidden();
    }

    public function test_crud_status_input_cannot_override_the_server_controlled_initial_status(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = $this->service('URB', 'Urbanisme');

        $this->actingAs($admin)->post(route('courriers-depart.store'), array_merge($this->data($service), ['statut' => 'envoye']))->assertRedirect();

        $this->assertDatabaseHas('outgoing_courriers', ['statut' => 'brouillon', 'created_by' => $admin->id]);
    }

    private function userWithRole(string $slug, ?Service $service = null): User
    {
        $role = Role::create(['name' => $slug, 'slug' => $slug]);

        return User::factory()->create(['role_id' => $role->id, 'service_id' => $service?->id]);
    }

    private function service(string $code, string $name): Service
    {
        return Service::create(['code' => $code, 'name' => $name]);
    }

    private function courrier(Service $service): OutgoingCourrier
    {
        return OutgoingCourrier::create(array_merge($this->data($service), ['created_by' => User::factory()->create()->id, 'statut' => 'brouillon']));
    }

    private function data(Service $service): array
    {
        return ['objet' => 'Objet depart', 'destinataire' => 'Destinataire', 'organisme_destinataire' => 'Organisme', 'date_envoi' => '2026-08-12', 'mode_envoi' => 'Ordinaire', 'priorite' => 'normale', 'service_id' => $service->id];
    }
}
