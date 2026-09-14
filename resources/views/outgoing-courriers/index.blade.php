@extends('layouts.app')

@section('title', __('app.outgoing').' — '.__('app.app_name'))
@section('page-title', __('app.outgoing'))

@php
    use Illuminate\Support\Str;

    $hasActiveFilters = collect($filters)->filter()->isNotEmpty();
    $sortUrl = fn (string $column) => request()->fullUrlWithQuery([
        'sort' => $column,
        'dir' => ($sort === $column && $direction === 'asc') ? 'desc' : 'asc',
        'page' => null,
    ]);
@endphp

@section('content')
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div>
            <nav class="flex items-center gap-1.5 text-[11px] font-medium text-primary-700/55 dark:text-primary-100/45" aria-label="Breadcrumb">
                <span>{{ __('app.dashboard') }}</span>
                <x-icon name="chevron-left" class="w-3 h-3 rtl-flip" />
                <span>{{ __('app.mail') }}</span>
                <x-icon name="chevron-left" class="w-3 h-3 rtl-flip" />
                <span class="font-semibold text-primary-700 dark:text-primary-100">{{ __('app.outgoing') }}</span>
            </nav>
            <div class="mt-1.5 flex flex-wrap items-center gap-2.5">
                <h2 class="font-display text-2xl font-semibold text-primary-800 dark:text-primary-50">{{ __('app.outgoing') }}</h2>
                <span class="tabular-nums rounded-full bg-primary-100/70 dark:bg-white/10 px-2.5 py-0.5 text-[11px] font-bold text-primary-700 dark:text-primary-100">{{ now()->format('Y') }}</span>
            </div>
            <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
                {{ $courriers->total() }} courrier{{ $courriers->total() > 1 ? 's' : '' }} enregistre{{ $courriers->total() > 1 ? 's' : '' }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('courriers-depart.export.pdf', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-primary-700 dark:text-primary-100 bg-white/60 dark:bg-white/[0.06] border border-primary-500/15 dark:border-white/10 shadow-glass hover:bg-white/80 dark:hover:bg-white/[0.1] hover:scale-[1.02] transition-all duration-200 ease-fluent">
                <x-icon name="download" class="h-4 w-4" />
                {{ __('app.export_pdf') }}
            </a>
            <a href="{{ route('courriers-depart.export.excel', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-primary-700 dark:text-primary-100 bg-white/60 dark:bg-white/[0.06] border border-primary-500/15 dark:border-white/10 shadow-glass hover:bg-white/80 dark:hover:bg-white/[0.1] hover:scale-[1.02] transition-all duration-200 ease-fluent">
                <x-icon name="download" class="h-4 w-4" />
                {{ __('app.export_excel') }}
            </a>
            <a href="{{ route('courriers-depart.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-primary-700 dark:text-primary-100 bg-white/60 dark:bg-white/[0.06] border border-primary-500/15 dark:border-white/10 shadow-glass hover:bg-white/80 dark:hover:bg-white/[0.1] hover:scale-[1.02] transition-all duration-200 ease-fluent">
                <x-icon name="download" class="h-4 w-4" />
                {{ __('app.print') }}
            </a>
            <a href="{{ route('courriers-depart.report', request()->query()) }}" class="inline-flex items-center gap-2 rounded-xl px-4 py-2 text-sm font-semibold text-primary-700 dark:text-primary-100 bg-white/60 dark:bg-white/[0.06] border border-primary-500/15 dark:border-white/10 shadow-glass hover:bg-white/80 dark:hover:bg-white/[0.1] hover:scale-[1.02] transition-all duration-200 ease-fluent">
                <x-icon name="building" class="h-4 w-4" />
                {{ __('app.report') }}
            </a>
        @can('create', \App\Models\OutgoingCourrier::class)
            <a href="{{ route('courriers-depart.create') }}" class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white bg-primary shadow-md hover:bg-primary-600 hover:scale-[1.02] active:scale-[0.99] transition-all">
                <x-icon name="plus-simple" class="h-4 w-4" />
                {{ __('app.add_mail') }}
            </a>
        @endcan
        </div>
    </div>

    <section class="mt-6 relative rounded-[22px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-xl border border-white/70 dark:border-white/10 shadow-glass px-5 sm:px-6 py-5 opacity-0 animate-fadeInUp" style="animation-delay: 0.1s;">
        <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

        <form method="GET" action="{{ route('courriers-depart.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4 xl:items-end">
            <div class="xl:col-span-2">
                <label for="q" class="mb-1.5 block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50">{{ __('app.search') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-primary-500/45 dark:text-primary-100/35">
                        <x-icon name="search" class="w-4 h-4" />
                    </span>
                    <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ __('app.search_placeholder') }}" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 pl-10 pr-4 py-2.5 text-sm text-primary-900 dark:text-primary-50 placeholder:text-primary-500/40 dark:placeholder:text-primary-100/30 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                </div>
            </div>

            <div>
                <label for="statut" class="mb-1.5 block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50">{{ __('app.status') }}</label>
                <select id="statut" name="statut" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('app.all_statuses') }}</option>
                    @foreach (\App\Models\OutgoingCourrier::STATUTS as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['statut'] ?? '') === $value)>{{ __('app.statuses.'.$value) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="priorite" class="mb-1.5 block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50">{{ __('app.priority') }}</label>
                <select id="priorite" name="priorite" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('app.all_priorities') }}</option>
                    @foreach (\App\Models\OutgoingCourrier::PRIORITES as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['priorite'] ?? '') === $value)>{{ __('app.priorities.'.$value) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="mode_envoi" class="mb-1.5 block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50">{{ __('app.send_mode') }}</label>
                <select id="mode_envoi" name="mode_envoi" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('app.all_modes') }}</option>
                    @foreach (\App\Models\OutgoingCourrier::MODES_ENVOI as $mode)
                        <option value="{{ $mode }}" @selected(($filters['mode_envoi'] ?? '') === $mode)>{{ __('app.modes.'.$mode) }}</option>
                    @endforeach
                </select>
            </div>

            @if ($services->isNotEmpty())
                <div>
                <label for="service_id" class="mb-1.5 block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50">{{ __('app.service') }}</label>
                    <select id="service_id" name="service_id" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20">
                    <option value="">{{ __('app.all_services') }}</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" @selected(($filters['service_id'] ?? null) == $service->id)>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div><label for="date_from" class="mb-1.5 block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50">Du</label><input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"></div>
            <div><label for="date_to" class="mb-1.5 block text-[11px] font-medium tracking-wide text-primary-700/60 dark:text-primary-100/50">Au</label><input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-xl bg-primary-100/50 dark:bg-white/[0.06] border border-primary-500/10 dark:border-white/10 px-3.5 py-2.5 text-sm text-primary-900 dark:text-primary-50 outline-none transition-all duration-300 ease-fluent focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20"></div>
            <div class="flex items-end gap-3">
                <button class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-white bg-primary shadow-md transition-all hover:bg-primary-600 hover:scale-[1.02] active:scale-[0.99]">
                    <x-icon name="filter" class="w-4 h-4" />
                    {{ __('app.filter') }}
                </button>
                @if ($hasActiveFilters)
                    <a href="{{ route('courriers-depart.index') }}" class="inline-flex items-center justify-center w-[42px] h-[42px] rounded-xl text-primary-700/60 dark:text-primary-100/50 bg-primary-500/8 dark:bg-white/5 border border-primary-500/15 dark:border-white/10 hover:bg-primary-500/12 dark:hover:bg-white/10 transition-colors duration-200 ease-fluent" title="{{ __('app.reset_filters') }}">
                        <x-icon name="refresh" class="w-4 h-4" />
                    </a>
                @endif
            </div>
        </form>
    </section>

    <section class="mt-6 relative overflow-hidden rounded-[24px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark opacity-0 animate-fadeInUp" style="animation-delay: 0.15s;">
        <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent z-10"></div>

        @if ($courriers->isEmpty())
            <x-empty-state icon="inbox" :title="__('app.no_outgoing')" :description="__('app.no_outgoing_description')">
                @can('create', \App\Models\OutgoingCourrier::class)
                    <x-slot:action>
                        <a href="{{ route('courriers-depart.create') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-r from-primary-500 to-secondary-500 rounded-xl px-5 py-2.5 shadow-[0_10px_24px_-8px_rgba(11,61,145,0.45)] hover:scale-[1.02] transition-transform duration-200 ease-fluent">
                            <x-icon name="plus-simple" class="w-4 h-4" />
                            {{ __('app.add_mail') }}
                        </a>
                    </x-slot:action>
                @endcan
            </x-empty-state>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="sticky top-0 z-[5] bg-primary-100/45 dark:bg-midnight-900/60 backdrop-blur-xl">
                    <tr class="border-b border-white/50 dark:border-white/10 text-left">
                        @foreach (['numero' => 'number', 'objet' => 'subject', 'destinataire' => 'recipient', 'organisme_destinataire' => 'organization', 'service' => 'service', 'date_envoi' => 'send_date', 'mode_envoi' => 'send_mode', 'priorite' => 'priority', 'statut' => 'status'] as $column => $label)
                            <th class="px-4 py-3.5">
                                <a href="{{ $sortUrl($column) }}" class="inline-flex items-center gap-1 text-[11px] font-semibold tracking-wide uppercase text-primary-800/85 dark:text-primary-100/80 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200">{{ __('app.'.$label) }}</a>
                            </th>
                        @endforeach
                        <th class="px-6 sm:px-8 py-3.5 text-[11px] font-semibold tracking-wide uppercase text-primary-800/85 dark:text-primary-100/80 text-right">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary-500/10 dark:divide-white/5">
                    @forelse ($courriers as $courrier)
                        <tr class="group even:bg-primary-100/30 dark:even:bg-white/[0.015] hover:bg-primary-100/60 dark:hover:bg-white/[0.04] transition-colors duration-150 ease-fluent">
                            <td class="px-6 sm:px-8 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="h-2 w-2 shrink-0 rounded-full {{ ['tres_urgente' => 'bg-red-500', 'urgente' => 'bg-amber-500', 'normale' => 'bg-emerald-500'][$courrier->priorite] ?? 'bg-slate-400' }}" aria-hidden="true"></span>
                                    <a href="{{ route('courriers-depart.show', $courrier) }}" class="font-semibold tabular-nums text-primary-800 dark:text-primary-100 hover:text-gold-600 dark:hover:text-gold-400 transition-colors duration-150 whitespace-nowrap">{{ $courrier->numero }}</a>
                                </div>
                            </td>
                            <td class="px-4 py-4 max-w-[240px]">
                                <a href="{{ route('courriers-depart.show', $courrier) }}" class="block text-primary-800/90 dark:text-primary-50/90 hover:text-primary-600 dark:hover:text-white transition-colors duration-150" title="{{ $courrier->objet }}">{{ Str::limit($courrier->objet, 48) }}</a>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2.5 min-w-[150px]">
                                    <div class="w-8 h-8 shrink-0 rounded-full bg-primary-100/80 dark:bg-white/10 flex items-center justify-center text-primary-700 dark:text-primary-200 text-[11px] font-semibold">{{ strtoupper(substr($courrier->destinataire, 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <span class="block truncate text-primary-700/85 dark:text-primary-100/75">{{ Str::limit($courrier->destinataire, 22) }}</span>
                                        @if ($courrier->organisme_destinataire)
                                            <span class="block truncate text-[11px] text-primary-700/50 dark:text-primary-100/40">{{ Str::limit($courrier->organisme_destinataire, 24) }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 hidden xl:table-cell whitespace-nowrap">
                                @if ($courrier->service)
                                    <span class="inline-flex items-center rounded-lg bg-primary-100/60 dark:bg-white/10 px-2.5 py-1 text-[11px] font-medium text-primary-700/85 dark:text-primary-100/70 whitespace-nowrap">{{ $courrier->service->name }}</span>
                                @else
                                    <span class="text-primary-700/40 dark:text-primary-100/30">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 hidden lg:table-cell whitespace-nowrap tabular-nums text-primary-700/75 dark:text-primary-100/65">{{ $courrier->date_envoi?->locale(app()->getLocale())->translatedFormat('d F Y') ?? '—' }}</td>
                            <td class="px-4 py-4 hidden md:table-cell whitespace-nowrap">
                                <span class="inline-flex items-center rounded-lg bg-primary-100/60 dark:bg-white/10 px-2.5 py-1 text-[11px] font-medium text-primary-700/85 dark:text-primary-100/70 whitespace-nowrap">{{ $courrier->modeEnvoiLabel() }}</span>
                            </td>
                            <td class="px-4 py-4"><x-badge :variant="$courrier->priorityVariant()">{{ $courrier->priorityLabel() }}</x-badge></td>
                            <td class="px-4 py-4"><x-badge :variant="$courrier->statusVariant()">{{ $courrier->statusLabel() }}</x-badge></td>
                            <td class="px-6 sm:px-8 py-4">
                                <div class="flex items-center justify-end gap-1.5 opacity-70 group-hover:opacity-100 transition-opacity duration-150">
                                    <a href="{{ route('courriers-depart.show', $courrier) }}" title="{{ __('app.view') }}" class="w-8 h-8 rounded-lg bg-primary-500/8 dark:bg-white/5 flex items-center justify-center text-primary-500/70 dark:text-primary-100/60 hover:text-primary-700 hover:bg-primary-500/15 dark:hover:text-primary-100 dark:hover:bg-white/10 transition-colors duration-150">
                                        <x-icon name="eye" class="w-4 h-4" />
                                    </a>
                                    @can('update', $courrier)
                                        <a href="{{ route('courriers-depart.edit', $courrier) }}" title="{{ __('app.edit') }}" class="w-8 h-8 rounded-lg bg-primary-500/8 dark:bg-white/5 flex items-center justify-center text-primary-500/70 dark:text-primary-100/60 hover:text-gold-600 hover:bg-gold-500/15 dark:hover:text-gold-400 transition-colors duration-150">
                                            <x-icon name="pencil" class="w-4 h-4" />
                                        </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-12 text-center text-primary-700/45 dark:text-primary-100/35">{{ __('app.no_outgoing') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $courriers->links('vendor.pagination.premium') }}
        @endif
    </section>
@endsection
