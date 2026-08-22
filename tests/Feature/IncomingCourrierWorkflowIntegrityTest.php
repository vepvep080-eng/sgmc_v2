<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\Role;
use App\Models\Routing;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomingCourrierWorkflowIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_crud_update_cannot_force_a_workflow_status(): void
    {
        $admin = $this->userWithRole(Role::ADMIN);
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $courrier = $this->courrier($service, $admin);

        $this->actingAs($admin)->put(route('courriers.update', $courrier), array_merge($this->data($service), [
            'statut' => 'traite',
            'traitement_commentaire' => 'Contournement',
        ]))->assertRedirect(route('courriers.show', $courrier));

        $courrier->refresh();
        $this->assertSame('nouveau', $courrier->statut);
        $this->assertNull($courrier->traitement_commentaire);
    }

    public function test_authorized_routing_process_remains_the_only_way_to_mark_a_courrier_processed(): void
    {
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $admin = $this->userWithRole(Role::ADMIN);
        $chef = $this->userWithRole(Role::CHEF_SERVICE, $service);
        $courrier = $this->courrier($service, $admin);
        $routing = Routing::create([
            'courrier_id' => $courrier->id,
            'service_destination_id' => $service->id,
            'routed_by' => $admin->id,
            'statut' => Routing::STATUT_EN_ATTENTE,
            'sent_at' => now(),
        ]);

        $this->actingAs($chef)->post(route('routings.accept', $routing))->assertRedirect();
        $this->actingAs($chef)->post(route('routings.process', $routing), ['commentaire' => 'Traité conformément au circuit.'])->assertRedirect();

        $this->assertDatabaseHas('courriers', ['id' => $courrier->id, 'statut' => 'traite', 'traite_par' => $chef->id]);
    }

    private function userWithRole(string $slug, ?Service $service = null): User
    {
        $role = Role::create(['name' => $slug, 'slug' => $slug]);

        return User::factory()->create(['role_id' => $role->id, 'service_id' => $service?->id]);
    }

    private function courrier(Service $service, User $creator): Courrier
    {
        return Courrier::create(array_merge($this->data($service), ['user_id' => $creator->id, 'statut' => 'nouveau']));
    }

    private function data(Service $service): array
    {
        return ['objet' => 'Objet', 'expediteur' => 'Expéditeur', 'date_reception' => '2026-08-16', 'priorite' => 'normale', 'type' => 'Administratif', 'service_id' => $service->id];
    }
}
