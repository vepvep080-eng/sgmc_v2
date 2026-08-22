<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterOutgoingCourriersRequest;
use App\Services\OutgoingCourrierQueryService;

class OutgoingCourrierReportController extends Controller
{
    public function index(FilterOutgoingCourriersRequest $request, OutgoingCourrierQueryService $query)
    {
        $filters = $request->validated();
        return view('outgoing-courriers.report', ['report' => $query->report($request->user(), $filters), 'filters' => $filters]);
    }
}
