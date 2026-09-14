<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrPayloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_arbitrary_qr_is_returned_as_safe_text_and_only_a_hash_is_audited(): void
    {
        $service = Service::create(['name' => 'Service', 'code' => 'QR']);
        $role = Role::firstOrCreate(['slug' => Role::ADMIN], ['name' => Role::ADMIN]);
        $user = User::factory()->create(['role_id' => $role->id, 'service_id' => $service->id]);
        $courrier = Courrier::create(['objet' => 'Objet', 'expediteur' => 'Expéditeur', 'date_reception' => now()->toDateString(), 'priorite' => 'normale', 'type' => 'Administratif', 'service_id' => $service->id, 'user_id' => $user->id, 'statut' => 'nouveau']);
        $payload = '<script>alert(1)</script>';

        $this->actingAs($user)->postJson(route('courriers.qr-payload.store', $courrier), ['payload' => $payload])->assertOk()->assertJsonPath('type', 'text')->assertJsonMissing(['payload']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'qr_read', 'auditable_id' => $courrier->id]);
    }
}
