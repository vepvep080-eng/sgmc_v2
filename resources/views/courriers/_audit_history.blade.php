@if ($courrier->auditLogs->isNotEmpty())
    <section class="mt-8 rounded-[24px] border border-white/70 bg-white/55 p-5 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] dark:shadow-glass-lg sm:p-6">
        <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">Historique</h3>

        <ol class="mt-5 space-y-4">
            @foreach ($courrier->auditLogs as $auditLog)
                <li class="rounded-2xl border border-white/60 bg-white/40 p-4 dark:border-white/10 dark:bg-white/[0.03]">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-primary-800 dark:text-primary-50">{{ $auditLog->action }}</p>
                        <p class="text-xs text-primary-700/50 dark:text-primary-100/40">{{ $auditLog->created_at->locale('fr')->translatedFormat('d/m/Y H:i') }}</p>
                    </div>
                    <p class="mt-2 text-sm text-primary-700/70 dark:text-primary-100/60">{{ $auditLog->description }}</p>
                    <p class="mt-2 text-xs text-primary-700/50 dark:text-primary-100/40">
                        {{ $auditLog->user?->name ?? 'Système' }}
                        @if ($auditLog->service)
                            · {{ $auditLog->service->name }}
                        @endif
                    </p>
                </li>
            @endforeach
        </ol>
    </section>
@endif
