@props(['variant' => 'default'])

@php
    $variants = [
        'default' => 'bg-primary-500/10 text-primary-700 dark:bg-primary-500/15 dark:text-primary-100 border-primary-500/15 dark:border-primary-400/20',
        'gold' => 'bg-gold-500/12 text-gold-700 dark:bg-gold-500/15 dark:text-gold-200 border-gold-500/25',
        'green' => 'bg-secondary-500/10 text-secondary-700 dark:bg-secondary-500/15 dark:text-secondary-200 border-secondary-500/20',
        'amber' => 'bg-amber-500/10 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300 border-amber-500/25',
        'red' => 'bg-red-500/10 text-red-700 dark:bg-red-500/15 dark:text-red-300 border-red-500/25',
        'gray' => 'bg-slate-500/10 text-slate-600 dark:bg-white/10 dark:text-primary-100/60 border-slate-500/15 dark:border-white/10',
    ];

    $classes = $variants[$variant] ?? $variants['default'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full border text-[11px] font-semibold tracking-wide whitespace-nowrap {$classes}"]) }}>
    {{ $slot }}
</span>
