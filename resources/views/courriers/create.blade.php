@extends('layouts.app')

@section('title', "Nouveau courrier — Bureau d'Ordre")
@section('page-title', __('app.new_courrier_title'))

@section('content')
    @php($courrier = new \App\Models\Courrier())

    <a href="{{ route('courriers.index') }}" class="inline-flex items-center gap-1.5 text-sm text-primary-700/60 dark:text-primary-100/50 hover:text-primary-700 dark:hover:text-primary-100 transition-colors duration-200 ease-fluent mb-5">
        <x-icon name="arrow-left" class="w-4 h-4" />
        {{ __('app.back_to_list') }}
    </a>

    <div class="relative rounded-[24px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark px-6 sm:px-9 py-8 opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

        <h2 class="font-display font-semibold text-xl sm:text-2xl text-primary-800 dark:text-primary-50">
            {{ __('app.register_incoming_title') }}
        </h2>
        <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">
            {{ __('app.register_incoming_description') }}
        </p>

        <form method="POST" action="{{ route('courriers.store') }}" enctype="multipart/form-data" class="mt-7">
            @csrf

            @include('courriers._form', ['courrier' => $courrier])

            <div class="mt-8 flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                <a href="{{ route('courriers.index') }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold text-primary-700 dark:text-primary-100 bg-white/60 dark:bg-white/[0.06] border border-primary-500/15 dark:border-white/10 shadow-glass hover:bg-white/80 dark:hover:bg-white/[0.1] transition-all">
                    {{ __('app.cancel') }}
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-xl px-6 py-2.5 text-sm font-bold text-white bg-primary shadow-md transition-all hover:bg-primary-600 hover:scale-[1.02] active:scale-[0.99]"
                >
                    <x-icon name="check" class="w-4 h-4" />
                    {{ __('app.save_courrier') }}
                </button>
            </div>
        </form>
    </div>
@endsection
