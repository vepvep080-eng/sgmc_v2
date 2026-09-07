@php
    $currentRouting = $courrier->currentRouting;

    $timeline = [[
        'at' => $courrier->created_at,
        'title' => 'Courrier créé',
        'icon' => 'inbox',
        'iconClass' => 'text-primary-600 dark:text-primary-200 bg-primary-500/10 dark:bg-white/10',
        'status' => 'Nouveau',
        'variant' => 'gold',
        'source' => "Bureau d'Ordre",
        'destination' => $courrier->service?->name ?? '—',
        'user' => $courrier->creator?->name ?? '—',
        'commentaire' => null,
        'attachmentUrl' => null,
        'attachmentExtension' => null,
    ]];

    foreach ($courrier->routings->sortBy('sent_at')->values() as $index => $routing) {
        $isPending = $routing->isPending();

        $timeline[] = [
            'at' => $routing->sent_at,
            'title' => $index === 0 ? 'Premier routage' : 'Courrier re-routé',
            'icon' => 'refresh',
            'iconClass' => $isPending
                ? 'text-gold-700 dark:text-gold-300 bg-gold-500/15 dark:bg-gold-500/10'
                : 'text-primary-600 dark:text-primary-200 bg-primary-500/10 dark:bg-white/10',
            'status' => $isPending ? 'En attente' : 'Routé',
            'variant' => $isPending ? 'gold' : 'default',
            'source' => $routing->serviceSource?->name ?? "Bureau d'Ordre",
            'destination' => $routing->serviceDestination?->name ?? '—',
            'user' => $routing->routedBy?->name ?? '—',
            'commentaire' => $isPending ? $routing->commentaire : null,
            'attachmentUrl' => null,
            'attachmentExtension' => null,
        ];

        if ($routing->statut === \App\Models\Routing::STATUT_ACCEPTE) {
            $timeline[] = [
                'at' => $routing->received_at,
                'title' => 'Courrier accepté',
                'icon' => 'check-circle',
                'iconClass' => 'text-emerald-700 dark:text-emerald-300 bg-emerald-500/15 dark:bg-emerald-500/10',
                'status' => 'Accepté',
                'variant' => 'green',
                'source' => $routing->serviceSource?->name ?? "Bureau d'Ordre",
                'destination' => $routing->serviceDestination?->name ?? '—',
                'user' => $routing->routedBy?->name ?? '—',
                'commentaire' => $routing->commentaire,
                'attachmentUrl' => null,
                'attachmentExtension' => null,
            ];
        }

        if ($routing->statut === \App\Models\Routing::STATUT_REFUSE) {
            $timeline[] = [
                'at' => $routing->received_at,
                'title' => 'Courrier refusé',
                'icon' => 'exclamation-triangle',
                'iconClass' => 'text-red-700 dark:text-red-300 bg-red-500/15 dark:bg-red-500/10',
                'status' => 'Refusé',
                'variant' => 'red',
                'source' => $routing->serviceSource?->name ?? "Bureau d'Ordre",
                'destination' => $routing->serviceDestination?->name ?? '—',
                'user' => $routing->routedBy?->name ?? '—',
                'commentaire' => $routing->commentaire,
                'attachmentUrl' => null,
                'attachmentExtension' => null,
            ];

            $timeline[] = [
                'at' => $routing->received_at,
                'title' => "Retour au Bureau d'Ordre",
                'icon' => 'home',
                'iconClass' => 'text-red-700 dark:text-red-300 bg-red-500/15 dark:bg-red-500/10',
                'status' => 'Retourné',
                'variant' => 'red',
                'source' => $routing->serviceDestination?->name ?? '—',
                'destination' => "Bureau d'Ordre",
                'user' => $routing->routedBy?->name ?? '—',
                'commentaire' => null,
                'attachmentUrl' => null,
                'attachmentExtension' => null,
            ];
        }
    }

    if ($courrier->traite_le) {
        $timeline[] = [
            'at' => $courrier->traite_le,
            'title' => 'Courrier traité',
            'icon' => 'check-circle',
            'iconClass' => 'text-emerald-700 dark:text-emerald-300 bg-emerald-500/15 dark:bg-emerald-500/10',
            'status' => 'Traité',
            'variant' => 'green',
            'source' => $courrier->service?->name ?? '—',
            'destination' => 'Traitement du courrier',
            'user' => $courrier->treatedBy?->name ?? '—',
            'commentaire' => $courrier->traitement_commentaire,
            'attachmentUrl' => $courrier->traitementPieceJointeUrl(),
            'attachmentExtension' => $courrier->traitementPieceJointeExtension(),
        ];
    }

    if ($courrier->archived_at) {
        $timeline[] = [
            'at' => $courrier->archived_at,
            'title' => 'Courrier archivé',
            'icon' => 'archive',
            'iconClass' => 'text-slate-600 dark:text-primary-100/70 bg-slate-500/10 dark:bg-white/10',
            'status' => 'Archivé',
            'variant' => 'gray',
            'source' => $courrier->service?->name ?? '—',
            'destination' => 'Archives',
            'user' => $courrier->archivedBy?->name ?? '—',
            'commentaire' => null,
            'attachmentUrl' => null,
            'attachmentExtension' => null,
        ];
    }

    $timeline = collect($timeline)->sortBy('at')->values();
