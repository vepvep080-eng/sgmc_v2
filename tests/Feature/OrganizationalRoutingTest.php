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
        $recipient = $this->user(Role::CHEF_SERVICE, $serviceA);
        $courrier = Courrier::create(['objet' => 'Objet', 'expediteur' => 'Expéditeur', 'date_reception' => now()->toDateString(), 'priorite' => 'normale', 'type' => 'Administratif', 'service_id' => $serviceA->id, 'user_id' => $bureau->id, 'statut' => 'nouveau']);

        $this->actingAs($bureau)->from(route('courriers.show', $courrier))->post(route('courriers.routings.store', $courrier), ['department_id' => $a->id, 'service_destination_id' => $serviceB->id])->assertRedirect(route('courriers.show', $courrier))->assertSessionHasErrors('service_destination_id');
        $this->actingAs($bureau)->post(route('courriers.routings.store', $courrier), ['department_id' => $a->id, 'service_destination_id' => $serviceA->id, 'recipient_user_id' => $recipient->id])->assertRedirect();

        $this->assertDatabaseHas('routings', ['courrier_id' => $courrier->id, 'service_destination_id' => $serviceA->id, 'recipient_user_id' => $recipient->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'routed', 'auditable_id' => $courrier->id]);
        $this->assertDatabaseHas('notifications', ['notifiable_id' => $recipient->id]);
    }

    private function user(string $roleSlug, ?Service $service = null): User
    {
        $role = Role::create(['name' => $roleSlug, 'slug' => $roleSlug]);
        return User::factory()->create(['role_id' => $role->id, 'service_id' => $service?->id]);
    }
}
