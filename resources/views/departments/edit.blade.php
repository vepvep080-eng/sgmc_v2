@extends('layouts.app')
@section('title', __('app.edit_department'))
@section('page-title', __('app.edit_department'))
@section('content')<form method="POST" action="{{ route('departments.update',$department) }}" class="rounded-xl bg-white p-6 dark:bg-white/5">@csrf @method('PUT') @include('departments._form')<div class="mt-6"><button class="rounded-xl bg-primary-500 px-4 py-2 text-white">{{ __('app.save') }}</button></div></form>@endsection
