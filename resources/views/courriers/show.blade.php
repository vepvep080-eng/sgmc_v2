@extends('layouts.app')

@section('title', "{$courrier->numero} — Bureau d'Ordre")
@section('page-title', 'Détail du courrier')

@section('content')
    <a href="{{ route('courriers.index') }}" class="inline-flex items-center gap-1.5 text-sm text-primary-700/60 dark:text-primary-100/50 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200 ease-fluent mb-5">
        <x-icon name="arrow-left" class="w-4 h-4" />
        Retour à la liste
    </a>

    <div class="relative rounded-[24px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark px-6 sm:px-9 py-8 opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="font-display font-semibold text-2xl text-primary-800 dark:text-primary-50">{{ $courrier->numero }}</span>
                    <x-badge :variant="$courrier->statusVariant()">{{ $courrier->statusLabel() }}</x-badge>
                    <x-badge :variant="$courrier->priorityVariant()">{{ $courrier->priorityLabel() }}</x-badge>
                </div>
                <p class="mt-1.5 text-primary-700/70 dark:text-primary-100/60">{{ $courrier->objet }}</p>
            </div>

            <div class="flex items-center gap-2">
                @can('view', $courrier)
                    <a
                        href="{{ route('courriers.export.pdf.show', $courrier) }}"
                        class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-primary-700/80 dark:text-primary-100/75 bg-primary-500/8 dark:bg-white/5 border border-primary-500/15 dark:border-white/10 hover:bg-primary-500/12 dark:hover:bg-white/10 transition-colors duration-200 ease-fluent"
                    >
                        <x-icon name="download" class="w-4 h-4" />
                        Exporter PDF
                    </a>
                @endcan

                <a
                    href="{{ route('courriers.print.show', $courrier) }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-primary-700/80 dark:text-primary-100/75 bg-primary-500/8 dark:bg-white/5 border border-primary-500/15 dark:border-white/10 hover:bg-primary-500/12 dark:hover:bg-white/10 transition-colors duration-200 ease-fluent"
                >
                    <x-icon name="download" class="w-4 h-4" />
                    Imprimer
                </a>

                <a
                    href="{{ route('courriers.edit', $courrier) }}"
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-primary-700/80 dark:text-primary-100/75 bg-primary-500/8 dark:bg-white/5 border border-primary-500/15 dark:border-white/10 hover:bg-primary-500/12 dark:hover:bg-white/10 transition-colors duration-200 ease-fluent"
                >
                    <x-icon name="pencil" class="w-4 h-4" />
                    Modifier
                </a>

                <button
                    type="button"
                    data-confirm-delete
                    data-url="{{ route('courriers.destroy', $courrier) }}"
                    data-message="Le courrier « {{ $courrier->numero }} » sera définitivement supprimé."
                    class="inline-flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold text-red-600 dark:text-red-300 bg-red-500/8 dark:bg-red-500/10 border border-red-500/20 hover:bg-red-500/14 transition-colors duration-200 ease-fluent"
                >
                    <x-icon name="trash" class="w-4 h-4" />
                    Supprimer
                </button>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 border-t border-white/50 dark:border-white/10 pt-7">
            <div>
                <dt class="text-xs font-medium tracking-wide text-primary-700/50 dark:text-primary-100/40">Expéditeur</dt>
                <dd class="mt-1 text-sm text-primary-800 dark:text-primary-50">{{ $courrier->expediteur }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium tracking-wide text-primary-700/50 dark:text-primary-100/40">Date de réception</dt>
                <dd class="mt-1 text-sm text-primary-800 dark:text-primary-50">{{ $courrier->date_reception?->locale(app()->getLocale())->translatedFormat('d F Y') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium tracking-wide text-primary-700/50 dark:text-primary-100/40">Référence</dt>
                <dd class="mt-1 text-sm text-primary-800 dark:text-primary-50">{{ $courrier->reference ?: '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium tracking-wide text-primary-700/50 dark:text-primary-100/40">Type</dt>
                <dd class="mt-1 text-sm text-primary-800 dark:text-primary-50">{{ $courrier->type }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium tracking-wide text-primary-700/50 dark:text-primary-100/40">Service destinataire</dt>
                <dd class="mt-1 text-sm text-primary-800 dark:text-primary-50">{{ $courrier->service?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium tracking-wide text-primary-700/50 dark:text-primary-100/40">Enregistré par</dt>
                <dd class="mt-1 text-sm text-primary-800 dark:text-primary-50">{{ $courrier->creator?->name ?? '—' }}</dd>
            </div>

            @if ($courrier->description)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-medium tracking-wide text-primary-700/50 dark:text-primary-100/40">Description</dt>
                    <dd class="mt-1 text-sm text-primary-800 dark:text-primary-50 whitespace-pre-line leading-relaxed">{{ $courrier->description }}</dd>
                </div>
            @endif

            <div class="sm:col-span-2">
                <dt class="text-xs font-medium tracking-wide text-primary-700/50 dark:text-primary-100/40">Pièce jointe</dt>
                <dd class="mt-2">
                    @if ($courrier->piece_jointe)
                        <a
                            href="{{ $courrier->pieceJointeUrl() }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-2.5 rounded-xl border border-white/70 dark:border-white/10 bg-white/50 dark:bg-white/[0.04] px-4 py-2.5 text-sm text-primary-700 dark:text-primary-100 hover:border-gold-400/60 transition-colors duration-200 ease-fluent"
                        >
                            <span class="w-8 h-8 rounded-lg bg-primary-500/10 dark:bg-white/10 flex items-center justify-center text-primary-600 dark:text-primary-200 text-[10px] font-bold uppercase">
                                {{ $courrier->pieceJointeExtension() }}
                            </span>
                            Télécharger la pièce jointe
                            <x-icon name="download" class="w-4 h-4 text-primary-500/50" />
                        </a>
                    @else
                        <p class="text-sm text-primary-700/45 dark:text-primary-100/35">Aucune pièce jointe.</p>
                    @endif
                </dd>
            </div>
        </div>

        <p class="mt-8 pt-5 border-t border-white/50 dark:border-white/10 text-xs text-primary-700/45 dark:text-primary-100/35">
            Créé le {{ $courrier->created_at->locale(app()->getLocale())->translatedFormat('d F Y à H:i') }}
            @if ($courrier->updated_at->ne($courrier->created_at))
                · Modifié le {{ $courrier->updated_at->locale(app()->getLocale())->translatedFormat('d F Y à H:i') }}
            @endif
        </p>
    </div>

    @include('courriers._routing_timeline', ['courrier' => $courrier])

    @can('update', $courrier)
        <section class="relative mt-6 rounded-[24px] border border-white/70 bg-white/55 dark:border-white/10 dark:bg-white/[0.05] backdrop-blur-2xl shadow-glass-lg dark:shadow-glass-lg-dark p-6" data-scan-panel data-courrier-id="{{ $courrier->id }}">
            <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-100/80 dark:bg-primary-500/15 border border-primary-500/15 dark:border-white/10 flex items-center justify-center shrink-0">
                        <x-icon name="archive" class="w-5 h-5 text-primary-700 dark:text-primary-100" />
                    </div>
                    <h2 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">{{ __('app.scan_documents') }}</h2>
                </div>
                {{-- La pill est stylée via [data-scanner-status] dans app.css :
                     scanner.js remplace className (status-*) à chaque mise à jour. --}}
                <p data-scanner-status>{{ __('app.scanner_unavailable') }}</p>
            </div>

            <button type="button" data-start-scan class="mt-5 inline-flex items-center gap-2 rounded-xl bg-primary px-5 py-2.5 text-sm font-bold text-white shadow-md transition-all hover:bg-primary-600 hover:scale-[1.02] active:scale-[0.99]">
                <x-icon name="refresh" class="w-4 h-4" />
                {{ __('app.start_scan') }}
            </button>

            <div class="mt-6 border-t border-primary-500/10 dark:border-white/10 pt-5" data-qr-panel>
                <h3 class="font-semibold text-primary-800 dark:text-primary-50">{{ __('app.read_qr') }}</h3>
                <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/60">{{ __('app.qr_value') }}</p>
                <input
                    data-qr-payload
                    type="text"
                    class="mt-3 w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-4 py-2.5 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                    maxlength="2048"
                />
                <button data-qr-submit type="button" class="mt-2.5 inline-flex items-center gap-2 rounded-xl border border-primary-500/15 dark:border-white/10 bg-white/60 dark:bg-white/[0.06] px-4 py-2 text-sm font-semibold text-primary-700 dark:text-primary-100 shadow-glass hover:bg-white/80 dark:hover:bg-white/[0.1] transition-all duration-200 ease-fluent">{{ __('app.read_qr') }}</button>
                <pre data-qr-result class="mt-3 hidden whitespace-pre-wrap rounded-xl border border-primary-500/10 bg-primary-100/60 dark:bg-white/5 p-3.5 text-sm text-primary-900 dark:text-primary-50"></pre>
            </div>
        </section>
        @push('scripts')
        <script>
            document.querySelector('[data-qr-submit]')?.addEventListener('click', async () => {
                const input = document.querySelector('[data-qr-payload]'); const result = document.querySelector('[data-qr-result]');
                const response = await fetch('{{ route('courriers.qr-payload.store', $courrier) }}', {method: 'POST', headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: JSON.stringify({payload: input.value})});
                const data = await response.json(); result.textContent = response.ok ? data.display : (data.message || ''); result.classList.remove('hidden');
            });
        </script>
        @endpush
    @endcan

@can('redirect', \App\Models\Routing::class)
        <section class="mt-6 rounded-[24px] border border-white/70 bg-white/55 p-6 dark:border-white/10 dark:bg-white/[0.05]">
            <h2 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">توجيه المراسلة</h2>
            
            <!-- Affichage de l'état de routage -->
            @if ($courrier->currentRouting && $courrier->currentRouting->department_id && is_null($courrier->currentRouting->service_destination_id))
                <div class="mb-4 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200/50 dark:border-amber-400/30">
                    <p class="font-medium text-amber-800 dark:text-amber-300 mb-1">
                        {{ __('app.status.pending_department_routing') }}
                    </p>
                    <p class="text-sm text-amber-700/80 dark:text-amber-200">
                        {{ __('app.status.pending_department_routing_description', ['department' => $courrier->currentRouting->department->name]) }}
                    </p>
                </div>
            @endif
            
            <form method="POST" action="{{ route('courriers.routings.store', $courrier) }}" class="mt-5 grid gap-4">
                @csrf
                @php
                    $me = auth()->user();
                    $isBoAdmin = $me && $me->hasRole(\App\Models\Role::ADMIN, \App\Models\Role::BUREAU_ORDRE);
                @endphp

                @if ($isBoAdmin)
                <div>
                    <span class="mb-1 block text-sm font-medium">Envoyer à (départements — choix multiple)</span>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @forelse($routableDepartments ?? [] as $department)
                            <label class="flex items-start gap-2 rounded-xl border px-3 py-2 text-sm">
                                <input type="checkbox" name="department_ids[]" value="{{ $department->id }}" class="mt-1"
                                    @checked(in_array($department->id, old('department_ids', $pendingDepartmentIds ?? []))) />
                                <span>{{ $department->name }}</span>
                            </label>
                        @empty
                            <p class="text-sm opacity-60">Aucun département actif.</p>
                        @endforelse
                    </div>
                    @error('department_ids')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                @else
                <div>
                    <span class="mb-1 block text-sm font-medium">Dispatcher vers (services de {{ $managedDepartment?->name ?? 'votre département' }} — choix multiple)</span>
                    <div class="grid gap-2 sm:grid-cols-2">
                        @forelse($dispatchableServices ?? [] as $service)
                            <label class="flex items-start gap-2 rounded-xl border px-3 py-2 text-sm">
                                <input type="checkbox" name="service_destination_ids[]" value="{{ $service->id }}" class="mt-1"
                                    @checked(in_array($service->id, old('service_destination_ids', [])))
                                    @disabled(in_array($service->id, $dispatchedServiceIds ?? [])) />
                                <span>{{ $service->name }}</span>
                            </label>
                        @empty
                            <p class="text-sm opacity-60">Aucun service dans votre département.</p>
                        @endforelse
                    </div>
                    @error('service_destination_ids')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                @endif
                <div>
                    <label for="commentaire" class="mb-1 block text-sm">Commentaire</label>
                    <textarea
                        id="commentaire"
                        name="commentaire"
                        class="w-full rounded-xl border px-3 py-2"
                    ></textarea>
                </div>
                <div>
                    <button type="submit" class="rounded-xl bg-primary-500 px-4 py-2 text-white">
                        {{ $isBoAdmin ? 'Envoyer aux départements' : 'Dispatcher vers les services' }}
                    </button>
                </div>
            </form>
        </section>
    @endcan
    @include('courriers._audit_history', ['courrier' => $courrier])
@endsection
