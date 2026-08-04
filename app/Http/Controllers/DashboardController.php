<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetricsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, DashboardMetricsService $dashboardMetrics): View
    {
        return view('dashboard', $dashboardMetrics->for($request->user()));
    }
}
