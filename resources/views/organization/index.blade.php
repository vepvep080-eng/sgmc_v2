@extends('layouts.app')
@section('title', __('app.organization_structure'))
@section('page-title', __('app.organization_structure'))
@section('content')
<div class="space-y-5 text-center">
 <div class="rounded-xl border bg-white p-4 dark:bg-white/5">رئيسة المجلس الجماعي</div>
 <div class="text-primary-500">↓</div><div class="rounded-xl border bg-white p-4 dark:bg-white/5">مدير المصالح</div>
 <div class="grid gap-5 lg:grid-cols-2">@forelse($departments as $department)<section class="rounded-xl border bg-white p-5 text-start dark:bg-white/5"><h2 class="font-semibold">{{ $department->name }}</h2><p class="text-xs text-gray-500">{{ __('app.manager') }}: {{ $department->manager?->name ?? '—' }}</p><ul class="mt-4 space-y-2">@forelse($department->services as $service)<li class="rounded-lg bg-gray-50 p-3 dark:bg-white/5">{{ $service->name }} <span class="text-xs text-gray-500">{{ $service->manager?->name ? '— '.$service->manager->name : '' }}</span></li>@empty<li class="text-sm text-gray-500">{{ __('app.no_services') }}</li>@endforelse</ul></section>@empty<div class="col-span-full rounded-xl border p-6">{{ __('app.no_departments') }}</div>@endforelse</div>
</div>
@endsection
