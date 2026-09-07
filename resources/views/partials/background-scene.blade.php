{{-- Ambient background: zellige pattern, glow blobs, floating particles. --}}
{{-- Shared between the splash screen and the login page for visual continuity. --}}

{{-- Ambient ornamental pattern — two very subtle counter-drifting layers --}}
<div class="pointer-events-none absolute inset-0 opacity-[0.045] dark:opacity-[0.06] mix-blend-multiply dark:mix-blend-screen overflow-hidden" aria-hidden="true">
    <svg class="absolute -inset-1/3 w-[166%] h-[166%] animate-patternDrift motion-reduce:animate-none" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <pattern id="zellige" width="64" height="64" patternUnits="userSpaceOnUse">
                <path d="M32 0 L64 32 L32 64 L0 32 Z" fill="none" stroke="#0D4A36" stroke-width="1" />
                <circle cx="32" cy="32" r="10" fill="none" stroke="#C99738" stroke-width="1" />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#zellige)" />
    </svg>
    <svg class="absolute -inset-1/3 w-[166%] h-[166%] animate-patternDriftReverse motion-reduce:animate-none opacity-60" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <pattern id="zellige2" width="96" height="96" patternUnits="userSpaceOnUse">
                <path d="M48 4 L92 48 L48 92 L4 48 Z" fill="none" stroke="#2E7A58" stroke-width="1" />
            </pattern>
        </defs>
        <rect width="100%" height="100%" fill="url(#zellige2)" />
    </svg>
</div>

{{-- Ambient glow blobs --}}
<div class="pointer-events-none absolute -top-24 -left-24 w-96 h-96 rounded-full bg-primary-300/20 dark:bg-primary-500/10 blur-3xl animate-blobDrift motion-reduce:animate-none" aria-hidden="true"></div>
<div class="pointer-events-none absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-secondary-300/20 dark:bg-secondary-500/10 blur-3xl animate-blobDrift motion-reduce:animate-none" style="animation-delay: -7s;" aria-hidden="true"></div>
<div class="pointer-events-none absolute top-1/3 right-1/4 w-72 h-72 rounded-full bg-gold-300/10 dark:bg-gold-500/10 blur-3xl animate-blobDrift motion-reduce:animate-none" style="animation-delay: -14s;" aria-hidden="true"></div>

{{-- Soft focused glow directly behind the card --}}
<div class="pointer-events-none absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[36rem] h-[36rem] rounded-full bg-gradient-to-br from-white/60 via-primary-200/15 to-transparent dark:from-white/5 dark:via-primary-500/5 blur-3xl" aria-hidden="true"></div>

{{-- Floating particles (populated by resources/js/app.js) --}}
<div id="particle-field" class="particle-field" aria-hidden="true"></div>
