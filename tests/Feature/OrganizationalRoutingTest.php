<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\Courrier;
use App\Models\Department;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationalRoutingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_departments_and_used_departments_cannot_be_deleted(): void
    {
        $admin = $this->user(Role::ADMIN);
        $this->actingAs($admin)->post(route('departments.store'), ['name' => 'قسم الشؤون الإدارية والمالية والقانونية', 'code' => 'AAF'])->assertRedirect();
        $department = Department::firstOrFail();
        Service::create(['name' => 'مصلحة المالية المحلية والصفقات', 'code' => 'FIN', 'department_id' => $department->id]);
        $this->actingAs($admin)->delete(route('departments.destroy', $department))->assertRedirect();
        $this->assertDatabaseHas('departments', ['id' => $department->id]);
    }

    public function test_routing_rejects_a_service_from_another_department_and_records_a_valid_transfer(): void
    {
        $bureau = $this->user(Role::BUREAU_ORDRE);
        $a = Department::create(['name' => 'قسم أ', 'code' => 'A']);
        $b = Department::create(['name' => 'قسم ب', 'code' => 'B']);
        $serviceA = Service::create(['name' => 'مصلحة أ', 'code' => 'SA', 'department_id' => $a->id]);
        $serviceB = Service::create(['name' => 'مصلحة ب', 'code' => 'SB', 'department_id' => $b->id]);
        // Chef du département A (via departments.manager_id) pour l'étape 2.
        $chefA = $this->user(Role::CHEF_DEPARTEMENT);
        $a->update(['manager_id' => $chefA->id]);
        $recipient = $this->user(Role::CHEF_SERVICE, $serviceA);
        $courrier = Courrier::create(['objet' => 'Objet', 'expediteur' => 'Expéditeur', 'date_reception' => now()->toDateString(), 'priorite' => 'normale', 'type' => 'Administratif', 'service_id' => $serviceA->id, 'user_id' => $bureau->id, 'statut' => 'nouveau']);

        // Étape 1 : le BO ne peut PAS viser un service directement (hiérarchie).
        $this->actingAs($bureau)->from(route('courriers.show', $courrier))->post(route('courriers.routings.store', $courrier), ['department_ids' => [$a->id], 'service_destination_ids' => [$serviceB->id]])->assertRedirect(route('courriers.show', $courrier))->assertSessionHasErrors('service_destination_ids');

        // Étape 1 valide : BO -> département A (choix multiple, service NULL).
        $this->actingAs($bureau)->post(route('courriers.routings.store', $courrier), ['department_ids' => [$a->id]])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('routings', ['courrier_id' => $courrier->id, 'department_id' => $a->id, 'service_destination_id' => null]);
        $this->assertDatabaseHas('courriers', ['id' => $courrier->id, 'statut' => 'en_attente_de_dispatch']);

        // Étape 2 invalide : le chef A ne peut pas dispatcher vers le service B (autre département).
        $this->actingAs($chefA)->from(route('courriers.show', $courrier))->post(route('courriers.routings.store', $courrier), ['service_destination_ids' => [$serviceB->id]])->assertRedirect(route('courriers.show', $courrier))->assertSessionHasErrors('service_destination_ids');

        // Étape 2 valide : chef A -> service A (SON département).
        $this->actingAs($chefA)->post(route('courriers.routings.store', $courrier), ['service_destination_ids' => [$serviceA->id], 'recipient_user_id' => $recipient->id])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('routings', ['courrier_id' => $courrier->id, 'service_destination_id' => $serviceA->id, 'recipient_user_id' => $recipient->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'routed', 'auditable_id' => $courrier->id]);
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $recipient->id]);
    }

    public function test_bo_can_send_to_both_departments_at_once(): void
    {
        $bureau = $this->user(Role::BUREAU_ORDRE);
        $a = Department::create(['name' => 'قسم أ', 'code' => 'A2']);
        $b = Department::create(['name' => 'قسم ب', 'code' => 'B2']);
        $courrier = Courrier::create(['objet' => 'Objet', 'expediteur' => 'Expéditeur', 'date_reception' => now()->toDateString(), 'priorite' => 'normale', 'type' => 'Administratif', 'service_id' => null, 'user_id' => $bureau->id, 'statut' => 'nouveau']);

        $this->actingAs($bureau)->post(route('courriers.routings.store', $courrier), ['department_ids' => [$a->id, $b->id]])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseCount('routings', 2);
        $this->assertDatabaseHas('routings', ['courrier_id' => $courrier->id, 'department_id' => $a->id]);
        $this->assertDatabaseHas('routings', ['courrier_id' => $courrier->id, 'department_id' => $b->id]);
    }

    public function test_chef_service_cannot_redirect_a_courrier(): void
    {
        $bureau = $this->user(Role::BUREAU_ORDRE);
        $a = Department::create(['name' => 'قسم أ', 'code' => 'A3']);
        $serviceA = Service::create(['name' => 'مصلحة أ', 'code' => 'SA3', 'department_id' => $a->id]);
        $chef = $this->user(Role::CHEF_SERVICE, $serviceA);
        $courrier = Courrier::create(['objet' => 'Objet', 'expediteur' => 'Expéditeur', 'date_reception' => now()->toDateString(), 'priorite' => 'normale', 'type' => 'Administratif', 'service_id' => $serviceA->id, 'user_id' => $bureau->id, 'statut' => 'nouveau']);

        // La policy 'redirect' bloque déjà le chef de service (403),
        // même sans passer par la validation du FormRequest.
        $this->actingAs($chef)->post(route('courriers.routings.store', $courrier), ['service_destination_ids' => [$serviceA->id]])->assertForbidden();
    }

    private function user(string $roleSlug, ?Service $service = null): User
    {
        $role = Role::firstOrCreate(['slug' => $roleSlug], ['name' => $roleSlug]);
        return User::factory()->create(['role_id' => $role->id, 'service_id' => $service?->id]);
    }
}
