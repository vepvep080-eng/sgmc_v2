{{-- Top corner emblems — shared between the splash screen and the login page. --}}
<header class="relative z-10 w-full max-w-5xl flex items-start justify-between opacity-0 animate-fadeInUp" style="animation-delay: 0.1s;">

    {{-- Moroccan Coat of Arms --}}
    <div class="flex flex-col items-center gap-2.5 group">
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/50 dark:bg-white/[0.06] backdrop-blur-md border border-white/70 dark:border-white/10 shadow-chip flex items-center justify-center overflow-hidden transition-all duration-300 ease-fluent group-hover:scale-[1.04] group-hover:border-gold-300/60">
            <img
                src="{{ asset('assets/armoiries-royaume.png') }}"
                alt="Armoiries du Royaume du Maroc"
                class="w-full h-full object-contain p-2 transition-transform duration-300 ease-fluent group-hover:scale-105"
            />
        </div>
        <span class="text-[10px] sm:text-xs font-medium tracking-wide text-primary-700/70 dark:text-primary-100/60 text-center max-w-[6rem]">
            Royaume du Maroc
        </span>
    </div>

    {{-- Commune Logo --}}
    <div class="flex flex-col items-center gap-2.5 group">
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/50 dark:bg-white/[0.06] backdrop-blur-md border border-white/70 dark:border-white/10 shadow-chip flex items-center justify-center overflow-hidden transition-all duration-300 ease-fluent group-hover:scale-[1.04] group-hover:border-gold-300/60">
            <img
                src="{{ asset('assets/commune-jerada.jpg') }}"
                alt="Commune de Jerada"
                class="w-full h-full object-cover transition-transform duration-300 ease-fluent group-hover:scale-105"
            />
        </div>
        <span class="text-[10px] sm:text-xs font-medium tracking-wide text-secondary-700/70 dark:text-secondary-200/60 text-center max-w-[6rem]">
            Commune de Jerada
        </span>
    </div>
</header>
