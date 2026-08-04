{{--
    One shared modal for the whole page instead of one per row — trigger
    buttons set the target URL via data attributes (see app.js
    `initConfirmModal()`), which fills in this modal's form action
    before showing it.
--}}
<div
    id="confirm-delete-modal"
    class="hidden fixed inset-0 z-50 items-center justify-center px-4"
    role="dialog"
    aria-modal="true"
    aria-labelledby="confirm-delete-title"
>
    <div data-modal-backdrop class="absolute inset-0 bg-midnight-950/60 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-sm rounded-[26px] bg-white/90 dark:bg-midnight-900/90 backdrop-blur-2xl border border-white/70 dark:border-white/10 shadow-glass-lg dark:shadow-glass-lg-dark px-7 py-8 text-center opacity-0 animate-scaleIn" style="animation-duration: 0.35s;">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-20 h-[2px] rounded-full bg-gradient-to-r from-transparent via-gold-500 to-transparent"></div>

        <div class="mx-auto w-14 h-14 rounded-full bg-red-500/10 text-red-600 dark:text-red-300 flex items-center justify-center">
            <x-icon name="trash" class="w-6 h-6" />
        </div>

        <h2 id="confirm-delete-title" class="mt-4 font-display font-semibold text-xl text-primary-800 dark:text-primary-50">
            Supprimer ce courrier ?
        </h2>
        <p data-modal-message class="mt-2 text-sm text-primary-700/70 dark:text-primary-100/60">
            Cette action est définitive et ne peut pas être annulée.
        </p>

        <form data-confirm-delete-form method="POST" class="mt-6 flex items-center gap-3">
            @csrf
            @method('DELETE')
            <button
                type="button"
                data-modal-close
                class="flex-1 rounded-xl py-2.5 text-sm font-semibold text-primary-700/80 dark:text-primary-100/75 bg-primary-500/8 dark:bg-white/5 border border-primary-500/15 dark:border-white/10 hover:bg-primary-500/12 dark:hover:bg-white/10 transition-colors duration-200 ease-fluent"
            >
                Annuler
            </button>
            <button
                type="submit"
                class="flex-1 rounded-xl py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-red-600 to-red-500 shadow-[0_10px_24px_-8px_rgba(220,38,38,0.5)] hover:scale-[1.02] active:scale-[0.99] transition-all duration-200 ease-fluent"
            >
                Supprimer
            </button>
        </form>
    </div>
</div>
