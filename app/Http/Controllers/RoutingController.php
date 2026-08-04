<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessCourrierRequest;
use App\Http\Requests\RejectRoutingRequest;
use App\Http\Requests\StoreRoutingRequest;
use App\Models\Courrier;
use App\Models\Routing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class RoutingController extends Controller
{
    public function store(StoreRoutingRequest $request, Courrier $courrier): RedirectResponse
    {
        DB::transaction(function () use ($request, $courrier) {
            $newServiceId = (int) $request->validated('service_destination_id');

            Routing::create([
                'courrier_id' => $courrier->id,
                'service_source_id' => $courrier->service_id,
                'service_destination_id' => $newServiceId,
                'routed_by' => $request->user()->id,
                'statut' => Routing::STATUT_EN_ATTENTE,
                'sent_at' => now(),
                'commentaire' => $request->validated('commentaire'),
            ]);

            $courrier->update([
                'service_id' => $newServiceId,
                'statut' => 'nouveau',
            ]);
        });

        return redirect()
            ->route('courriers.show', $courrier)
            ->with('success', 'Le courrier a été routé vers le service sélectionné.');
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
