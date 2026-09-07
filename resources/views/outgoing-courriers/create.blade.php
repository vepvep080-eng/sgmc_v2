@extends('layouts.app')

@section('title', __('app.outgoing_new_title'))
@section('page-title', __('app.outgoing_page_title'))

@section('content')
    @php($courrier = new \App\Models\OutgoingCourrier())
    <a href="{{ route('courriers-depart.index') }}" class="mb-5 inline-flex items-center gap-1.5 text-sm text-primary-700/60 hover:text-primary-700 dark:text-primary-100/50 dark:hover:text-primary-100"><x-icon name="arrow-left" class="h-4 w-4" />{{ __('app.back_to_list') }}</a>
    <div class="rounded-[24px] border border-white/70 bg-white/55 px-6 py-8 shadow-glass-lg backdrop-blur-2xl dark:border-white/10 dark:bg-white/[0.05] sm:px-9">
        <h2 class="font-display text-xl font-semibold text-primary-800 dark:text-primary-50">{{ __('app.outgoing_register_title') }}</h2>
        <p class="mt-1 text-sm text-primary-700/60 dark:text-primary-100/50">{{ __('app.outgoing_number_note') }}</p>
        <form method="POST" action="{{ route('courriers-depart.store') }}" enctype="multipart/form-data" class="mt-7">@csrf @include('outgoing-courriers._form')
            <div class="mt-8 flex justify-end gap-3"><a href="{{ route('courriers-depart.index') }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-primary-700/75 dark:text-primary-100/70">{{ __('app.cancel') }}</a><button class="rounded-xl bg-gradient-to-r from-primary-500 to-secondary-500 px-6 py-2.5 text-sm font-semibold text-white">{{ __('app.save_courrier') }}</button></div>
        </form>
    </div>
@endsection