<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterOutgoingCourriersRequest;
use App\Http\Requests\ArchiveOutgoingCourrierRequest;
use App\Http\Requests\PrepareOutgoingCourrierRequest;
use App\Http\Requests\SendOutgoingCourrierRequest;
use App\Http\Requests\StoreOutgoingCourrierRequest;
use App\Http\Requests\UpdateOutgoingCourrierRequest;
use App\Models\OutgoingCourrier;
use App\Models\Service;
use App\Services\OutgoingCourrierQueryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OutgoingCourrierController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(OutgoingCourrier::class, 'outgoing_courrier');
    }

    public function index(FilterOutgoingCourriersRequest $request, OutgoingCourrierQueryService $outgoingCourrierQuery): View
    {
        $filters = $request->validated();

        return view('outgoing-courriers.index', [
            'courriers' => $outgoingCourrierQuery->paginate($request->user(), $filters),
            'filters' => $outgoingCourrierQuery->filters($filters),
            'sort' => $outgoingCourrierQuery->sort($filters),
            'direction' => $outgoingCourrierQuery->direction($filters),
            'services' => $outgoingCourrierQuery->servicesFor($request->user()),
        ]);
    }

    public function create(): View
    {
        return view('outgoing-courriers.create', [
            'services' => Service::active()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreOutgoingCourrierRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('attachment');
        $data['created_by'] = $request->user()->id;
        $data['statut'] = 'brouillon';

        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('outgoing-courriers', 'public');
        }

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $courrier = DB::transaction(function () use ($data) {
                    $data['numero'] = OutgoingCourrier::generateNumero();

                    return OutgoingCourrier::create($data);
                });

                break;
            } catch (QueryException $exception) {
                if ($attempt === 2 || ! str_contains($exception->getMessage(), 'outgoing_courriers.numero')) {
                    throw $exception;
                }
            }
        }

        return redirect()
            ->route('courriers-depart.show', $courrier)
            ->with('success', "Le courrier départ {$courrier->numero} a été créé avec succès.");
    }

    public function show(OutgoingCourrier $outgoingCourrier): View
    {
        return view('outgoing-courriers.show', [
            'courrier' => $outgoingCourrier->load(['service', 'creator', 'updater', 'auditLogs.user', 'auditLogs.service']),
        ]);
    }

    public function edit(OutgoingCourrier $outgoingCourrier): View
    {
        return view('outgoing-courriers.edit', [
            'courrier' => $outgoingCourrier,
            'services' => Service::active()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateOutgoingCourrierRequest $request, OutgoingCourrier $outgoingCourrier): RedirectResponse
    {
        $data = $request->safe()->except('attachment');
        $data['updated_by'] = $request->user()->id;

        if ($request->hasFile('attachment')) {
            if ($outgoingCourrier->attachment) {
                Storage::disk('public')->delete($outgoingCourrier->attachment);
            }

            $data['attachment'] = $request->file('attachment')->store('outgoing-courriers', 'public');
        }

        $outgoingCourrier->update($data);

        return redirect()
            ->route('courriers-depart.show', $outgoingCourrier)
            ->with('success', "Le courrier départ {$outgoingCourrier->numero} a été mis à jour.");
    }

    public function destroy(OutgoingCourrier $outgoingCourrier): RedirectResponse
    {
        if ($outgoingCourrier->attachment) {
            Storage::disk('public')->delete($outgoingCourrier->attachment);
        }

        $numero = $outgoingCourrier->numero;
        $outgoingCourrier->delete();

        return redirect()
            ->route('courriers-depart.index')
            ->with('success', "Le courrier départ {$numero} a été supprimé.");
    }

    public function prepare(PrepareOutgoingCourrierRequest $request, OutgoingCourrier $outgoingCourrier): RedirectResponse
    {
        return $this->transition($outgoingCourrier, 'pret', 'Le courrier départ est prêt à être envoyé.');
    }

    public function send(SendOutgoingCourrierRequest $request, OutgoingCourrier $outgoingCourrier): RedirectResponse
    {
        return $this->transition($outgoingCourrier, 'envoye', 'Le courrier départ a été envoyé.');
    }

    public function archive(ArchiveOutgoingCourrierRequest $request, OutgoingCourrier $outgoingCourrier): RedirectResponse
    {
        return $this->transition($outgoingCourrier, 'archive', 'Le courrier départ a été archivé.');
    }

    private function transition(OutgoingCourrier $courrier, string $statut, string $message): RedirectResponse
    {
        $courrier->update([
            'statut' => $statut,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('courriers-depart.show', $courrier)->with('success', $message);
    }
}
