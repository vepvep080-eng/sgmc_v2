<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterCourriersRequest;
use App\Services\AuditService;
use App\Services\CourrierPdfExportService;
use Symfony\Component\HttpFoundation\Response;

class CourrierPdfExportController extends Controller
{
    public function index(FilterCourriersRequest $request, CourrierPdfExportService $pdfExport, AuditService $auditService): Response
    {
        $filters = $request->validated();
        $response = $pdfExport->download($request->user(), $filters);

        $auditService->record(
            'exported_pdf',
            null,
            null,
            'Export PDF de la liste des courriers.',
            null,
            ['filters' => $filters],
        );

        return $response;
    }
}