@endphp

@if (
    $currentRouting
    && $currentRouting->statut === \App\Models\Routing::STATUT_EN_ATTENTE
    && auth()->user()->can('accept', $currentRouting)
)
    <div class="mt-6 rounded-[24px] border border-gold-400/40 bg-gold-500/8 px-6 py-5 dark:bg-gold-500/10">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                    Décision requise
                </h3>
                <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
                    Ce courrier est en attente de votre décision pour le service <strong>{{ $currentRouting->serviceDestination->name }}</strong>.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <form method="POST" action="{{ route('routings.accept', $currentRouting) }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition-transform duration-200 ease-fluent hover:scale-[1.02]">
                        <x-icon name="check-circle" class="w-4 h-4" />
                        Accepter
                    </button>
                </form>

                <button
                    type="button"
                    onclick="document.getElementById('reject-modal-{{ $currentRouting->id }}').showModal()"
                    class="inline-flex items-center gap-2 rounded-xl border border-red-500/20 bg-red-500/8 px-4 py-2.5 text-sm font-semibold text-red-600 transition-colors duration-200 ease-fluent hover:bg-red-500/14 dark:bg-red-500/10 dark:text-red-300"
                >
                    <x-icon name="exclamation-triangle" class="w-4 h-4" />
                    Refuser
                </button>
            </div>
        </div>
    </div>

    <dialog
        id="reject-modal-{{ $currentRouting->id }}"
        class="w-[90vw] max-w-md rounded-2xl border-none bg-transparent p-0 backdrop:bg-midnight-950/60 backdrop:backdrop-blur-sm"
    >
        <div class="relative rounded-2xl border border-white/70 bg-white/95 p-6 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-midnight-900/95 sm:p-7">
            <div class="absolute left-7 top-0 h-[2px] w-14 rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <h4 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                Refuser ce courrier
            </h4>
            <p class="mt-1.5 text-sm text-primary-700/60 dark:text-primary-100/50">
                Le motif est obligatoire et restera visible dans l'historique. Le courrier retournera au Bureau d'Ordre.
            </p>

            <form method="POST" action="{{ route('routings.reject', $currentRouting) }}" class="mt-5">
                @csrf

                <label for="commentaire-reject-{{ $currentRouting->id }}" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">
                    Motif du refus <span class="text-red-500">*</span>
                </label>
                <textarea
                    id="commentaire-reject-{{ $currentRouting->id }}"
                    name="commentaire"
                    rows="3"
                    required
                    placeholder="Expliquez pourquoi ce courrier est refusé..."
                    class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-2.5 text-sm text-primary-900 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50 @error('commentaire') border-red-400 @enderror"
                >{{ old('commentaire') }}</textarea>
                @error('commentaire')
                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div class="mt-5 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('reject-modal-{{ $currentRouting->id }}').close()"
                        class="rounded-xl px-4 py-2.5 text-sm font-medium text-primary-700/60 transition-colors duration-200 ease-fluent hover:text-primary-800 dark:text-primary-100/50 dark:hover:text-primary-50"
                    >
                        Annuler
                    </button>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-200 ease-fluent hover:bg-red-700"
                    >
                        Confirmer le refus
                    </button>
                </div>
            </form>
        </div>
    </dialog>
@endif

