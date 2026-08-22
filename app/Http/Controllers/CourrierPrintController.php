<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterCourriersRequest;
use App\Models\Courrier;
use App\Services\AuditService;
use App\Services\CourrierQueryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourrierPrintController extends Controller
{
    public function index(FilterCourriersRequest $request, CourrierQueryService $courrierQuery, AuditService $auditService): View
    {
        $filters = $request->validated();
        $courriers = $courrierQuery->export($request->user(), $filters);

        $view = view('courriers.print.index', [
            'courriers' => $courriers,
            'generatedAt' => now(),
        ]);

        $auditService->record(
            'printed_list',
            null,
            null,
            'Impression de la liste des courriers.',
            null,
            ['filters' => $filters, 'result_count' => $courriers->count()],
        );

        return $view;
    }

    public function show(Request $request, Courrier $courrier, AuditService $auditService): View
    {
        $this->authorize('view', $courrier);

        $view = view('courriers.print.show', [
            'courrier' => $courrier->loadMissing(['service', 'creator', 'treatedBy', 'archivedBy']),
            'generatedAt' => now(),
        ]);

        $auditService->record('printed_courrier', $courrier, $courrier->service_id, "Impression du courrier {$courrier->numero}.");

        return $view;
    }
}
