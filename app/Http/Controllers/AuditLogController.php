<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterAuditLogsRequest;
use App\Models\AuditLog;
use App\Models\Service;
use App\Models\User;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(FilterAuditLogsRequest $request): View
    {
        $this->authorize('viewAny', AuditLog::class);

        $filters = $request->validated();

        $auditLogs = AuditLog::query()
            ->with(['user:id,name', 'service:id,name', 'courrier:id,numero', 'outgoingCourrier:id,numero'])
            ->when(filled($filters['user_id'] ?? null), fn ($query) => $query->where('user_id', $filters['user_id']))
            ->when(filled($filters['action'] ?? null), fn ($query) => $query->where('action', $filters['action']))
            ->when(filled($filters['service_id'] ?? null), fn ($query) => $query->where('service_id', $filters['service_id']))
            ->when(filled($filters['date_from'] ?? null), fn ($query) => $query->whereDate('created_at', '>=', $filters['date_from']))
            ->when(filled($filters['date_to'] ?? null), fn ($query) => $query->whereDate('created_at', '<=', $filters['date_to']))
            ->when(
                filled($filters['numero'] ?? null),
                fn ($query) => $query->whereHas('courrier', fn ($courrier) => $courrier->where('numero', 'like', '%'.$filters['numero'].'%'))
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('audit-logs.index', [
            'auditLogs' => $auditLogs,
            'filters' => $filters,
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'services' => Service::query()->orderBy('name')->get(['id', 'name']),
            'actions' => AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}
