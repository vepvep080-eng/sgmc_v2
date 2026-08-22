<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    public function index(): View
    {
        return view('organization.index', ['departments' => Department::active()->with(['manager', 'services' => fn ($query) => $query->where('is_active', true)->with('manager')->orderBy('name')])->orderBy('name')->get()]);
    }
}
