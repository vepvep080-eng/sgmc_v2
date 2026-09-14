@extends('layouts.app')

@section('title', __('app.incoming') . " — " . __('app.app_name'))
@section('page-title', __('app.incoming'))

@php
    use Illuminate\Support\Str;

    $hasActiveFilters = collect($filters)->filter()->isNotEmpty();

    // Toggles sort direction when the same column header is clicked again.
    $sortUrl = fn (string $column) => request()->fullUrlWithQuery([
        'sort' => $column,
        'dir' => ($sort === $column && $direction === 'asc') ? 'desc' : 'asc',
        'page' => null,
    ]);

    $sortIcon = fn (string $column) => $sort === $column
        ? ($direction === 'asc' ? 'chevron-down rotate-180' : 'chevron-down')
        : null;
@endphp

@section('content')
    {{-- Header (maquette: breadcrumb + year badge + export cluster) --}}
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div>
            <nav class="flex items-center gap-1.5 text-[11px] font-medium text-primary-700/55 dark:text-primary-100/45" aria-label="Breadcrumb">
                <span>{{ __('app.dashboard') }}</span>
                <x-icon name="chevron-left" class="w-3 h-3 rtl-flip" />
                <span>{{ __('app.mail') }}</span>
                <x-icon name="chevron-left" class="w-3 h-3 rtl-flip" />
                <span class="font-semibold text-primary-700 dark:text-primary-100">{{ __('app.incoming') }}</span>
            </nav>
            <div class="mt-1.5 flex flex-wrap items-center gap-2.5">
                <h2 class="font-display font-semibold text-2xl text-primary-800 dark:text-primary-50">{{ __('app.incoming') }}</h2>
                <span class="tabular-nums rounded-full bg-primary-100/70 dark:bg-white/10 px-2.5 py-0.5 text-[11px] font-bold text-primary-700 dark:text-primary-100">{{ now()->format('Y') }}</span>
            </div>
            <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
                {{ $courriers->total() }} {{ __('app.mail') }} {{ __('app.registered') }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <a
                href="{{ route('courriers.export.pdf', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-primary-700 dark:text-primary-100 bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 shadow-glass transition-all duration-300 ease-fluent hover:bg-white/80 dark:hover:bg-white/[0.1] hover:scale-[1.02] active:scale-[0.99]"
            >
                <x-icon name="download" class="w-4 h-4" />
                {{ __('app.export_pdf') }}
            </a>

            <a
                href="{{ route('courriers.print', request()->query()) }}"
                target="_blank"
                class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-primary-700 dark:text-primary-100 bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 shadow-glass transition-all duration-300 ease-fluent hover:bg-white/80 dark:hover:bg-white/[0.1] hover:scale-[1.02] active:scale-[0.99]"
            >
                <x-icon name="download" class="w-4 h-4" />
                {{ __('app.print') }}
            </a>

            <a
                href="{{ route('courriers.export.excel', request()->query()) }}"
                class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-primary-700 dark:text-primary-100 bg-white/60 dark:bg-white/[0.06] border border-white/70 dark:border-white/10 shadow-glass transition-all duration-300 ease-fluent hover:bg-white/80 dark:hover:bg-white/[0.1] hover:scale-[1.02] active:scale-[0.99]"
            >
                <x-icon name="download" class="w-4 h-4" />
                {{ __('app.export_excel') }}
            </a>

            <a
                href="{{ route('courriers.create') }}"
                class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white bg-primary shadow-md transition-all hover:bg-primary-600 hover:scale-[1.02] active:scale-[0.99]"
            >
                <x-icon name="plus-simple" class="w-4 h-4" />
                {{ __('app.add_mail') }}
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mt-6 relative rounded-[22px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-xl border border-white/70 dark:border-white/10 shadow-glass px-5 sm:px-6 py-5 opacity-0 animate-fadeInUp" style="animation-delay: 0.1s;">
        <form method="GET" action="{{ route('courriers.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-7 xl:items-end">

            {{-- Search --}}
            <div class="min-w-0 2xl:col-span-2">
                <label for="q" class="block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50 mb-1.5">
                    {{ __('app.search') }}
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-primary-500/45 dark:text-primary-100/35">
                        <x-icon name="search" class="w-4 h-4" />
                    </span>
                    <input
                        id="q" name="q" type="text"
                        value="{{ $filters['q'] ?? '' }}"
                        placeholder="{{ __('app.search_placeholder') }}"
                        class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 pl-10 pr-4 py-2.5 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                    />
                </div>
            </div>

            {{-- Statut --}}
            <div class="w-full">
                <label for="statut" class="block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50 mb-1.5">
                    Statut
                </label>
                <select
                    id="statut" name="statut"
                    class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                >
                    <option value="">{{ __('app.all_statuses') }}</option>
                    @foreach (\App\Models\Courrier::STATUTS as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['statut'] ?? '') === $value)>{{ __('app.statuses.'.$value) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Priorité --}}
            <div class="w-full">
                <label for="priorite" class="block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50 mb-1.5">
                    Priorité
                </label>
                <select
                    id="priorite" name="priorite"
                    class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                >
                    <option value="">{{ __('app.all_priorities') }}</option>
                    @foreach (\App\Models\Courrier::PRIORITES as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['priorite'] ?? '') === $value)>{{ __('app.priorities.'.$value) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            @if ($types->isNotEmpty())
                <div class="w-full">
                    <label for="type" class="block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50 mb-1.5">
                        Type
                    </label>
                    <select
                        id="type" name="type"
                        class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"
                    >
                        <option value="">{{ __('app.all_types') }}</option>
                        @foreach ($types as $type)
                            <option value="{{ $type }}" @selected(($filters['type'] ?? '') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="w-full">
                <label for="service_id" class="block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50 mb-1.5">{{ __('app.service') }}</label>
                <select id="service_id" name="service_id" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('app.all_services') }}</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}" @selected((string) ($filters['service_id'] ?? '') === (string) $service->id)>{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full">
                <label for="date_from" class="block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50 mb-1.5">{{ __('app.reception_from') }}</label>
                <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20" />
            </div>

            <div class="w-full">
                <label for="date_to" class="block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50 mb-1.5">{{ __('app.reception_to') }}</label>
                <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20" />
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2 shrink-0">
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white bg-primary shadow-md transition-all hover:bg-primary-600 hover:scale-[1.02] active:scale-[0.99]"
                >
                    <x-icon name="filter" class="w-4 h-4" />
                    {{ __('app.filter') }}
                </button>

                @if ($hasActiveFilters)
                    <a
                        href="{{ route('courriers.index') }}"
                        title="{{ __('app.reset_filters') }}"
                        class="inline-flex items-center justify-center w-[42px] h-[42px] rounded-xl text-primary-700/60 dark:text-primary-100/50 bg-primary-500/8 dark:bg-white/5 border border-primary-500/15 dark:border-white/10 hover:bg-primary-500/12 dark:hover:bg-white/10 transition-colors duration-200 ease-fluent"
                    >
                        <x-icon name="refresh" class="w-4 h-4" />
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="mt-6 relative rounded-[24px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark overflow-hidden opacity-0 animate-fadeInUp" style="animation-delay: 0.15s;">
        <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent z-10"></div>

        @if ($courriers->isEmpty())
            @if ($hasActiveFilters)
                <x-empty-state
                    icon="search"
                    title="{{ __('app.no_results') }}"
                    description="{{ __('app.no_results_description') }}"
                >
                    <x-slot:action>
                        <a href="{{ route('courriers.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary-700/80 dark:text-primary-100/75 bg-primary-500/8 dark:bg-white/5 border border-primary-500/15 dark:border-white/10 rounded-xl px-5 py-2.5 hover:bg-primary-500/12 dark:hover:bg-white/10 transition-colors duration-200 ease-fluent">
                            <x-icon name="refresh" class="w-4 h-4" />
                            {{ __('app.reset_filters') }}
                        </a>
                    </x-slot:action>
                </x-empty-state>
            @else
                <x-empty-state
                    icon="inbox"
                    title="{{ __('app.no_mail') }}"
                    description="{{ __('app.no_mail_description') }}"
                >
                    <x-slot:action>
                        <a href="{{ route('courriers.create') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-r from-primary-500 to-secondary-500 rounded-xl px-5 py-2.5 shadow-[0_10px_24px_-8px_rgba(11,61,145,0.45)] hover:scale-[1.02] transition-transform duration-200 ease-fluent">
                            <x-icon name="plus-simple" class="w-4 h-4" />
                            {{ __('app.create_first_mail') }}
                        </a>
                    </x-slot:action>
                </x-empty-state>
            @endif
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 z-[5] bg-primary-100/45 dark:bg-midnight-900/60 backdrop-blur-xl">
                        <tr class="border-b-2 border-outline-strong/40 dark:border-white/10 text-left">
                            <th class="px-6 sm:px-8 py-3.5">
                                <a href="{{ $sortUrl('numero') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold tracking-wide uppercase text-primary-800/85 dark:text-primary-100/80 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200">
                                    {{ __('app.mail_number') }}
                                    @if ($sortIcon('numero'))<x-icon name="chevron-down" class="w-3 h-3 {{ $direction === 'asc' && $sort === 'numero' ? 'rotate-180' : '' }}" />@endif
                                </a>
                            </th>
                            <th class="px-4 py-3.5">
                                <a href="{{ $sortUrl('objet') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold tracking-wide uppercase text-primary-800/85 dark:text-primary-100/80 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200">
                                    {{ __('app.subject') }}
                                    @if ($sortIcon('objet'))<x-icon name="chevron-down" class="w-3 h-3 {{ $direction === 'asc' && $sort === 'objet' ? 'rotate-180' : '' }}" />@endif
                                </a>
                            </th>
                            <th class="px-4 py-3.5 hidden md:table-cell">
                                <a href="{{ $sortUrl('expediteur') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold tracking-wide uppercase text-primary-800/85 dark:text-primary-100/80 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200">
                                    {{ __('app.sender') }}
                                    @if ($sortIcon('expediteur'))<x-icon name="chevron-down" class="w-3 h-3 {{ $direction === 'asc' && $sort === 'expediteur' ? 'rotate-180' : '' }}" />@endif
                                </a>
                            </th>
                            <th class="px-4 py-3.5 hidden sm:table-cell">
                                <a href="{{ $sortUrl('date_reception') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold tracking-wide uppercase text-primary-800/85 dark:text-primary-100/80 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200">
                                    {{ __('app.reception') }}
                                    @if ($sortIcon('date_reception'))<x-icon name="chevron-down" class="w-3 h-3 {{ $direction === 'asc' && $sort === 'date_reception' ? 'rotate-180' : '' }}" />@endif
                                </a>
                            </th>
                            <th class="px-4 py-3.5 hidden lg:table-cell">
                                <a href="{{ $sortUrl('service') }}" class="inline-flex items-center gap-1 text-[11px] font-semibold tracking-wide uppercase text-primary-800/85 dark:text-primary-100/80 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200">
                                    {{ __('app.service') }}
                                    @if ($sortIcon('service'))<x-icon name="chevron-down" class="w-3 h-3 {{ $direction === 'asc' && $sort === 'service' ? 'rotate-180' : '' }}" />@endif
                                </a>
                            </th>
                            <th class="px-4 py-3.5 text-[11px] font-semibold tracking-wide uppercase text-primary-800/85 dark:text-primary-100/80">{{ __('app.priority') }}</th>
                            <th class="px-4 py-3.5 text-[11px] font-semibold tracking-wide uppercase text-primary-800/85 dark:text-primary-100/80">Statut</th>
                            <th class="px-6 sm:px-8 py-3.5"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary-500/10 dark:divide-white/5">
                        @foreach ($courriers as $courrier)
                            <tr class="group even:bg-primary-100/30 dark:even:bg-white/[0.015] hover:bg-primary-100/60 dark:hover:bg-white/[0.04] transition-colors duration-150 ease-fluent">
                                <td class="px-6 sm:px-8 py-4">
                                    <a href="{{ route('courriers.show', $courrier) }}" class="font-semibold tabular-nums text-primary-800 dark:text-primary-100 hover:text-gold-600 dark:hover:text-gold-400 transition-colors duration-150 whitespace-nowrap">
                                        {{ $courrier->numero }}
                                    </a>
                                </td>
                                <td class="px-4 py-4 max-w-[220px]">
                                    <a href="{{ route('courriers.show', $courrier) }}" class="block text-primary-800 dark:text-primary-50/90 hover:text-primary-600 dark:hover:text-white transition-colors duration-150" title="{{ $courrier->objet }}">
                                        {{ Str::limit($courrier->objet, 48) }}
                                    </a>
                                    <p class="md:hidden mt-0.5 text-xs text-primary-700/45 dark:text-primary-100/35 truncate">{{ $courrier->expediteur }}</p>
                                </td>
                                <td class="px-4 py-4 hidden md:table-cell">
                                    <div class="flex items-center gap-2.5 min-w-[150px]">
                                        <div class="w-8 h-8 shrink-0 rounded-full bg-primary-100/80 dark:bg-white/10 flex items-center justify-center text-primary-700 dark:text-primary-200 text-[11px] font-semibold">
                                            {{ strtoupper(substr($courrier->expediteur, 0, 1)) }}
                                        </div>
                                        <span class="text-primary-700/80 dark:text-primary-100/70">{{ Str::limit($courrier->expediteur, 30) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 hidden sm:table-cell text-primary-700/70 dark:text-primary-100/60 whitespace-nowrap tabular-nums">
                                    {{ $courrier->date_reception?->locale(app()->getLocale())->translatedFormat('d F Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-4 hidden lg:table-cell whitespace-nowrap">
                                    @if ($courrier->service)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-primary-100/60 dark:bg-white/10 text-[11px] font-medium text-primary-700/85 dark:text-primary-100/70">{{ $courrier->service->name }}</span>
                                    @else
                                        <span class="text-primary-700/40 dark:text-primary-100/30">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <x-badge :variant="$courrier->priorityVariant()">{{ $courrier->priorityLabel() }}</x-badge>
                                </td>
                                <td class="px-4 py-4">
                                    <x-badge :variant="$courrier->statusVariant()">{{ $courrier->statusLabel() }}</x-badge>
                                </td>
                                <td class="px-6 sm:px-8 py-4">
                                    <div class="flex items-center justify-end gap-1.5 opacity-70 group-hover:opacity-100 transition-opacity duration-150">
                                        <a href="{{ route('courriers.show', $courrier) }}" title="{{ __('app.view') }}" class="w-8 h-8 rounded-lg bg-primary-500/8 dark:bg-white/5 flex items-center justify-center text-primary-500/70 dark:text-primary-100/60 hover:text-primary-700 hover:bg-primary-500/15 dark:hover:text-primary-100 dark:hover:bg-white/10 transition-colors duration-150">
                                            <x-icon name="eye" class="w-4 h-4" />
                                        </a>
                                        <a href="{{ route('courriers.edit', $courrier) }}" title="{{ __('app.edit') }}" class="w-8 h-8 rounded-lg bg-primary-500/8 dark:bg-white/5 flex items-center justify-center text-primary-500/70 dark:text-primary-100/60 hover:text-gold-600 hover:bg-gold-500/15 dark:hover:text-gold-400 transition-colors duration-150">
                                            <x-icon name="pencil" class="w-4 h-4" />
                                        </a>
                                        <button
                                            type="button"
                                            data-confirm-delete
                                            data-url="{{ route('courriers.destroy', $courrier) }}"
                                            data-message="{{ __('app.delete_confirm_mail', ['numero' => $courrier->numero]) }}"
                                            title="{{ __('app.delete') }}"
                                            class="w-8 h-8 rounded-lg bg-primary-500/8 dark:bg-white/5 flex items-center justify-center text-primary-500/70 dark:text-primary-100/60 hover:text-red-600 hover:bg-red-500/15 dark:hover:text-red-400 transition-colors duration-150"
                                        >
                                            <x-icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $courriers->links('vendor.pagination.premium') }}

            {{-- Quick archival context notice --}}
            <div class="mt-5 rounded-xl bg-primary-100/60 dark:bg-white/[0.05] px-5 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-10 h-10 shrink-0 rounded-lg bg-primary-100/80 dark:bg-white/10 flex items-center justify-center text-primary-700 dark:text-primary-100">
                        <x-icon name="archive" class="w-5 h-5" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-primary-800 dark:text-primary-50">{{ __('app.archival_notice_title') }}</p>
                        <p class="mt-1 text-xs text-primary-700/60 dark:text-primary-100/45">{{ __('app.archival_notice_description') }}</p>
                    </div>
                </div>
                <span class="hidden lg:inline-flex shrink-0 items-center rounded-full bg-primary-100/80 dark:bg-white/10 px-3 py-1.5 text-[11px] font-semibold text-primary-700/85 dark:text-primary-100/80">{{ __('app.archival_notice_regulation') }}</span>
            </div>
        @endif
    </div>
@endsection
