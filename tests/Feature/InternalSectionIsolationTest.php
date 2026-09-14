<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\Section;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternalSectionIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_section_manager_and_employee_cannot_access_another_section_courrier(): void
    {
        $service = Service::create(['name' => 'مصلحة', 'code' => 'SRV']);
        $sectionA = Section::create(['service_id' => $service->id, 'name' => 'قسم أ', 'code' => 'A']);
        $sectionB = Section::create(['service_id' => $service->id, 'name' => 'قسم ب', 'code' => 'B']);
        $sectionManager = $this->user(Role::CHEF_SECTION, $service, $sectionA);
        $employee = $this->user(Role::EMPLOYEE, $service, $sectionA);
        $owner = $this->user(Role::BUREAU_ORDRE, $service);
        $courrier = Courrier::create(['objet' => 'Objet', 'expediteur' => 'Expéditeur', 'date_reception' => now()->toDateString(), 'priorite' => 'normale', 'type' => 'Administratif', 'service_id' => $service->id, 'section_id' => $sectionB->id, 'user_id' => $owner->id, 'statut' => 'nouveau']);

        $this->actingAs($sectionManager)->get(route('courriers.show', $courrier))->assertForbidden();
        $this->actingAs($employee)->get(route('courriers.show', $courrier))->assertForbidden();
    }

    private function user(string $roleSlug, Service $service, ?Section $section = null): User
    {
        $role = Role::firstOrCreate(['slug' => $roleSlug], ['name' => $roleSlug]);

        return User::factory()->create(['role_id' => $role->id, 'service_id' => $service->id, 'section_id' => $section?->id]);
    }
}
