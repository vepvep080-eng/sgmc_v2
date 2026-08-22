<?php

namespace App\Http\Controllers;

use App\Exports\CourriersExcelExport;
use App\Http\Requests\FilterCourriersRequest;
use App\Services\AuditService;
use App\Services\CourrierQueryService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourrierExcelExportController extends Controller
{
    public function index(FilterCourriersRequest $request, CourrierQueryService $courrierQuery, AuditService $auditService): BinaryFileResponse
    {
        $filters = $request->validated();
        $courriers = $courrierQuery->export($request->user(), $filters);

        $response = Excel::download(
            new CourriersExcelExport($courriers),
            'courriers_'.now()->format('Y_m_d').'.xlsx'
        );

        $auditService->record(
            'exported_excel',
            null,
            null,
            'Export Excel de la liste des courriers.',
            null,
            ['filters' => $filters, 'result_count' => $courriers->count()],
        );

        return $response;
    }
}
