@extends('layouts.app')

@section('title', 'Nouvel utilisateur — Bureau d\'Ordre')
@section('page-title', __('app.user_creation_title'))

@section('content')

    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary-700/60 dark:text-primary-100/50 hover:text-primary-700 dark:hover:text-primary-50 transition-colors duration-150">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                <path d="m15 18-6-6 6-6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Retour à la liste
        </a>
    </div>

    <div class="max-w-2xl rounded-2xl bg-white/60 dark:bg-white/[0.04] backdrop-blur-2xl border border-white/60 dark:border-white/10 p-6 sm:p-8">
        <h2 class="font-display font-semibold text-lg text-primary-800 dark:text-primary-50 mb-6">
            Informations de l'utilisateur
        </h2>

        <form method="POST" action="{{ route('users.store') }}">
            @include('users._form')

            <div class="mt-8 flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-primary-700/60 dark:text-primary-100/50 hover:text-primary-700 dark:hover:text-primary-50 transition-colors duration-150">
                    Annuler
                </a>
                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-br from-primary-600 to-primary-700 text-white shadow-[0_8px_20px_-6px_rgba(11,61,145,0.45)] hover:shadow-[0_10px_24px_-6px_rgba(11,61,145,0.55)] transition-shadow duration-200 ease-fluent"
                >
                    Créer l'utilisateur
                </button>
            </div>
        </form>
    </div>

@endsection
