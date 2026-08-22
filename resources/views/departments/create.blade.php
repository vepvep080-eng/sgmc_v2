@extends('layouts.app')
@section('title', __('app.add_department'))
@section('page-title', __('app.add_department'))
@section('content')<form method="POST" action="{{ route('departments.store') }}" class="rounded-xl bg-white p-6 dark:bg-white/5">@csrf @include('departments._form',['department'=>new \App\Models\Department])<div class="mt-6"><button class="rounded-xl bg-primary-500 px-4 py-2 text-white">{{ __('app.save') }}</button></div></form>@endsection