@if (
    $currentRouting
    && $currentRouting->statut === \App\Models\Routing::STATUT_ACCEPTE
    && $courrier->statut === 'en_cours'
    && auth()->user()->can('process', $currentRouting)
)
    <div class="mt-6 rounded-[24px] border border-emerald-500/20 bg-emerald-500/5 px-6 py-5 dark:bg-emerald-500/10">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                    Traitement du courrier
                </h3>
                <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
                    Ce courrier a été accepté par votre service et peut maintenant être traité.
                </p>
            </div>

            <button
                type="button"
                onclick="document.getElementById('process-modal-{{ $currentRouting->id }}').showModal()"
                class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition-transform duration-200 ease-fluent hover:scale-[1.02]"
            >
                <x-icon name="check-circle" class="w-4 h-4" />
                Traiter
            </button>
        </div>
    </div>

    <dialog
        id="process-modal-{{ $currentRouting->id }}"
        class="w-[90vw] max-w-md rounded-2xl border-none bg-transparent p-0 backdrop:bg-midnight-950/60 backdrop:backdrop-blur-sm"
    >
        <div class="relative rounded-2xl border border-white/70 bg-white/95 p-6 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-midnight-900/95 sm:p-7">
            <div class="absolute left-7 top-0 h-[2px] w-14 rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <h4 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                Traiter ce courrier
            </h4>

            <p class="mt-1.5 text-sm text-primary-700/60 dark:text-primary-100/50">
                Ajoutez le compte rendu du traitement. Le courrier passera au statut « Traité ».
            </p>

            <form
                method="POST"
                action="{{ route('routings.process', $currentRouting) }}"
                enctype="multipart/form-data"
                class="mt-5"
            >
                @csrf

                <label for="commentaire-traitement-{{ $currentRouting->id }}" class="mb-1.5 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">
                    Commentaire <span class="text-red-500">*</span>
                </label>

                <textarea
                    id="commentaire-traitement-{{ $currentRouting->id }}"
                    name="commentaire"
                    rows="4"
                    required
                    class="w-full rounded-xl border border-white/70 bg-white/60 px-4 py-2.5 text-sm text-primary-900 outline-none transition-all duration-300 ease-fluent focus:border-gold-500 focus:ring-2 focus:ring-gold-400/40 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50 @error('commentaire') border-red-400 @enderror"
                >{{ old('commentaire') }}</textarea>

                @error('commentaire')
                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <label for="piece-jointe-traitement-{{ $currentRouting->id }}" class="mb-1.5 mt-4 block text-xs font-medium tracking-wide text-primary-700/75 dark:text-primary-100/70">
                    Pièce jointe
                    <span class="font-normal normal-case text-primary-700/40 dark:text-primary-100/30">(optionnelle)</span>
                </label>

                <input
                    id="piece-jointe-traitement-{{ $currentRouting->id }}"
                    name="piece_jointe_traitement"
                    type="file"
                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                    class="block w-full rounded-xl border border-white/70 bg-white/60 px-4 py-2.5 text-sm text-primary-700 file:mr-4 file:rounded-lg file:border-0 file:bg-primary-500/10 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-primary-700 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-100"
                />

                <p class="mt-1.5 text-xs text-primary-700/45 dark:text-primary-100/35">
                    PDF, DOC, DOCX ou image — 10 Mo maximum.
                </p>

                @error('piece_jointe_traitement')
                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div class="mt-5 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('process-modal-{{ $currentRouting->id }}').close()"
                        class="rounded-xl px-4 py-2.5 text-sm font-medium text-primary-700/60 transition-colors duration-200 ease-fluent hover:text-primary-800 dark:text-primary-100/50 dark:hover:text-primary-50"
                    >
                        Annuler
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition-transform duration-200 ease-fluent hover:scale-[1.02]"
                    >
                        <x-icon name="check-circle" class="w-4 h-4" />
                        Confirmer le traitement
                    </button>
                </div>
            </form>
        </div>
    </dialog>
@endif

@if ($courrier->statut === 'traite' && auth()->user()->can('archive', $courrier))
    <div class="mt-6 rounded-[24px] border border-slate-500/20 bg-slate-500/5 px-6 py-5 dark:bg-white/[0.03]">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                    Archivage du courrier
                </h3>
                <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
                    Ce courrier a été traité et peut être placé dans les archives.
                </p>
            </div>

            <button
                type="button"
                onclick="document.getElementById('archive-modal-{{ $courrier->id }}').showModal()"
                class="inline-flex items-center gap-2 rounded-xl bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-200 ease-fluent hover:bg-slate-800 dark:bg-slate-600 dark:hover:bg-slate-500"
            >
                <x-icon name="archive" class="w-4 h-4" />
                Archiver
            </button>
        </div>
    </div>

    <dialog
        id="archive-modal-{{ $courrier->id }}"
        class="w-[90vw] max-w-md rounded-2xl border-none bg-transparent p-0 backdrop:bg-midnight-950/60 backdrop:backdrop-blur-sm"
    >
        <div class="relative rounded-2xl border border-white/70 bg-white/95 p-6 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-midnight-900/95 sm:p-7">
            <div class="absolute left-7 top-0 h-[2px] w-14 rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <h4 class="font-display text-lg font-semibold text-primary-800 dark:text-primary-50">
                Archiver ce courrier
            </h4>

            <p class="mt-1.5 text-sm text-primary-700/60 dark:text-primary-100/50">
                Confirmez l’archivage du courrier <strong>{{ $courrier->numero }}</strong>. Son historique et ses pièces jointes seront conservés.
            </p>

            <form method="POST" action="{{ route('courriers.archive', $courrier) }}" class="mt-5">
                @csrf

                <div class="flex items-center justify-end gap-3">
                    <button
                        type="button"
                        onclick="document.getElementById('archive-modal-{{ $courrier->id }}').close()"
                        class="rounded-xl px-4 py-2.5 text-sm font-medium text-primary-700/60 transition-colors duration-200 ease-fluent hover:text-primary-800 dark:text-primary-100/50 dark:hover:text-primary-50"
                    >
                        Annuler
                    </button>

                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white transition-colors duration-200 ease-fluent hover:bg-slate-800 dark:bg-slate-600 dark:hover:bg-slate-500"
                    >
                        <x-icon name="archive" class="w-4 h-4" />
                        Confirmer l’archivage
                    </button>
                </div>
            </form>
        </div>
    </dialog>
