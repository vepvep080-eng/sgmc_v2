@props(['icon', 'label', 'value', 'accent' => 'primary'])

@php
    $accents = [
        'primary' => 'from-primary-500 to-primary-600 text-primary-50',
        'gold' => 'from-gold-500 to-gold-600 text-white',
        'secondary' => 'from-secondary-500 to-secondary-600 text-secondary-50',
        'red' => 'from-red-500 to-red-600 text-red-50',
    ];
    $accentClasses = $accents[$accent] ?? $accents['primary'];
@endphp

<div class="group relative rounded-[22px] bg-white/55 dark:bg-white/[0.05] backdrop-blur-xl border border-white/70 dark:border-white/10 shadow-glass hover:shadow-glass-lg dark:hover:shadow-glass-lg-dark transition-shadow duration-300 ease-fluent px-6 py-5 overflow-hidden">
    <div class="absolute top-0 left-6 w-12 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500/70 to-transparent"></div>

    <div class="flex items-center gap-4">
        <div class="shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br {{ $accentClasses }} flex items-center justify-center shadow-md transition-transform duration-300 ease-fluent group-hover:scale-110 group-hover:-rotate-3">
            <x-icon :name="$icon" class="w-6 h-6" />
        </div>
        <div>
            <p class="text-3xl font-display font-bold text-primary-800 dark:text-primary-50 tabular-nums leading-none">{{ $value }}</p>
            <p class="mt-1.5 text-xs font-medium tracking-wide text-primary-700/60 dark:text-primary-100/55">{{ $label }}</p>
        </div>
    </div>
</div>
