@extends('layouts.app')

@section('title', "Modifier {$courrier->numero} — Bureau d'Ordre")
@section('page-title', __('app.edit_courrier_title'))

@section('content')
    <a href="{{ route('courriers.show', $courrier) }}" class="inline-flex items-center gap-1.5 text-sm text-primary-700/60 dark:text-primary-100/50 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200 ease-fluent mb-5">
        <x-icon name="arrow-left" class="w-4 h-4" />
        {{ __('app.back_to_detail') }}
    </a>

    <div class="relative rounded-[24px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark px-6 sm:px-9 py-8 opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

        <div class="flex items-center gap-3">
            <h2 class="font-display font-semibold text-xl sm:text-2xl text-primary-800 dark:text-primary-50">
                {{ __('app.edit_courrier_title') }}
            </h2>
            <x-badge variant="gold">{{ $courrier->numero }}</x-badge>
        </div>

        <form method="POST" action="{{ route('courriers.update', $courrier) }}" enctype="multipart/form-data" class="mt-7">
            @csrf
            @method('PUT')

            @include('courriers._form', ['courrier' => $courrier])

            <div class="mt-8 flex items-center justify-end gap-3">
                <a href="{{ route('courriers.show', $courrier) }}" class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-primary-700 dark:text-primary-100 bg-white/60 dark:bg-white/[0.06] border border-primary-500/15 dark:border-white/10 shadow-glass hover:bg-white/80 dark:hover:bg-white/[0.1] transition-all duration-200 ease-fluent">
                    {{ __('app.cancel') }}
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary-500 via-primary-500 to-secondary-500 shadow-[0_10px_28px_-8px_rgba(11,61,145,0.5)] transition-all duration-300 ease-fluent hover:shadow-gold-glow hover:scale-[1.02] active:scale-[0.99]"
                >
                    <x-icon name="check" class="w-4 h-4" />
                    {{ __('app.save_changes') }}
                </button>
            </div>
        </form>
    </div>
@endsection
