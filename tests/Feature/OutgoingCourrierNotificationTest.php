<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\OutgoingCourrier;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OutgoingCourrierNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_management_recipients_are_notified_when_an_outgoing_courrier_is_sent(): void
    {
        $service = $this->service();
        $admin = $this->userWithRole(Role::ADMIN);
        $bureau = $this->userWithRole(Role::BUREAU_ORDRE);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $service);
        $courrier = $this->courrier($service, 'pret');

        $this->actingAs($admin)->post(route('courriers-depart.send', $courrier))->assertRedirect();

        $notification = $admin->notifications()->sole();
        $this->assertSame('sent', $notification->data['event']);
        $this->assertSame($courrier->id, $notification->data['outgoing_courrier_id']);
        $this->assertSame(route('courriers-depart.show', $courrier), $notification->data['url']);
        $this->assertCount(1, $bureau->notifications()->get());
        $this->assertCount(0, $chef->notifications()->get());
    }

    public function test_a_repeated_transition_does_not_create_a_duplicate_notification(): void
    {
        $service = $this->service();
        $admin = $this->userWithRole(Role::ADMIN);
        $courrier = $this->courrier($service, 'pret');

        $this->actingAs($admin)->post(route('courriers-depart.send', $courrier))->assertRedirect();
        $this->actingAs($admin)->post(route('courriers-depart.send', $courrier))->assertForbidden();

        $this->assertCount(1, $admin->notifications()->get());
    }

    public function test_a_non_workflow_update_does_not_create_an_outgoing_notification(): void
    {
        $service = $this->service();
        $admin = $this->userWithRole(Role::ADMIN);
        $courrier = $this->courrier($service, 'brouillon');

        $courrier->update(['objet' => 'Objet mis à jour', 'updated_by' => $admin->id]);

        $this->assertCount(0, $admin->notifications()->get());
    }

    public function test_a_user_cannot_mark_another_users_notification_as_read(): void
    {
        $service = $this->service();
        $admin = $this->userWithRole(Role::ADMIN);
        $bureau = $this->userWithRole(Role::BUREAU_ORDRE);
        $courrier = $this->courrier($service, 'pret');

        $this->actingAs($admin)->post(route('courriers-depart.send', $courrier));
        $notification = $admin->notifications()->sole();

        $this->actingAs($bureau)
            ->post(route('notifications.read', $notification))
            ->assertForbidden();

        $this->assertNull($notification->fresh()->read_at);
    }

    public function test_existing_incoming_courrier_notifications_continue_to_use_the_same_recipient_rule(): void
    {
        $service = $this->service();
        $admin = $this->userWithRole(Role::ADMIN);
        $courrier = Courrier::create([
            'objet' => 'Objet arrivée',
            'expediteur' => 'Expéditeur',
            'date_reception' => '2026-08-12',
            'priorite' => 'normale',
            'type' => 'Administratif',
            'service_id' => $service->id,
            'statut' => 'nouveau',
        ]);

        $courrier->update(['statut' => 'traite']);

        $notification = $admin->notifications()->sole();
        $this->assertSame('processed', $notification->data['event']);
        $this->assertSame($courrier->id, $notification->data['courrier_id']);
    }

    private function userWithRole(string $slug, ?Service $service = null): User
    {
        $role = Role::firstOrCreate(['slug' => $slug], ['name' => $slug]);

        return User::factory()->create(['role_id' => $role->id, 'service_id' => $service?->id]);
    }

    private function service(): Service
    {
        return Service::create(['code' => 'URB', 'name' => 'Urbanisme']);
    }

    private function courrier(Service $service, string $statut): OutgoingCourrier
    {
        return OutgoingCourrier::create([
            'objet' => 'Objet départ',
            'destinataire' => 'Destinataire',
            'organisme_destinataire' => 'Organisme',
            'date_envoi' => '2026-08-12',
            'mode_envoi' => 'Ordinaire',
            'priorite' => 'normale',
            'service_id' => $service->id,
            'created_by' => User::factory()->create()->id,
            'statut' => $statut,
        ]);
    }
}
