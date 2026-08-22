@extends('layouts.app')

@section('title', "Journal d'audit — Bureau d'Ordre")
@section('page-title', "Journal d'audit")

@section('content')
    <div class="opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <h2 class="font-display text-2xl font-semibold text-primary-800 dark:text-primary-50">{{ __('app.audit_log') }}</h2>
        <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">Traçabilité des actions réalisées dans l'application.</p>
    </div>

    <section class="mt-6 rounded-[24px] border border-white/70 bg-white/55 p-5 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] dark:shadow-glass-lg sm:p-6">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
            <select name="user_id" class="rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-800 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">
                <option value="">Tous les utilisateurs</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(($filters['user_id'] ?? null) == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>

            <select name="action" class="rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-800 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">
                <option value="">Toutes les actions</option>
                @foreach ($actions as $action)
                    <option value="{{ $action }}" @selected(($filters['action'] ?? null) === $action)>{{ $action }}</option>
                @endforeach
            </select>

            <select name="service_id" class="rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-800 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">
                <option value="">Tous les services</option>
                @foreach ($services as $service)
                    <option value="{{ $service->id }}" @selected(($filters['service_id'] ?? null) == $service->id)>{{ $service->name }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-800 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-800 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">
            <input type="text" name="numero" value="{{ $filters['numero'] ?? '' }}" placeholder="Numéro du courrier" class="rounded-xl border border-white/70 bg-white/60 px-4 py-3 text-sm text-primary-800 placeholder:text-primary-500/40 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">

            <div class="flex gap-3">
                <button type="submit" class="rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 px-5 py-3 text-sm font-semibold text-white">{{ __('app.search') }}</button>
                <a href="{{ route('audit-logs.index') }}" class="rounded-xl px-5 py-3 text-sm font-semibold text-primary-700/70 hover:bg-primary-500/8 dark:text-primary-100/65 dark:hover:bg-white/5">Réinitialiser</a>
            </div>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-[24px] border border-white/70 bg-white/55 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] dark:shadow-glass-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-white/50 text-xs uppercase tracking-wide text-primary-700/55 dark:border-white/10 dark:text-primary-100/45">
                    <tr>
                        <th class="px-5 py-4">Date</th>
                        <th class="px-5 py-4">Utilisateur</th>
                        <th class="px-5 py-4">Action</th>
                        <th class="px-5 py-4">Courrier</th>
                        <th class="px-5 py-4">Service</th>
                        <th class="px-5 py-4">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 dark:divide-white/5">
                    @forelse ($auditLogs as $auditLog)
                        <tr class="text-primary-700/75 dark:text-primary-100/70">
                            <td class="whitespace-nowrap px-5 py-4">{{ $auditLog->created_at->locale('fr')->translatedFormat('d/m/Y H:i') }}</td>
                            <td class="px-5 py-4">{{ $auditLog->user?->name ?? 'Système' }}</td>
                            <td class="px-5 py-4 font-medium">{{ $auditLog->action }}</td>
                            <td class="px-5 py-4">{{ $auditLog->auditable_type === \App\Models\OutgoingCourrier::class ? $auditLog->outgoingCourrier?->numero : $auditLog->courrier?->numero ?? '—' }}</td>
                            <td class="px-5 py-4">{{ $auditLog->service?->name ?? '—' }}</td>
                            <td class="px-5 py-4">{{ $auditLog->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-primary-700/55 dark:text-primary-100/45">Aucune trace d'audit trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $auditLogs->links('vendor.pagination.premium') }}
    </section>
@endsection
