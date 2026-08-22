<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function index(): View { $this->authorize('viewAny', Section::class); return view('sections.index', ['sections' => Section::with(['service','manager'])->withCount('users')->paginate(15)]); }
    public function create(): View { $this->authorize('create', Section::class); return view('sections.create', $this->options()); }
    public function store(Request $request): RedirectResponse { $this->authorize('create', Section::class); $data = $this->validated($request); Section::create($data); return redirect()->route('sections.index'); }
    public function edit(Section $section): View { $this->authorize('update', $section); return view('sections.edit', ['section' => $section] + $this->options()); }
    public function update(Request $request, Section $section): RedirectResponse { $this->authorize('update', $section); $section->update($this->validated($request, $section)); return redirect()->route('sections.index'); }
    public function destroy(Section $section): RedirectResponse { $this->authorize('delete', $section); if ($section->users()->exists()) return back()->with('error', __('app.section_has_users')); $section->delete(); return redirect()->route('sections.index'); }
    private function options(): array { return ['services' => Service::active()->orderBy('name')->get(), 'managers' => User::where('is_active', true)->orderBy('name')->get()]; }
    private function validated(Request $request, ?Section $section = null): array { return $request->validate(['service_id' => ['required','exists:services,id'], 'name' => ['required','string','max:255'], 'code' => ['required','string','max:20','unique:sections,code'.($section ? ','.$section->id : '')], 'manager_id' => ['nullable','exists:users,id'], 'description' => ['nullable','string','max:1000'], 'is_active' => ['boolean']]); }
}