@endif

<ol class="mt-6 space-y-6">
    @foreach ($timeline as $event)
        <li class="relative pl-9 sm:pl-11">
            @if (! $loop->last)
                <span class="absolute left-[15px] top-10 bottom-[-24px] w-px bg-white/70 dark:bg-white/10 sm:left-[19px]"></span>
            @endif

            <span class="absolute left-0 top-0 flex h-8 w-8 items-center justify-center rounded-full {{ $event['iconClass'] }}">
                <x-icon :name="$event['icon']" class="h-4 w-4" />
            </span>

            <div class="rounded-2xl border border-white/70 bg-white/40 px-4 py-4 dark:border-white/10 dark:bg-white/[0.03] sm:px-5">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="text-sm font-semibold text-primary-800 dark:text-primary-50">
                                {{ $event['title'] }}
                            </h4>

                            <x-badge :variant="$event['variant']">
                                {{ $event['status'] }}
                            </x-badge>
                        </div>

                        <p class="mt-1 text-xs text-primary-700/55 dark:text-primary-100/45">
                            {{ $event['at']?->locale(app()->getLocale())->translatedFormat('d F Y à H:i') ?? '—' }}
                        </p>
                    </div>
                </div>

                <dl class="mt-4 grid grid-cols-1 gap-x-5 gap-y-3 border-t border-white/50 pt-4 text-xs dark:border-white/10 sm:grid-cols-3">
                    <div>
                        <dt class="text-primary-700/45 dark:text-primary-100/35">Source</dt>
                        <dd class="mt-0.5 font-medium text-primary-700 dark:text-primary-100">{{ $event['source'] }}</dd>
                    </div>

                    <div>
                        <dt class="text-primary-700/45 dark:text-primary-100/35">Destination</dt>
                        <dd class="mt-0.5 font-medium text-primary-700 dark:text-primary-100">{{ $event['destination'] }}</dd>
                    </div>

                    <div>
                        <dt class="text-primary-700/45 dark:text-primary-100/35">Utilisateur associé</dt>
                        <dd class="mt-0.5 font-medium text-primary-700 dark:text-primary-100">{{ $event['user'] }}</dd>
                    </div>
                </dl>

                @if ($event['commentaire'])
                    <p class="mt-4 rounded-xl border border-primary-500/10 bg-primary-500/5 px-3 py-2.5 text-sm italic text-primary-700/70 dark:border-white/10 dark:bg-white/[0.03] dark:text-primary-100/60">
                        « {{ $event['commentaire'] }} »
                    </p>
                @endif

                @if ($event['attachmentUrl'])
                    <a
                        href="{{ $event['attachmentUrl'] }}"
                        target="_blank"
                        rel="noopener"
                        class="mt-4 inline-flex items-center gap-2 rounded-xl border border-white/70 bg-white/50 px-3 py-2 text-xs font-medium text-primary-700 transition-colors duration-200 hover:border-gold-400/60 dark:border-white/10 dark:bg-white/[0.04] dark:text-primary-100"
                    >
                        <x-icon name="paperclip" class="h-4 w-4 text-primary-500/60 dark:text-primary-100/50" />
                        Télécharger la pièce jointe
                        @if ($event['attachmentExtension'])
                            ({{ strtoupper($event['attachmentExtension']) }})
                        @endif
                    </a>
                @endif
            </div>
        </li>
    @endforeach
</ol>
