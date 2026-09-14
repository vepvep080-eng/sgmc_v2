<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Courrier;
use App\Models\Role;
use App\Models\Routing;
use App\Models\Service;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_courrier_records_the_authenticated_actor(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);

        $this->actingAs($admin)->post(route('courriers.store'), $this->courrierData(null));

        $auditLog = AuditLog::where('action', 'created')->firstOrFail();

        $this->assertSame($admin->id, $auditLog->user_id);
        $this->assertSame(Courrier::class, $auditLog->auditable_type);
        // Hiérarchie : l'enregistrement initial ne cible aucun service.
        $this->assertNull($auditLog->service_id);
    }

    public function test_updating_a_courrier_records_old_and_new_values(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = $this->service('URB', 'Urbanisme');
        $courrier = $this->courrier($service);

        $this->actingAs($admin)->put(route('courriers.update', $courrier), $this->courrierData($service, [
            'objet' => 'Objet mis à jour',
            'statut' => 'nouveau',
        ]))->assertRedirect(route('courriers.show', $courrier));

        $auditLog = AuditLog::where('action', 'updated')->firstOrFail();

        $this->assertSame('Objet du courrier', $auditLog->old_values['objet']);
        $this->assertSame('Objet mis à jour', $auditLog->new_values['objet']);
    }

    public function test_deleting_a_courrier_preserves_its_audit_trace(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = $this->service('URB', 'Urbanisme');
        $courrier = $this->courrier($service);

        $this->actingAs($admin)
            ->delete(route('courriers.destroy', $courrier))
            ->assertRedirect(route('courriers.index'));

        $auditLog = AuditLog::where('action', 'deleted')->firstOrFail();

        $this->assertSame($admin->id, $auditLog->user_id);
        $this->assertSame(Courrier::class, $auditLog->auditable_type);
        $this->assertSame($courrier->id, $auditLog->auditable_id);
        $this->assertSame($courrier->numero, $auditLog->old_values['numero']);
    }

    public function test_routing_and_receiving_a_courrier_record_audit_logs(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $dept = \App\Models\Department::create(['name' => 'Dept', 'code' => 'DPT']);
        $urbanisme = $this->service('URB', 'Urbanisme');
        $finance = $this->service('FIN', 'Finances');
        $urbanisme->update(['department_id' => $dept->id]);
        $finance->update(['department_id' => $dept->id]);
        $dept->update(['manager_id' => $this->userWithRole(Role::CHEF_DEPARTEMENT)->id]);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $finance);
        $courrier = $this->courrier($urbanisme);

        // Étape 1 (hiérarchique) : Admin -> département.
        $this->actingAs($admin)->post(route('courriers.routings.store', $courrier), [
            'department_ids' => [$dept->id],
            'commentaire' => 'Envoi au département.',
        ])->assertRedirect(route('courriers.show', $courrier));

        // Étape 2 : le chef du département dispatche vers son service.
        $chefDept = $dept->fresh()->manager;
        $this->actingAs($chefDept)->post(route('courriers.routings.store', $courrier), [
            'service_destination_ids' => [$finance->id],
            'commentaire' => 'Transmission au service compétent.',
        ])->assertRedirect(route('courriers.show', $courrier));

        $routing = Routing::where('courrier_id', $courrier->id)->latest('id')->firstOrFail();

        $this->actingAs($chef)
            ->post(route('routings.accept', $routing))
            ->assertRedirect(route('courriers.show', $courrier));

        $this->assertDatabaseHas('audit_logs', ['action' => 'routed', 'auditable_id' => $courrier->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'received', 'auditable_id' => $courrier->id, 'user_id' => $chef->id]);
    }

    public function test_processing_and_archiving_a_courrier_record_audit_logs(): void
    {
        $service = $this->service('URB', 'Urbanisme');
        $admin = $this->userWithRole(Role::ADMIN);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $service);
        $courrier = $this->courrier($service, ['statut' => 'en_cours']);
        $routing = Routing::create([
            'courrier_id' => $courrier->id,
            'service_destination_id' => $service->id,
            'routed_by' => $admin->id,
            'statut' => Routing::STATUT_ACCEPTE,
            'sent_at' => now(),
            'received_at' => now(),
        ]);

        $this->actingAs($chef)
            ->post(route('routings.process', $routing), ['commentaire' => 'Courrier traité.'])
            ->assertRedirect(route('courriers.show', $courrier));

        $courrier->refresh();

        $this->actingAs($admin)
            ->post(route('courriers.archive', $courrier))
            ->assertRedirect(route('courriers.show', $courrier));

        $this->assertDatabaseHas('audit_logs', ['action' => 'processed', 'auditable_id' => $courrier->id, 'user_id' => $chef->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'archived', 'auditable_id' => $courrier->id, 'user_id' => $admin->id]);
    }

    public function test_rejecting_a_courrier_records_an_audit_log(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = $this->service('URB', 'Urbanisme');
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $service);
        $courrier = $this->courrier($service);
        $routing = Routing::create([
            'courrier_id' => $courrier->id,
            'service_destination_id' => $service->id,
            'routed_by' => $admin->id,
            'statut' => Routing::STATUT_EN_ATTENTE,
            'sent_at' => now(),
        ]);

        $this->actingAs($chef)
            ->post(route('routings.reject', $routing), ['commentaire' => 'Dossier incomplet.'])
            ->assertRedirect(route('courriers.show', $courrier));

        $this->assertDatabaseHas('audit_logs', ['action' => 'rejected', 'auditable_id' => $courrier->id, 'user_id' => $chef->id]);
    }

    public function test_audit_log_access_is_admin_only_and_logs_are_immutable(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $normalUser = User::factory()->create();
        $auditLog = AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'created',
            'description' => 'Création du courrier.',
        ]);

        $this->actingAs($admin)->get(route('audit-logs.index'))->assertOk();
        $this->actingAs($normalUser)->get(route('audit-logs.index'))->assertForbidden();

        $this->assertFalse($normalUser->can('update', $auditLog));
        $this->assertFalse($normalUser->can('delete', $auditLog));
    }

    public function test_audit_history_is_displayed_on_an_authorized_courrier_record(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = $this->service('URB', 'Urbanisme');
        $courrier = $this->courrier($service);
        $auditLog = AuditLog::where('action', 'created')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('courriers.show', $courrier))
            ->assertOk()
            ->assertSee('Historique')
            ->assertSee($auditLog->description)
            ->assertSee($admin->name);
    }

    public function test_export_and_print_actions_record_the_actor_and_available_context(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = $this->service('URB', 'Urbanisme');
        $courrier = $this->courrier($service, ['objet' => "Dossier d'audit"]);

        $this->actingAs($admin)->get(route('courriers.export.pdf', ['q' => 'audit']))->assertOk();
        $this->actingAs($admin)->get(route('courriers.export.excel', ['q' => 'audit']))->assertOk();
        $this->actingAs($admin)->get(route('courriers.print', ['q' => 'audit']))->assertOk();
        $this->actingAs($admin)->get(route('courriers.export.pdf.show', $courrier))->assertOk();
        $this->actingAs($admin)->get(route('courriers.print.show', $courrier))->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'exported_pdf',
            'user_id' => $admin->id,
            'new_values->filters->q' => 'audit',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'exported_excel',
            'user_id' => $admin->id,
            'new_values->filters->q' => 'audit',
            'new_values->result_count' => 1,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'printed_list',
            'user_id' => $admin->id,
            'new_values->filters->q' => 'audit',
            'new_values->result_count' => 1,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'exported_courrier_pdf',
            'user_id' => $admin->id,
            'auditable_type' => Courrier::class,
            'auditable_id' => $courrier->id,
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'printed_courrier',
            'user_id' => $admin->id,
            'auditable_type' => Courrier::class,
            'auditable_id' => $courrier->id,
        ]);
    }

    public function test_sensitive_values_are_removed_before_an_audit_log_is_stored(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = $this->service('URB', 'Urbanisme');
        $courrier = $this->courrier($service);

        $this->actingAs($admin);

        $auditLog = app(AuditService::class)->record(
            'updated',
            $courrier,
            $service->id,
            'Test de données sensibles.',
            ['password' => 'secret', 'remember_token' => 'token', 'objet' => 'Ancien objet'],
            ['password' => 'new-secret', 'api_token' => 'token', 'objet' => 'Nouvel objet'],
        );

        $this->assertArrayNotHasKey('password', $auditLog->old_values);
        $this->assertArrayNotHasKey('remember_token', $auditLog->old_values);
        $this->assertArrayNotHasKey('password', $auditLog->new_values);
        $this->assertArrayNotHasKey('api_token', $auditLog->new_values);
        $this->assertSame('Nouvel objet', $auditLog->new_values['objet']);
    }

    public function test_sensitive_values_are_removed_recursively_before_an_audit_log_is_stored(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);

        $this->actingAs($admin);

        $auditLog = app(AuditService::class)->record(
            'security_test',
            null,
            null,
            'Nested sensitive data test.',
            null,
            ['filters' => ['token' => 'secret', 'q' => 'courrier']],
        );

        $this->assertArrayNotHasKey('token', $auditLog->new_values['filters']);
        $this->assertSame('courrier', $auditLog->new_values['filters']['q']);
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
        return Courrier::create(array_merge($this->courrierData($service), [
            'statut' => 'nouveau',
        ], $attributes));
    }

    private function courrierData(?Service $service, array $attributes = []): array
    {
        return array_merge([
            'objet' => 'Objet du courrier',
            'expediteur' => 'Expediteur',
            'date_reception' => '2026-08-05',
            'reference' => 'REF-001',
            'priorite' => 'normale',
            'type' => 'Administratif',
            'service_id' => $service?->id,
            'description' => 'Description administrative.',
        ], $attributes);
    }
}
