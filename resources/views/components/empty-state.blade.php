@props(['icon' => 'inbox', 'title', 'description' => null])

<div class="flex flex-col items-center justify-center text-center px-6 py-16 sm:py-20">
    <div class="relative w-16 h-16 mb-5">
        <div class="absolute inset-0 rounded-full bg-gold-400/15 blur-xl" aria-hidden="true"></div>
        <div class="relative w-16 h-16 rounded-2xl bg-primary-500/8 dark:bg-white/5 border border-primary-500/15 dark:border-white/10 flex items-center justify-center text-primary-500/50 dark:text-primary-100/40">
            <x-icon :name="$icon" class="w-7 h-7" />
        </div>
    </div>
    <p class="font-display font-semibold text-lg text-primary-700 dark:text-primary-100">{{ $title }}</p>
    @if ($description)
        <p class="mt-1.5 text-sm text-primary-700/60 dark:text-primary-100/50 max-w-sm">{{ $description }}</p>
    @endif
    @isset($action)
        <div class="mt-6">{{ $action }}</div>
    @endisset
</div>
