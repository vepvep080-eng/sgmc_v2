@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination" class="flex items-center justify-between gap-4 px-6 sm:px-8 py-4 border-t border-white/50 dark:border-white/10">

        <p class="text-xs text-primary-700/55 dark:text-primary-100/45">
            {{ __('Affichage de') }} <span class="font-semibold text-primary-700 dark:text-primary-100">{{ $paginator->firstItem() }}</span>
            {{ __('à') }} <span class="font-semibold text-primary-700 dark:text-primary-100">{{ $paginator->lastItem() }}</span>
            {{ __('sur') }} <span class="font-semibold text-primary-700 dark:text-primary-100">{{ $paginator->total() }}</span>
        </p>

        <div class="flex items-center gap-1.5">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="w-9 h-9 flex items-center justify-center rounded-lg text-primary-700/25 dark:text-primary-100/20 cursor-not-allowed">
                    <x-icon name="chevron-left" class="w-4 h-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-lg text-primary-700/70 dark:text-primary-100/60 hover:bg-primary-500/8 dark:hover:bg-white/8 transition-colors duration-200 ease-fluent">
                    <x-icon name="chevron-left" class="w-4 h-4" />
                </a>
            @endif

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="w-9 h-9 flex items-center justify-center text-xs text-primary-700/40 dark:text-primary-100/30">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="w-9 h-9 flex items-center justify-center rounded-lg text-xs font-semibold text-white bg-gradient-to-br from-primary-500 to-secondary-500 shadow-[0_4px_14px_-4px_rgba(11,61,145,0.5)]">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-lg text-xs font-medium text-primary-700/70 dark:text-primary-100/60 hover:bg-primary-500/8 dark:hover:bg-white/8 transition-colors duration-200 ease-fluent">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-lg text-primary-700/70 dark:text-primary-100/60 hover:bg-primary-500/8 dark:hover:bg-white/8 transition-colors duration-200 ease-fluent">
                    <x-icon name="chevron-right" class="w-4 h-4" />
                </a>
            @else
                <span class="w-9 h-9 flex items-center justify-center rounded-lg text-primary-700/25 dark:text-primary-100/20 cursor-not-allowed">
                    <x-icon name="chevron-right" class="w-4 h-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
