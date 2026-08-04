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
@endsection
