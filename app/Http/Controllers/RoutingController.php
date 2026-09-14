<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessCourrierRequest;
use App\Http\Requests\RejectRoutingRequest;
use App\Http\Requests\StoreRoutingRequest;
use App\Models\Courrier;
use App\Models\Department;
use App\Models\Role;
use App\Models\Routing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class RoutingController extends Controller
{
    /**
     * Routage hiérarchique multi-destinataires :
     * - Admin/BO : 1..N départements (routings.department_id, service NULL).
     * - Chef département : 1..N services de SON département.
     * Chaque envoi = 1 ligne routing (traçabilité qui -> qui -> étape).
     */
    public function store(StoreRoutingRequest $request, Courrier $courrier): RedirectResponse
    {
        $user = $request->user();
        $commentaire = $request->validated('commentaire');

        // Double garde-fou serveur (même si le select est manipulé côté client)
        // en plus de StoreRoutingRequest. NB : on appelle la Policy DIRECTEMENT
        // (pas via Gate/$this->authorize) car l'ability prend des arguments
        // supplémentaires et Gate résoudrait la policy sur le modèle Courrier.
        $routingPolicy = app(\App\Policies\RoutingPolicy::class);

        if ($user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE)) {
            abort_unless(
                $routingPolicy->routeHierarchically($user, $courrier, $request->departmentIds(), null),
                403
            );
        } elseif ($user->hasRole(Role::CHEF_DEPARTEMENT)) {
            abort_unless(
                $routingPolicy->routeHierarchically($user, $courrier, null, $request->serviceDestinationIds()),
                403
            );
        } else {
            abort(403);
        }

        DB::transaction(function () use ($request, $courrier, $user, $commentaire) {
            if ($user->hasRole(Role::ADMIN, Role::BUREAU_ORDRE)) {
                foreach ($request->departmentIds() as $departmentId) {
                    Routing::create([
                        'courrier_id' => $courrier->id,
                        'service_source_id' => $courrier->service_id,
                        'department_id' => $departmentId,
                        'service_destination_id' => null,
                        'section_destination_id' => null,
                        'recipient_user_id' => Department::whereKey($departmentId)->value('manager_id'),
                        'routed_by' => $user->id,
                        'statut' => Routing::STATUT_EN_ATTENTE,
                        'sent_at' => now(),
                        'commentaire' => $commentaire,
                    ]);
                }

                $courrier->update([
                    'service_id' => null,
                    'section_id' => null,
                    'assigned_user_id' => null,
                    // Traçabilité : reçu (par les départements) en attente de dispatch.
                    'statut' => 'en_attente_de_dispatch',
                ]);

                return;
            }

            // Chef de département : dispatch vers ses services.
            $managed = Department::query()->where('manager_id', $user->id)->firstOrFail();

            foreach ($request->serviceDestinationIds() as $serviceId) {
                Routing::create([
                    'courrier_id' => $courrier->id,
                    'service_source_id' => $courrier->service_id,
                    'department_id' => $managed->id,
                    'service_destination_id' => $serviceId,
                    'section_destination_id' => $request->validated('section_destination_id'),
                    'recipient_user_id' => $request->validated('recipient_user_id'),
                    'routed_by' => $user->id,
                    'statut' => Routing::STATUT_EN_ATTENTE,
                    'sent_at' => now(),
                    'commentaire' => $commentaire,
                ]);
            }

            $courrier->update([
                'service_id' => $request->serviceDestinationIds()[0],
                'section_id' => $request->validated('section_destination_id'),
                'assigned_user_id' => $request->validated('recipient_user_id'),
                // Traçabilité : transmis aux services.
                'statut' => 'nouveau',
            ]);
        });

        return redirect()
            ->route('courriers.show', $courrier)
            ->with('success', 'Le routage a été effectué avec succès.');
    }

    public function accept(Routing $routing): RedirectResponse
    {
        $this->authorize('accept', $routing);

        DB::transaction(function () use ($routing) {
            $routing->update([
                'statut' => Routing::STATUT_ACCEPTE,
                'received_at' => now(),
            ]);

            $routing->courrier->update(['statut' => 'en_cours']);
        });

        return redirect()
            ->route('courriers.show', $routing->courrier)
            ->with('success', 'Le courrier a été accepté.');
    }

    public function reject(RejectRoutingRequest $request, Routing $routing): RedirectResponse
    {
        $this->authorize('reject', $routing);

        DB::transaction(function () use ($request, $routing) {
            $routing->update([
                'statut' => Routing::STATUT_REFUSE,
                'received_at' => now(),
                'commentaire' => $request->validated('commentaire') ?? $routing->commentaire,
            ]);

            $routing->courrier->update(['statut' => 'nouveau']);
        });

        return redirect()
            ->route('courriers.show', $routing->courrier)
            ->with('success', "Le courrier a été refusé et retourné au Bureau d'Ordre.");
    }

    public function process(ProcessCourrierRequest $request, Routing $routing): RedirectResponse
    {
        $courrier = $routing->courrier;

        DB::transaction(function () use ($request, $courrier) {
            $pieceJointe = null;

            if ($request->hasFile('piece_jointe_traitement')) {
                $pieceJointe = $request->file('piece_jointe_traitement')
                    ->store('traitements', 'public');
            }

            $courrier->update([
                'traitement_commentaire' => $request->validated('commentaire'),
                'traitement_piece_jointe' => $pieceJointe,
                'traite_par' => $request->user()->id,
                'traite_le' => now(),
                'statut' => 'traite',
            ]);
        });

        return redirect()
            ->route('courriers.show', $courrier)
            ->with('success', 'Le courrier a été traité avec succès.');
    }
}
