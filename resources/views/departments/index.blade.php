@extends('layouts.app')
@section('title', __('app.departments'))
@section('page-title', __('app.departments'))
@section('content')
<div class="flex justify-end mb-5">@can('create', \App\Models\Department::class)<a href="{{ route('departments.create') }}" class="rounded-xl bg-primary-500 px-4 py-2 text-white">{{ __('app.add_department') }}</a>@endcan</div>
<div class="overflow-x-auto rounded-xl bg-white dark:bg-white/5"><table class="w-full text-sm"><thead><tr><th class="p-3 text-start">{{ __('app.department') }}</th><th class="p-3 text-start">{{ __('app.manager') }}</th><th class="p-3 text-start">{{ __('app.services') }}</th><th class="p-3"></th></tr></thead><tbody>@forelse($departments as $department)<tr class="border-t"><td class="p-3">{{ $department->name }}<div class="text-xs text-gray-500">{{ $department->code }}</div></td><td class="p-3">{{ $department->manager?->name ?? '—' }}</td><td class="p-3">{{ $department->services_count }}</td><td class="p-3">@can('update',$department)<a href="{{ route('departments.edit',$department) }}">{{ __('app.edit') }}</a>@endcan</td></tr>@empty<tr><td colspan="4" class="p-6 text-center">{{ __('app.no_departments') }}</td></tr>@endforelse</tbody></table></div>{{ $departments->links() }}
@endsection
