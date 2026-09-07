<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterCourriersRequest;
use App\Http\Requests\StoreCourrierRequest;
use App\Http\Requests\UpdateCourrierRequest;
use App\Models\Courrier;
use App\Models\Routing;
use App\Models\Service;
use App\Models\Department;
use App\Models\User;
use App\Services\CourrierQueryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\QueryException;
use Illuminate\View\View;

class CourrierController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Courrier::class, 'courrier');
    }

    public function index(FilterCourriersRequest $request, CourrierQueryService $courrierQuery): View
    {
        $filters = $request->validated();

        return view('courriers.index', [
            'courriers' => $courrierQuery->paginate($request->user(), $filters),
            'sort' => $courrierQuery->sort($filters),
            'direction' => $courrierQuery->direction($filters),
            'filters' => $courrierQuery->filters($filters),
            'types' => $courrierQuery->typesFor($request->user()),
            'services' => $courrierQuery->servicesFor($request->user()),
        ]);
    }

    public function create(): View
    {
        return view('courriers.create', [
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function quickCreate(): View
    {
        $this->authorize('create', Courrier::class);

        return view('courriers.quick-create', [
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreCourrierRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('piece_jointe');
        $data['user_id'] = $request->user()->id;
        $data['statut'] = 'nouveau';

        if ($request->hasFile('piece_jointe')) {
            $data['piece_jointe'] = $request->file('piece_jointe')->store('courriers', 'public');
        }

        for ($attempt = 0; $attempt < 3; $attempt++) {
            try {
                $courrier = DB::transaction(function () use ($data, $request) {
                    $data['numero'] = Courrier::generateNumero();
                    $courrier = Courrier::create($data);

                    // First move: straight from the Bureau d'Ordre (no source
                    // service) to whichever service was chosen on the form.
                    Routing::create([
                        'courrier_id' => $courrier->id,
                        'service_source_id' => null,
                        'service_destination_id' => $courrier->service_id,
                        'routed_by' => $request->user()->id,
                        'statut' => Routing::STATUT_EN_ATTENTE,
                        'sent_at' => now(),
                    ]);

                    return $courrier;
                });

                break;
            } catch (QueryException $exception) {
                if ($attempt === 2 || ! str_contains($exception->getMessage(), 'courriers.numero')) {
                    throw $exception;
                }
            }
        }

        return redirect()
            ->route('courriers.show', $courrier)
            ->with('success', "Le courrier {$courrier->numero} a été créé avec succès.");
    }

    public function show(Courrier $courrier): View
    {
        return view('courriers.show', [
            'courrier' => $courrier->load([
                'service',
                'creator',
                'treatedBy',
                'archivedBy',
                'currentRouting.serviceDestination',
                'routings.serviceSource',
                'routings.serviceDestination',
                'routings.routedBy',
                'auditLogs.user',
                'auditLogs.service',
            ]),
            'departments' => Department::active()->with(['services' => fn ($query) => $query->where('is_active', true)->orderBy('name')])->orderBy('name')->get(),
            'recipients' => User::where('is_active', true)->with('service')->orderBy('name')->get(),
        ]);
    }

    public function edit(Courrier $courrier): View
    {
        return view('courriers.edit', [
            'courrier' => $courrier,
            'services' => Service::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateCourrierRequest $request, Courrier $courrier): RedirectResponse
    {
        $data = $request->safe()->except(['piece_jointe', 'remove_piece_jointe']);

        if ($request->boolean('remove_piece_jointe') && $courrier->piece_jointe) {
            Storage::disk('public')->delete($courrier->piece_jointe);
            $data['piece_jointe'] = null;
        }

        if ($request->hasFile('piece_jointe')) {
            if ($courrier->piece_jointe) {
                Storage::disk('public')->delete($courrier->piece_jointe);
            }

            $data['piece_jointe'] = $request->file('piece_jointe')->store('courriers', 'public');
        }

        $courrier->update($data);

        return redirect()
            ->route('courriers.show', $courrier)
            ->with('success', "Le courrier {$courrier->numero} a été mis à jour.");
    }

    public function destroy(Courrier $courrier): RedirectResponse
    {
        $numero = $courrier->numero;

        try {
            $courrier->delete();
        } catch (QueryException $exception) {
            if (str_contains($exception->getMessage(), 'FOREIGN KEY constraint failed')
                || str_contains($exception->getMessage(), 'foreign key constraint fails')) {
                return redirect()
                    ->route('courriers.show', $courrier)
                    ->with('error', "Impossible de supprimer le courrier {$numero} : il possède un historique de numérisation (scan). Ce courrier doit être archivé plutôt que supprimé.");
            }

            throw $exception;
        }

        if ($courrier->piece_jointe) {
            Storage::disk('public')->delete($courrier->piece_jointe);
        }

        return redirect()
            ->route('courriers.index')
            ->with('success', "Le courrier {$numero} a été supprimé.");
    }

    /**
     * Archive a courrier that has already been processed.
     */
    public function archive(Request $request, Courrier $courrier): RedirectResponse
    {
        $this->authorize('archive', $courrier);

        DB::transaction(function () use ($request, $courrier) {
            $courrier->update([
                'statut' => 'archive',
                'archived_by' => $request->user()->id,
                'archived_at' => now(),
            ]);
        });

        return redirect()
            ->route('courriers.show', $courrier)
            ->with('success', "Le courrier {$courrier->numero} a été archivé.");
    }
}
