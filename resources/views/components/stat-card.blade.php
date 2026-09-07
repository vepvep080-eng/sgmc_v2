@props(['icon', 'label', 'value', 'accent' => 'primary', 'trend' => null, 'unit' => null, 'solid' => false])

@php
    $iconTints = [
        'primary' => 'bg-primary-100 text-primary-600',
        'gold' => 'bg-gold-200 text-gold-600',
        'secondary' => 'bg-secondary-100 text-secondary-600',
        'red' => 'bg-error/10 text-error',
    ];
    $tintClasses = $iconTints[$accent] ?? $iconTints['primary'];
@endphp

<div class="group relative flex flex-col justify-between rounded-[22px] px-6 py-5 overflow-hidden {{ $solid ? 'bg-primary-600 text-primary-50 border border-gold-500/15 shadow-glass' : 'bg-white/55 dark:bg-white/[0.05] backdrop-blur-xl border border-white/70 dark:border-white/10 shadow-glass' }} transition-shadow duration-300 ease-fluent hover:-translate-y-0.5">
    <div class="absolute top-0 left-6 w-12 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-400 to-transparent"></div>

    <div class="flex items-center justify-between">
        <span class="text-[13px] font-medium tracking-wide {{ $solid ? 'text-primary-200/75' : 'text-primary-700/60 dark:text-primary-100/55' }}">
            {{ $label }}
        </span>

        <div class="shrink-0 w-12 h-12 rounded-full {{ $solid ? 'bg-primary-500 text-gold-400' : $tintClasses }} flex items-center justify-center transition-transform duration-300 ease-fluent group-hover:scale-105">
            <x-icon :name="$icon" class="w-6 h-6" />
        </div>
    </div>

    <div class="mt-4 flex flex-col">
        <p class="text-3xl font-display font-bold tabular-nums leading-none {{ $solid ? 'text-primary-50' : 'text-primary-800 dark:text-primary-50' }}">
            {{ $value }}
            @if ($unit)
                <span class="text-sm font-medium {{ $solid ? 'text-primary-200/80' : 'text-primary-700/60 dark:text-primary-100/45' }}">{{ $unit }}</span>
            @endif
        </p>

        @if ($trend)
            <p class="mt-2 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $solid ? 'bg-primary-50 text-primary-700' : 'bg-primary-50 text-primary-700 dark:bg-white/10 dark:text-primary-200/70' }}">
                <span class="h-1.5 w-1.5 rounded-full bg-gold-500" aria-hidden="true"></span>
                {{ $trend }}
            </p>
        @endif
    </div>
</div>
