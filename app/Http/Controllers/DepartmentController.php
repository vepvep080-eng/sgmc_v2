<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct() { $this->authorizeResource(Department::class, 'department'); }

    public function index(Request $request): View
    {
        $departments = Department::with('manager')->withCount('services')
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->query('q').'%'))
            ->orderBy('name')->paginate(15)->withQueryString();

        return view('departments.index', compact('departments'));
    }

    public function create(): View { return view('departments.create', $this->formOptions()); }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        Department::create($request->validated());
        return redirect()->route('departments.index')->with('success', __('app.department_created'));
    }

    public function edit(Department $department): View { return view('departments.edit', ['department' => $department] + $this->formOptions()); }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());
        return redirect()->route('departments.index')->with('success', __('app.department_updated'));
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->services()->exists()) {
            return redirect()->route('departments.index')->with('error', __('app.department_has_services'));
        }
        $department->delete();
        return redirect()->route('departments.index')->with('success', __('app.department_deleted'));
    }

    private function formOptions(): array { return ['managers' => User::where('is_active', true)->orderBy('name')->get()]; }
}
