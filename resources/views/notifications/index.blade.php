@extends('layouts.app')

@section('title', 'Notifications — Bureau d\'Ordre')
@section('page-title', __('app.notifications'))

@section('content')
    <div class="flex items-end justify-between gap-4 opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div>
            <h2 class="font-display text-2xl font-semibold text-primary-800 dark:text-primary-50">{{ __('app.notifications') }}</h2>
            <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">Vos dernières activités de courrier.</p>
        </div>
    </div>

    <section class="mt-6 overflow-hidden rounded-[24px] border border-white/70 bg-white/55 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] dark:shadow-glass-lg-dark opacity-0 animate-fadeInUp" style="animation-delay: 0.1s;">
        @forelse ($notifications as $notification)
            <article class="flex items-center gap-4 border-b border-white/40 px-6 py-5 last:border-0 dark:border-white/5 sm:px-8 {{ $notification->read_at ? '' : 'bg-primary-500/[0.04] dark:bg-white/[0.03]' }}">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ ($notification->data['event'] ?? '') === 'archived' ? 'bg-primary-500/10 text-primary-600' : 'bg-secondary-500/10 text-secondary-600' }} dark:text-secondary-300">
                    <x-icon :name="($notification->data['event'] ?? '') === 'archived' ? 'archive' : 'inbox'" class="h-5 w-5" />
                </div>
                <div class="min-w-0 flex-1">
                    <a href="{{ $notification->data['url'] ?? route('dashboard') }}" class="block text-sm font-semibold text-primary-800 transition-colors hover:text-gold-600 dark:text-primary-50 dark:hover:text-gold-400">{{ $notification->data['title'] ?? 'Notification' }}</a>
                    <p class="mt-1 text-sm text-primary-700/65 dark:text-primary-100/55">{{ $notification->data['message'] ?? '' }}</p>
                    <p class="mt-1.5 text-xs text-primary-700/45 dark:text-primary-100/35">{{ $notification->created_at->locale('fr')->translatedFormat('d F Y à H:i') }}</p>
                </div>
                @if (! $notification->read_at)
                    <form method="POST" action="{{ route('notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="rounded-lg px-3 py-2 text-xs font-semibold text-primary-700/75 hover:bg-primary-500/8 dark:text-primary-100/70 dark:hover:bg-white/10">{{ __('app.mark_read') }}</button>
                    </form>
                @endif
            </article>
        @empty
            <x-empty-state icon="inbox" title="Aucune notification" description="Les nouvelles activités apparaîtront ici." />
        @endforelse

        {{ $notifications->links('vendor.pagination.premium') }}
    </section>
@endsection
