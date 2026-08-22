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
                <dd class="mt-1 text-sm text-primary-800 dark:text-primary-50">{{ $courrier->date_reception?->locale('fr')->translatedFormat('d F Y') ?? '—' }}</dd>
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
            Créé le {{ $courrier->created_at->locale('fr')->translatedFormat('d F Y à H:i') }}
            @if ($courrier->updated_at->ne($courrier->created_at))
                · Modifié le {{ $courrier->updated_at->locale('fr')->translatedFormat('d F Y à H:i') }}
            @endif
        </p>
    </div>

    @include('courriers._routing_timeline', ['courrier' => $courrier])

    @can('update', $courrier)
        <section class="mt-6 rounded-[24px] border border-white/70 bg-white/55 p-6 dark:border-white/10 dark:bg-white/[0.05]" data-scan-panel data-courrier-id="{{ $courrier->id }}">
            <h2 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">{{ __('app.scan_documents') }}</h2>
            <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/60" data-scanner-status>{{ __('app.scanner_unavailable') }}</p>
            <button type="button" data-start-scan class="mt-4 rounded-xl bg-primary-500 px-4 py-2 text-sm font-semibold text-white">{{ __('app.start_scan') }}</button>

            <div class="mt-6 border-t pt-5 dark:border-white/10" data-qr-panel>
                <h3 class="font-semibold text-primary-800 dark:text-primary-50">{{ __('app.read_qr') }}</h3>
                <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/60">{{ __('app.qr_value') }}</p>
                <input
                    data-qr-payload
                    type="text"
                    class="mt-3 w-full rounded-xl border border-primary-500/15 dark:border-white/10 bg-white/70 dark:bg-white/[0.04] px-3 py-2 text-primary-800 dark:text-primary-50 placeholder:text-primary-700/40 dark:placeholder:text-primary-100/30"
                    maxlength="2048"
                />
                <button data-qr-submit type="button" class="mt-2 rounded-xl border border-primary-500/15 dark:border-white/10 px-4 py-2 text-sm text-primary-700 dark:text-primary-100">{{ __('app.read_qr') }}</button>
                <pre data-qr-result class="mt-3 hidden whitespace-pre-wrap rounded-lg bg-slate-50 dark:bg-white/5 p-3 text-sm text-primary-800 dark:text-primary-50"></pre>
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
            <form method="POST" action="{{ route('courriers.routings.store', $courrier) }}" class="mt-5 grid gap-4 sm:grid-cols-3">
                @csrf
                <div>
                    <label for="department_id" class="mb-1 block text-sm text-primary-700/70 dark:text-primary-100/60">{{ __('app.department') }}</label>
                    <select id="department_id" name="department_id" class="w-full rounded-xl border border-primary-500/15 dark:border-white/10 bg-white/70 dark:bg-white/[0.04] px-3 py-2 text-primary-800 dark:text-primary-50">
                        <option value="">{{ __('app.no_department') }}</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="service_destination_id" class="mb-1 block text-sm text-primary-700/70 dark:text-primary-100/60">{{ __('app.service') }}</label>
                    <select id="service_destination_id" name="service_destination_id" required class="w-full rounded-xl border border-primary-500/15 dark:border-white/10 bg-white/70 dark:bg-white/[0.04] px-3 py-2 text-primary-800 dark:text-primary-50">
                        @foreach($departments as $department)
                            <optgroup label="{{ $department->name }}">
                                @foreach($department->services as $service)
                                    <option value="{{ $service->id }}" data-department="{{ $department->id }}">{{ $service->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('service_destination_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="recipient_user_id" class="mb-1 block text-sm text-primary-700/70 dark:text-primary-100/60">{{ __('app.manager') }}</label>
                    <select id="recipient_user_id" name="recipient_user_id" class="w-full rounded-xl border border-primary-500/15 dark:border-white/10 bg-white/70 dark:bg-white/[0.04] px-3 py-2 text-primary-800 dark:text-primary-50">
                        <option value="">{{ __('app.no_manager') }}</option>
                        @foreach($recipients as $recipient)
                            <option value="{{ $recipient->id }}" data-service="{{ $recipient->service_id }}">{{ $recipient->name }}{{ $recipient->service ? ' — '.$recipient->service->name : '' }}</option>
                        @endforeach
                    </select>
                    @error('recipient_user_id')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-3">
                    <label for="commentaire" class="mb-1 block text-sm text-primary-700/70 dark:text-primary-100/60">{{ __('app.description') }}</label>
                    <textarea
                        id="commentaire"
                        name="commentaire"
                        class="w-full rounded-xl border border-primary-500/15 dark:border-white/10 bg-white/70 dark:bg-white/[0.04] px-3 py-2 text-primary-800 dark:text-primary-50 placeholder:text-primary-700/40 dark:placeholder:text-primary-100/30"
                    ></textarea>
                </div>
                <div class="sm:col-span-3">
                    <button class="rounded-xl bg-primary-500 px-4 py-2 text-white">توجيه</button>
                </div>
            </form>
        </section>
    @endcan
    @include('courriers._audit_history', ['courrier' => $courrier])
@endsection
