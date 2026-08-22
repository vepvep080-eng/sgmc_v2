<?php

namespace Tests\Feature;

use App\Models\Courrier;
use App\Models\OutgoingCourrier;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorrespondenceNumberGenerationTest extends TestCase
{
    use RefreshDatabase;

    public function test_incoming_numbers_keep_the_format_and_continue_after_the_highest_existing_number(): void
    {
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $user = User::factory()->create();
        $year = now()->format('Y');
        $existing = Courrier::create(array_merge($this->incomingData($service, $user), ['numero' => "COUR-{$year}-0007"]));
        $created = Courrier::create($this->incomingData($service, $user));

        $this->assertSame("COUR-{$year}-0007", $existing->fresh()->numero);
        $this->assertSame("COUR-{$year}-0008", $created->numero);
    }

    public function test_outgoing_numbers_keep_the_format_and_continue_after_the_highest_existing_number(): void
    {
        $service = Service::create(['name' => 'Urbanisme', 'code' => 'URB']);
        $user = User::factory()->create();
        $year = now()->format('Y');
        $existing = OutgoingCourrier::create(array_merge($this->outgoingData($service, $user), ['numero' => "DEP-{$year}-0004"]));
        $created = OutgoingCourrier::create($this->outgoingData($service, $user));

        $this->assertSame("DEP-{$year}-0004", $existing->fresh()->numero);
        $this->assertSame("DEP-{$year}-0005", $created->numero);
    }

    private function incomingData(Service $service, User $user): array
    {
        return ['objet' => 'Objet', 'expediteur' => 'Expéditeur', 'date_reception' => now()->toDateString(), 'priorite' => 'normale', 'type' => 'Administratif', 'service_id' => $service->id, 'user_id' => $user->id, 'statut' => 'nouveau'];
    }

    private function outgoingData(Service $service, User $user): array
    {
        return ['objet' => 'Objet départ', 'destinataire' => 'Destinataire', 'organisme_destinataire' => 'Organisme', 'date_envoi' => now()->toDateString(), 'mode_envoi' => 'Ordinaire', 'priorite' => 'normale', 'service_id' => $service->id, 'created_by' => $user->id, 'statut' => 'brouillon'];
    }
}
