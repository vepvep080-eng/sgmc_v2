<?php

namespace App\Http\Controllers;

use App\Exports\OutgoingCourriersExcelExport;
use App\Http\Requests\FilterOutgoingCourriersRequest;
use App\Services\ArabicPdfTextService;
use App\Services\AuditService;
use App\Services\OutgoingCourrierQueryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\Response;

class OutgoingCourrierExportController extends Controller
{
    public function pdf(FilterOutgoingCourriersRequest $request, OutgoingCourrierQueryService $query, AuditService $audit, ArabicPdfTextService $arabic): Response
    {
        $filters = $request->validated(); $courriers = $query->export($request->user(), $filters);
        $audit->record('exported_outgoing_pdf', null, null, 'Export PDF des courriers depart.', null, ['filters' => $filters, 'result_count' => $courriers->count()]);
        return Pdf::loadView('outgoing-courriers.pdf.index', compact('courriers', 'filters') + ['generatedAt' => now(), 'arabic' => $arabic])->setPaper('a4', 'landscape')->download('courriers-depart-'.now()->format('Y-m-d').'.pdf');
    }
    public function excel(FilterOutgoingCourriersRequest $request, OutgoingCourrierQueryService $query, AuditService $audit)
    {
        $filters = $request->validated(); $courriers = $query->export($request->user(), $filters);
        $audit->record('exported_outgoing_excel', null, null, 'Export Excel des courriers depart.', null, ['filters' => $filters, 'result_count' => $courriers->count()]);
        return Excel::download(new OutgoingCourriersExcelExport($courriers), 'courriers-depart-'.now()->format('Y-m-d').'.xlsx');
    }
    public function print(FilterOutgoingCourriersRequest $request, OutgoingCourrierQueryService $query, AuditService $audit)
    {
        $filters = $request->validated(); $courriers = $query->export($request->user(), $filters);
        $audit->record('printed_outgoing_list', null, null, 'Impression des courriers depart.', null, ['filters' => $filters, 'result_count' => $courriers->count()]);
        return view('outgoing-courriers.print.index', compact('courriers', 'filters') + ['generatedAt' => now()]);
    }
}
