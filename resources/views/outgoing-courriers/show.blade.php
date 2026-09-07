@extends('layouts.app')

@section('title', "{$courrier->numero} - Bureau d'Ordre")
@section('page-title', 'Detail du courrier depart')

@section('content')
    <a href="{{ route('courriers-depart.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm text-primary-700/60 hover:text-primary-700 dark:text-primary-100/50 dark:hover:text-primary-100"><x-icon name="arrow-left" class="h-4 w-4" />Retour a la liste</a>

    <div class="rounded-[24px] border border-white/70 bg-white/55 px-6 py-8 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] sm:px-9">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div><div class="flex flex-wrap items-center gap-2.5"><span class="font-display text-2xl font-semibold text-primary-800 dark:text-primary-50">{{ $courrier->numero }}</span><x-badge :variant="$courrier->statusVariant()">{{ $courrier->statusLabel() }}</x-badge><x-badge :variant="$courrier->priorityVariant()">{{ $courrier->priorityLabel() }}</x-badge></div><p class="mt-1.5 text-primary-700/70 dark:text-primary-100/60">{{ $courrier->objet }}</p></div>
            @can('update', $courrier)<a href="{{ route('courriers-depart.edit', $courrier) }}" class="rounded-xl border border-primary-500/15 bg-primary-500/8 px-4 py-2.5 text-sm font-semibold text-primary-700 dark:text-primary-100">Modifier</a>@endcan
        </div>

        <dl class="mt-8 grid grid-cols-1 gap-x-8 gap-y-6 border-t border-white/50 pt-7 text-sm sm:grid-cols-2">
            <div><dt class="text-xs text-primary-700/50">Destinataire</dt><dd class="mt-1 text-primary-800 dark:text-primary-50">{{ $courrier->destinataire }}</dd></div><div><dt class="text-xs text-primary-700/50">Organisme</dt><dd class="mt-1 text-primary-800 dark:text-primary-50">{{ $courrier->organisme_destinataire }}</dd></div><div><dt class="text-xs text-primary-700/50">Adresse</dt><dd class="mt-1 whitespace-pre-line text-primary-800 dark:text-primary-50">{{ $courrier->adresse_destinataire ?: '-' }}</dd></div><div><dt class="text-xs text-primary-700/50">Service</dt><dd class="mt-1 text-primary-800 dark:text-primary-50">{{ $courrier->service?->name ?? '-' }}</dd></div><div><dt class="text-xs text-primary-700/50">Date d'envoi</dt><dd class="mt-1 text-primary-800 dark:text-primary-50">{{ $courrier->date_envoi?->locale(app()->getLocale())->translatedFormat('d F Y') }}</dd></div><div><dt class="text-xs text-primary-700/50">Mode d'envoi</dt><dd class="mt-1 text-primary-800 dark:text-primary-50">{{ $courrier->mode_envoi }}</dd></div><div><dt class="text-xs text-primary-700/50">Reference postale</dt><dd class="mt-1 text-primary-800 dark:text-primary-50">{{ $courrier->reference_postale ?: '-' }}</dd></div><div><dt class="text-xs text-primary-700/50">Createur</dt><dd class="mt-1 text-primary-800 dark:text-primary-50">{{ $courrier->creator?->name ?? '-' }}</dd></div>
            @if ($courrier->description)<div class="sm:col-span-2"><dt class="text-xs text-primary-700/50">Description</dt><dd class="mt-1 whitespace-pre-line text-primary-800 dark:text-primary-50">{{ $courrier->description }}</dd></div>@endif
            @if ($courrier->attachment)<div class="sm:col-span-2"><dt class="text-xs text-primary-700/50">Piece jointe</dt><dd class="mt-2"><a href="{{ $courrier->attachmentUrl() }}" class="font-semibold text-primary-700 hover:text-gold-600 dark:text-primary-100">Telecharger le fichier ({{ strtoupper($courrier->attachmentExtension()) }})</a></dd></div>@endif
        </dl>

        <section class="mt-8 border-t border-white/50 pt-6">
            <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">Workflow</h3>
            <div class="mt-4 flex flex-wrap gap-3">
                @can('prepare', $courrier)<form method="POST" action="{{ route('courriers-depart.prepare', $courrier) }}">@csrf<button class="rounded-xl bg-gold-500 px-4 py-2.5 text-sm font-semibold text-primary-900">Marquer comme pret</button></form>@endcan
                @can('send', $courrier)<form method="POST" action="{{ route('courriers-depart.send', $courrier) }}">@csrf<button class="rounded-xl bg-secondary-600 px-4 py-2.5 text-sm font-semibold text-white">Confirmer l'envoi</button></form>@endcan
                @can('archive', $courrier)<form method="POST" action="{{ route('courriers-depart.archive', $courrier) }}">@csrf<button class="rounded-xl bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white">Archiver</button></form>@endcan
            </div>
        </section>

        <section class="mt-8 border-t border-white/50 pt-6">
            <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">Historique</h3>
            <ol class="mt-4 space-y-3">
                @forelse ($courrier->auditLogs as $auditLog)
                    <li class="rounded-xl border border-white/60 bg-white/40 p-4 text-sm dark:border-white/10 dark:bg-white/[0.03]"><div class="flex flex-wrap justify-between gap-2"><span class="font-semibold text-primary-800 dark:text-primary-50">{{ $auditLog->action }}</span><span class="text-xs text-primary-700/50 dark:text-primary-100/40">{{ $auditLog->created_at->locale(app()->getLocale())->translatedFormat('d/m/Y H:i') }}</span></div><p class="mt-1 text-primary-700/70 dark:text-primary-100/60">{{ $auditLog->description }}</p><p class="mt-1 text-xs text-primary-700/50 dark:text-primary-100/40">{{ $auditLog->user?->name ?? 'Systeme' }}</p></li>
                @empty
                    <li class="text-sm text-primary-700/60 dark:text-primary-100/50">Aucune trace disponible.</li>
                @endforelse
            </ol>
        </section>
    </div>
@endsection
