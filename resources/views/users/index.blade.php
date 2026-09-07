@extends('layouts.app')

@section('title', "Utilisateurs — Bureau d'Ordre")
@section('page-title', __('app.users_management'))

@section('content')

    {{-- Header row: search / filter / create --}}
    <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-6">
        <form method="GET" action="{{ route('users.index') }}" class="flex-1 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-primary-700/40 dark:text-primary-100/35">
                    <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3" stroke-linecap="round"/>
                </svg>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="{{ __('app.search_user_placeholder') }}"
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm bg-white/60 dark:bg-white/[0.04] border border-white/60 dark:border-white/10 text-primary-800 dark:text-primary-50 placeholder:text-primary-700/40 dark:placeholder:text-primary-100/30 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
                />
            </div>

            <select
                name="role_id"
                onchange="this.form.submit()"
                class="px-4 py-2.5 rounded-xl text-sm bg-white/60 dark:bg-white/[0.04] border border-white/60 dark:border-white/10 text-primary-800 dark:text-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-500/30"
            >
                <option value="">{{ __('app.all_roles') }}</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected(request('role_id') == $role->id)>{{ $role->name }}</option>
                @endforeach
            </select>

            @if (request('search') || request('role_id'))
                <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-xl text-sm font-medium text-primary-700/60 dark:text-primary-100/50 hover:text-primary-700 dark:hover:text-primary-50 transition-colors duration-200 ease-fluent">
                    {{ __('app.reset') }}
                </a>
            @endif
        </form>

        @can('create', App\Models\User::class)

            <a
                href="{{ route('users.create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-br from-primary-600 to-primary-700 text-white shadow-[0_8px_20px_-6px_rgba(11,61,145,0.45)] hover:shadow-[0_10px_24px_-6px_rgba(11,61,145,0.55)] transition-shadow duration-200 ease-fluent whitespace-nowrap"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                    <path d="M12 5v14M5 12h14" stroke-linecap="round"/>
                </svg>
                {{ __('app.new_user') }}
            </a>
        @endcan
    </div>

    {{-- Users table card --}}
    <div class="rounded-2xl bg-white/60 dark:bg-white/[0.04] backdrop-blur-2xl border border-white/60 dark:border-white/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-white/50 dark:border-white/10">
                        <th class="text-left px-6 py-3.5 font-semibold text-[11px] tracking-wider uppercase text-primary-700/45 dark:text-primary-100/35">{{ __('app.table_name') }}</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-[11px] tracking-wider uppercase text-primary-700/45 dark:text-primary-100/35">{{ __('app.table_email') }}</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-[11px] tracking-wider uppercase text-primary-700/45 dark:text-primary-100/35">{{ __('app.table_role') }}</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-[11px] tracking-wider uppercase text-primary-700/45 dark:text-primary-100/35">{{ __('app.service') }}</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-[11px] tracking-wider uppercase text-primary-700/45 dark:text-primary-100/35">{{ __('app.section') }}</th>
                        <th class="text-left px-6 py-3.5 font-semibold text-[11px] tracking-wider uppercase text-primary-700/45 dark:text-primary-100/35">{{ __('app.status') }}</th>
                        <th class="text-right px-6 py-3.5 font-semibold text-[11px] tracking-wider uppercase text-primary-700/45 dark:text-primary-100/35">{{ __('app.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/40 dark:divide-white/[0.06]">
                    @forelse ($users as $user)
                        <tr class="hover:bg-primary-500/[0.03] dark:hover:bg-white/[0.02] transition-colors duration-150">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-secondary-500 flex items-center justify-center text-white text-xs font-semibold shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-primary-800 dark:text-primary-50">{{ $user->name}}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-primary-700/70 dark:text-primary-100/60">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @if ($user->role)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-primary-500/10 text-primary-700 dark:bg-white/10 dark:text-primary-50">
                                        {{ $user->role->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-primary-700/40 dark:text-primary-100/30">{{ __('app.no_role') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->service)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-secondary-500/10 text-secondary-700 dark:bg-white/10 dark:text-primary-100/60">
                                        {{ $user->service->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-primary-700/40 dark:text-primary-100/30">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->section)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-500/10 text-green-700 dark:bg-white/10 dark:text-primary-100/60">
                                        {{ $user->section->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-primary-700/40 dark:text-primary-100/30">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-500/10 text-emerald-700 dark:text-emerald-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ __('app.active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-rose-500/10 text-rose-700 dark:text-rose-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> {{ __('app.suspended') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    @can('update', $user)

                                        <a
                                            href="{{ route('users.edit', $user) }}"
                                            title="{{ __('app.edit') }}"
                                            class="p-2 rounded-lg text-primary-700/60 dark:text-primary-100/50 hover:bg-primary-500/10 hover:text-primary-700 dark:hover:bg-white/10 dark:hover:text-primary-50 transition-colors duration-150"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-4 h-4">
                                                <path d="M11 4H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5Z" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('delete', $user)
                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('{{ __('app.delete_confirm_user', ['name' => $user->name]) }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                title="{{ __('app.delete') }}"
                                                class="p-2 rounded-lg text-rose-600/70 dark:text-rose-300/70 hover:bg-rose-500/10 hover:text-rose-700 dark:hover:text-rose-200 transition-colors duration-150"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="w-4 h-4">
                                                    <path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-primary-700/45 dark:text-primary-100/35">
                                {{ __('app.no_users_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-white/50 dark:border-white/10">
                {{ $users->links() }}
            </div>
        @endif
    </div>

@endsection
