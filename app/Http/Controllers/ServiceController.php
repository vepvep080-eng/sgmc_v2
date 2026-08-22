<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Service::class, 'service');
    }

    public function index(Request $request): View
    {
        $services = Service::query()
            ->with(['department', 'manager'])->withCount(['users', 'courriers'])
            ->when(
                $request->filled('q'),
                fn ($query) => $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->query('q').'%')
                        ->orWhere('code', 'like', '%'.$request->query('q').'%');
                })
            )
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('services.index', [
            'services' => $services,
            'filters' => $request->only(['q']),
        ]);
    }

    public function create(): View
    {
        return view('services.create', $this->formOptions());
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $service = Service::create($request->validated());

        return redirect()
            ->route('services.index')
            ->with('success', "Le service {$service->name} a été créé avec succès.");
    }

    public function edit(Service $service): View
    {
        return view('services.edit', ['service' => $service] + $this->formOptions());
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()
            ->route('services.index')
            ->with('success', "Le service {$service->name} a été mis à jour.");
    }

    public function destroy(Service $service): RedirectResponse
    {
        if ($service->users()->exists() || $service->courriers()->exists()) {
            return redirect()
                ->route('services.index')
                ->with('error', "Impossible de supprimer {$service->name} : des utilisateurs ou des courriers y sont encore rattachés.");
        }

        $name = $service->name;
        $service->delete();

        return redirect()
            ->route('services.index')
            ->with('success', "Le service {$name} a été supprimé.");
    }

    private function formOptions(): array
    {
        return [
            'departments' => Department::active()->orderBy('name')->get(),
            'managers' => User::where('is_active', true)->orderBy('name')->get(),
        ];
    }
}
