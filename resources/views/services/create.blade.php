@extends('layouts.app')

@section('title', "Nouveau service — Bureau d'Ordre")
@section('page-title', 'Nouveau service')

@section('content')
    <div class="max-w-xl opacity-0 animate-fadeInUp" style="animation-delay: 0.05s;">
        <div class="relative rounded-[24px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-2xl border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark overflow-hidden p-6 sm:p-8">
            <div class="absolute top-0 left-8 w-16 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

            <h2 class="font-display font-semibold text-xl text-primary-800 dark:text-primary-50 mb-6">
                Nouveau service
            </h2>

            <form method="POST" action="{{ route('services.store') }}" class="flex flex-col gap-5">
                @csrf
                @include('services._form')

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 text-sm font-semibold text-white bg-gradient-to-r from-primary-500 to-secondary-500 rounded-xl px-5 py-2.5 shadow-[0_10px_24px_-8px_rgba(11,61,145,0.45)] hover:scale-[1.02] transition-transform duration-200 ease-fluent">
                        Créer le service
                    </button>
                    <a href="{{ route('services.index') }}" class="text-sm font-medium text-primary-700/60 dark:text-primary-100/50 hover:text-primary-800 dark:hover:text-primary-50">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
