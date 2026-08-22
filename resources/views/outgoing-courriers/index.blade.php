@extends('layouts.app')

@section('title', __('app.outgoing').' — '.__('app.app_name'))
@section('page-title', __('app.outgoing'))

@php
    $hasActiveFilters = collect($filters)->filter()->isNotEmpty();
    $sortUrl = fn (string $column) => request()->fullUrlWithQuery([
        'sort' => $column,
        'dir' => ($sort === $column && $direction === 'asc') ? 'desc' : 'asc',
        'page' => null,
    ]);
@endphp

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="font-display text-2xl font-semibold text-primary-800 dark:text-primary-50">{{ __('app.outgoing') }}</h2>
            <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
                {{ $courriers->total() }} courrier{{ $courriers->total() > 1 ? 's' : '' }} enregistre{{ $courriers->total() > 1 ? 's' : '' }}
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('courriers-depart.export.pdf', request()->query()) }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-primary-700 dark:text-primary-100">{{ __('app.export_pdf') }}</a>
            <a href="{{ route('courriers-depart.export.excel', request()->query()) }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-primary-700 dark:text-primary-100">{{ __('app.export_excel') }}</a>
            <a href="{{ route('courriers-depart.print', request()->query()) }}" target="_blank" class="rounded-xl px-3 py-2 text-sm font-semibold text-primary-700 dark:text-primary-100">{{ __('app.print') }}</a>
            <a href="{{ route('courriers-depart.report', request()->query()) }}" class="rounded-xl px-3 py-2 text-sm font-semibold text-primary-700 dark:text-primary-100">{{ __('app.report') }}</a>
        @can('create', \App\Models\OutgoingCourrier::class)
            <a href="{{ route('courriers-depart.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 px-5 py-2.5 text-sm font-semibold text-white shadow-[0_10px_28px_-8px_rgba(11,61,145,0.5)]">
                <x-icon name="plus-simple" class="h-4 w-4" />
                {{ __('app.add_mail') }}
            </a>
        @endcan
        </div>
    </div>

    <section class="mt-6 rounded-[22px] border border-white/70 bg-white/55 p-5 shadow-glass backdrop-blur-xl dark:border-white/10 dark:bg-white/[0.05] sm:p-6">
        <form method="GET" action="{{ route('courriers-depart.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="xl:col-span-2">
                <label for="q" class="mb-1.5 block text-[11px] font-medium text-primary-700/60 dark:text-primary-100/50">{{ __('app.search') }}</label>
                <input id="q" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Numero, objet, destinataire, organisme..." class="w-full rounded-xl border border-white/70 bg-white/70 px-4 py-2.5 text-sm text-primary-900 dark:border-white/10 dark:bg-white/[0.06] dark:text-primary-50">
            </div>

            <div>
                <label for="statut" class="mb-1.5 block text-[11px] font-medium text-primary-700/60 dark:text-primary-100/50">{{ __('app.status') }}</label>
                <select id="statut" name="statut" class="w-full rounded-xl border border-white/70 bg-white/70 px-3.5 py-2.5 text-sm dark:border-white/10 dark:bg-white/[0.06]">
                    <option value="">{{ __('app.all_statuses') }}</option>
                    @foreach (\App\Models\OutgoingCourrier::STATUTS as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['statut'] ?? '') === $value)>{{ __('app.statuses.'.$value) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="priorite" class="mb-1.5 block text-[11px] font-medium text-primary-700/60 dark:text-primary-100/50">{{ __('app.priority') }}</label>
                <select id="priorite" name="priorite" class="w-full rounded-xl border border-white/70 bg-white/70 px-3.5 py-2.5 text-sm dark:border-white/10 dark:bg-white/[0.06]">
                    <option value="">{{ __('app.all_priorities') }}</option>
                    @foreach (\App\Models\OutgoingCourrier::PRIORITES as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['priorite'] ?? '') === $value)>{{ __('app.priorities.'.$value) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="mode_envoi" class="mb-1.5 block text-[11px] font-medium text-primary-700/60 dark:text-primary-100/50">{{ __('app.send_mode') }}</label>
                <select id="mode_envoi" name="mode_envoi" class="w-full rounded-xl border border-white/70 bg-white/70 px-3.5 py-2.5 text-sm dark:border-white/10 dark:bg-white/[0.06]">
                    <option value="">{{ __('app.all_modes') }}</option>
                    @foreach (\App\Models\OutgoingCourrier::MODES_ENVOI as $mode)
                        <option value="{{ $mode }}" @selected(($filters['mode_envoi'] ?? '') === $mode)>{{ __('app.modes.'.$mode) }}</option>
                    @endforeach
                </select>
            </div>

            @if ($services->isNotEmpty())
                <div>
                <label for="service_id" class="mb-1.5 block text-[11px] font-medium text-primary-700/60 dark:text-primary-100/50">{{ __('app.service') }}</label>
                    <select id="service_id" name="service_id" class="w-full rounded-xl border border-white/70 bg-white/70 px-3.5 py-2.5 text-sm dark:border-white/10 dark:bg-white/[0.06]">
                    <option value="">{{ __('app.all_services') }}</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}" @selected(($filters['service_id'] ?? null) == $service->id)>{{ $service->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div><label for="date_from" class="mb-1.5 block text-[11px] font-medium text-primary-700/60 dark:text-primary-100/50">Du</label><input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-xl border border-white/70 bg-white/70 px-3.5 py-2.5 text-sm dark:border-white/10 dark:bg-white/[0.06]"></div>
            <div><label for="date_to" class="mb-1.5 block text-[11px] font-medium text-primary-700/60 dark:text-primary-100/50">Au</label><input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-xl border border-white/70 bg-white/70 px-3.5 py-2.5 text-sm dark:border-white/10 dark:bg-white/[0.06]"></div>
            <div class="flex items-end gap-3">
                <button class="rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 px-5 py-2.5 text-sm font-semibold text-white">{{ __('app.filter') }}</button>
                @if ($hasActiveFilters)
                    <a href="{{ route('courriers-depart.index') }}" class="px-4 py-2.5 text-sm font-semibold text-primary-700/70 dark:text-primary-100/65">Reinitialiser</a>
                @endif
            </div>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-[24px] border border-white/70 bg-white/55 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05]">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-white/50 text-xs uppercase tracking-wide text-primary-700/55 dark:border-white/10 dark:text-primary-100/45">
                    <tr>
                        @foreach (['numero' => 'number', 'objet' => 'subject', 'destinataire' => 'recipient', 'organisme_destinataire' => 'organization', 'service' => 'service', 'date_envoi' => 'send_date', 'mode_envoi' => 'send_mode', 'priorite' => 'priority', 'statut' => 'status'] as $column => $label)
                            <th class="px-5 py-4"><a href="{{ $sortUrl($column) }}">{{ __('app.'.$label) }}</a></th>
                        @endforeach
                        <th class="px-5 py-4">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 dark:divide-white/5">
                    @forelse ($courriers as $courrier)
                        <tr class="text-primary-700/75 dark:text-primary-100/70">
                            <td class="whitespace-nowrap px-5 py-4 font-semibold"><a href="{{ route('courriers-depart.show', $courrier) }}">{{ $courrier->numero }}</a></td>
                            <td class="px-5 py-4">{{ $courrier->objet }}</td>
                            <td class="px-5 py-4">{{ $courrier->destinataire }}</td>
                            <td class="px-5 py-4">{{ $courrier->organisme_destinataire }}</td>
                            <td class="px-5 py-4">{{ $courrier->service?->name ?? '-' }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $courrier->date_envoi?->format('d/m/Y') }}</td>
                            <td class="px-5 py-4">{{ $courrier->modeEnvoiLabel() }}</td>
                            <td class="px-5 py-4"><x-badge :variant="$courrier->priorityVariant()">{{ $courrier->priorityLabel() }}</x-badge></td>
                            <td class="px-5 py-4"><x-badge :variant="$courrier->statusVariant()">{{ $courrier->statusLabel() }}</x-badge></td>
                            <td class="px-5 py-4"><div class="flex gap-2"><a href="{{ route('courriers-depart.show', $courrier) }}" class="text-primary-600 hover:text-gold-600">{{ __('app.view') }}</a>@can('update', $courrier)<a href="{{ route('courriers-depart.edit', $courrier) }}" class="text-primary-600 hover:text-gold-600">{{ __('app.edit') }}</a>@endcan</div></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <x-empty-state icon="inbox" :title="__('app.no_outgoing')" :description="__('app.no_outgoing_description')">
                                    @can('create', \App\Models\OutgoingCourrier::class)
                                        <x-slot:action><a href="{{ route('courriers-depart.create') }}" class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white">{{ __('app.add_mail') }}</a></x-slot:action>
                                    @endcan
                                </x-empty-state>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $courriers->links('vendor.pagination.premium') }}
    </section>
@endsection
