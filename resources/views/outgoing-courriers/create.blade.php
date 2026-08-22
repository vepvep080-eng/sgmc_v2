@extends('layouts.app')

@section('title', "Nouveau courrier départ — Bureau d'Ordre")
@section('page-title', 'Nouveau courrier départ')

@section('content')
    @php($courrier = new \App\Models\OutgoingCourrier())
    <a href="{{ route('courriers-depart.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm text-primary-700/60 hover:text-primary-700 dark:text-primary-100/50 dark:hover:text-primary-100"><x-icon name="arrow-left" class="h-4 w-4" />Retour à la liste</a>
    <div class="rounded-[24px] border border-white/70 bg-white/55 px-6 py-8 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] sm:px-9">
        <h2 class="font-display text-xl font-semibold text-primary-800 dark:text-primary-50">Enregistrer un courrier départ</h2>
        <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">Le numéro est généré automatiquement lors de l'enregistrement.</p>
        <form method="POST" action="{{ route('courriers-depart.store') }}" enctype="multipart/form-data" class="mt-7">@csrf @include('outgoing-courriers._form')
            <div class="mt-8 flex justify-end gap-3"><a href="{{ route('courriers-depart.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-primary-700/75 dark:text-primary-100/70">Annuler</a><button class="rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 px-6 py-2.5 text-sm font-semibold text-white">Enregistrer le courrier</button></div>
        </form>
    </div>
@endsection
