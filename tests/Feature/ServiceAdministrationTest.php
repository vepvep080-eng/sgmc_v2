<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceAdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_and_create_a_service(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);

        $this->actingAs($admin)->get(route('services.index'))->assertOk();
        $this->actingAs($admin)->post(route('services.store'), [
            'name' => 'Urbanisme', 'code' => 'URB', 'is_active' => true,
        ])->assertRedirect(route('services.index'));

        $this->assertDatabaseHas('services', ['name' => 'Urbanisme', 'code' => 'URB']);
    }

    public function test_non_admin_cannot_mutate_services_and_validation_is_enforced(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $chef = $this->userWithRole(Role::CHEF_SERVICE);
        $service = Service::create(['name' => 'Finances', 'code' => 'FIN']);

        $this->actingAs($chef)->post(route('services.store'), ['name' => 'X', 'code' => 'X'])->assertForbidden();
        $this->actingAs($chef)->put(route('services.update', $service), ['name' => 'X', 'code' => 'X'])->assertForbidden();
        $this->actingAs($chef)->delete(route('services.destroy', $service))->assertForbidden();
        $this->actingAs($admin)->from(route('services.create'))->post(route('services.store'), [])
            ->assertRedirect(route('services.create'))
            ->assertSessionHasErrors(['name', 'code']);
    }

    public function test_linked_service_cannot_be_deleted(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = Service::create(['name' => 'Technique', 'code' => 'TEC']);
        User::factory()->create(['service_id' => $service->id]);

        $this->actingAs($admin)->delete(route('services.destroy', $service))->assertRedirect(route('services.index'));

        $this->assertDatabaseHas('services', ['id' => $service->id]);
    }

    private function userWithRole(string $slug): User
    {
        $role = Role::firstOrCreate(['slug' => $slug], ['name' => $slug]);

        return User::factory()->create(['role_id' => $role->id]);
    }
}
