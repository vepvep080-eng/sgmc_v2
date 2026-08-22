<?php

namespace App\Http\Controllers;

use App\Models\Courrier;
use App\Services\AuditService;
use App\Services\CourrierDocumentPdfExportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CourrierDocumentPdfExportController extends Controller
{
    public function show(Request $request, Courrier $courrier, CourrierDocumentPdfExportService $pdfExport, AuditService $auditService): Response
    {
        $this->authorize('view', $courrier);

        $response = $pdfExport->download($courrier);

        $auditService->record('exported_courrier_pdf', $courrier, $courrier->service_id, "Export PDF du courrier {$courrier->numero}.");

        return $response;
    }
}