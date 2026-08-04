@if (session('success') || session('error'))
    <div
        data-toast
        data-auto-dismiss="4000"
        class="fixed top-5 right-5 z-50 max-w-sm w-[calc(100%-2.5rem)] sm:w-full opacity-0 animate-fadeInUp"
        role="status"
        aria-live="polite"
    >
        @if (session('success'))
            <div class="flex items-start gap-3 rounded-2xl border border-secondary-500/25 bg-white/80 dark:bg-midnight-900/85 backdrop-blur-xl shadow-glass-lg px-4 py-3.5">
                <span class="mt-0.5 shrink-0 w-7 h-7 rounded-full bg-secondary-500/15 text-secondary-600 dark:text-secondary-300 flex items-center justify-center">
                    <x-icon name="check-circle" class="w-4 h-4" />
                </span>
                <p class="text-sm text-primary-800 dark:text-primary-50 leading-snug pt-0.5">{{ session('success') }}</p>
                <button type="button" data-dismiss-toast aria-label="Fermer" class="ml-auto shrink-0 text-primary-500/40 hover:text-primary-700 dark:text-primary-100/40 dark:hover:text-primary-100 transition-colors duration-200">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-start gap-3 rounded-2xl border border-red-500/25 bg-white/80 dark:bg-midnight-900/85 backdrop-blur-xl shadow-glass-lg px-4 py-3.5 mt-2">
                <span class="mt-0.5 shrink-0 w-7 h-7 rounded-full bg-red-500/15 text-red-600 dark:text-red-300 flex items-center justify-center">
                    <x-icon name="exclamation-triangle" class="w-4 h-4" />
                </span>
                <p class="text-sm text-primary-800 dark:text-primary-50 leading-snug pt-0.5">{{ session('error') }}</p>
                <button type="button" data-dismiss-toast aria-label="Fermer" class="ml-auto shrink-0 text-primary-500/40 hover:text-primary-700 dark:text-primary-100/40 dark:hover:text-primary-100 transition-colors duration-200">
                    <x-icon name="x" class="w-4 h-4" />
                </button>
            </div>
        @endif
    </div>
@endif
