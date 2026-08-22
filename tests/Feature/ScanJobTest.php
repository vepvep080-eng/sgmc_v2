<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\ScanJob;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ScanJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_and_an_unauthorized_user_cannot_create_a_scan_job(): void
    {
        [$admin, $courrier] = $this->courrierFor(Role::ADMIN);
        $this->post(route('courriers.scan-jobs.store', $courrier))->assertRedirect(route('login'));
        [$chef] = $this->courrierFor(Role::CHEF_SERVICE);
        $this->actingAs($chef)->post(route('courriers.scan-jobs.store', $courrier))->assertForbidden();
    }

    public function test_authorized_user_creates_a_hashed_one_time_job_and_attaches_a_pdf(): void
    {
        Storage::fake('local'); Storage::fake('public');
        [$admin, $courrier] = $this->courrierFor(Role::ADMIN);
        $response = $this->actingAs($admin)->postJson(route('courriers.scan-jobs.store', $courrier));
        $response->assertCreated()->assertJsonPath('job.status', 'pending');
        $token = $response->json('token'); $job = ScanJob::firstOrFail();
        $this->assertNotSame($token, $job->getRawOriginal('token_hash'));

        $this->actingAs($admin)->post(route('scan-jobs.upload', $job), ['token' => $token, 'document' => UploadedFile::fake()->create('scan.pdf', 100, 'application/pdf')])->assertOk();
        $this->assertSame(ScanJob::COMPLETED, $job->fresh()->status);
        $this->assertNotNull($courrier->fresh()->piece_jointe);
        $this->assertDatabaseHas('audit_logs', ['action' => 'scan_attached', 'auditable_id' => $courrier->id]);
        $this->actingAs($admin)->post(route('scan-jobs.upload', $job), ['token' => $token, 'document' => UploadedFile::fake()->create('again.pdf', 100, 'application/pdf')])->assertStatus(422);
    }

    public function test_expired_or_wrong_token_is_rejected_and_no_attachment_is_created(): void
    {
        [$admin, $courrier] = $this->courrierFor(Role::ADMIN);
        $job = ScanJob::create(['public_id' => (string) \Illuminate\Support\Str::uuid(), 'user_id' => $admin->id, 'courrier_id' => $courrier->id, 'token_hash' => \Illuminate\Support\Facades\Hash::make(str_repeat('a', 64)), 'expires_at' => now()->subMinute()]);
        $this->actingAs($admin)->post(route('scan-jobs.upload', $job), ['token' => str_repeat('a', 64), 'document' => UploadedFile::fake()->create('scan.pdf', 100, 'application/pdf')])->assertStatus(410);
        $this->assertNull($courrier->fresh()->piece_jointe);
    }

    private function courrierFor(string $roleSlug): array
    {
        $service = Service::create(['name' => 'Service', 'code' => uniqid('S')]);
        $role = Role::create(['name' => $roleSlug, 'slug' => $roleSlug]);
        $user = User::factory()->create(['role_id' => $role->id, 'service_id' => $service->id]);
        $courrier = Courrier::create(['objet' => 'Objet', 'expediteur' => 'Expéditeur', 'date_reception' => now()->toDateString(), 'priorite' => 'normale', 'type' => 'Administratif', 'service_id' => $service->id, 'user_id' => $user->id, 'statut' => 'nouveau']);
        return [$user, $courrier];
    }
}
