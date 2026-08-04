@props(['name', 'class' => 'w-5 h-5'])

{{--
    Hand-drawn outline icons (Heroicons-style, 1.6 stroke) — no external
    icon package required, consistent with the rest of the app's SVGs.
    Usage: <x-icon name="inbox" class="w-5 h-5" />
--}}
@php
    $paths = [
        'home' => '<path d="M3 11.5 12 4l9 7.5" /><path d="M5.5 9.5V19a1 1 0 0 0 1 1H9a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h2.5a1 1 0 0 0 1-1V9.5" />',
        'inbox' => '<path d="M3 12h4.5l1.5 3h6l1.5-3H21" /><path d="M5 5.5h14l2 6.5V18a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18v-6l2-6.5Z" />',
        'logout' => '<path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4H18a1.5 1.5 0 0 1 1.5 1.5v13A1.5 1.5 0 0 1 18 20h-7.5A1.5 1.5 0 0 1 9 18.5V17" /><path d="M3 12h11.5" /><path d="M11 8.5 14.5 12 11 15.5" />',
        'menu' => '<path d="M4 6h16" /><path d="M4 12h16" /><path d="M4 18h16" />',
        'x' => '<path d="M6 6l12 12" /><path d="M18 6 6 18" />',
        'search' => '<circle cx="10.5" cy="10.5" r="6.5" /><path d="M20 20l-4.7-4.7" />',
        'filter' => '<path d="M4 5h16" /><path d="M7 12h10" /><path d="M10.5 19h3" />',
        'sort' => '<path d="M7 4v16" /><path d="M4 7l3-3 3 3" /><path d="M17 20V4" /><path d="M14 17l3 3 3-3" />',
        'plus' => '<circle cx="12" cy="12" r="9" /><path d="M12 8v8" /><path d="M8 12h8" />',
        'plus-simple' => '<path d="M12 5v14" /><path d="M5 12h14" />',
        'pencil' => '<path d="M4 20h4l10.5-10.5a2 2 0 0 0 0-2.8l-1.2-1.2a2 2 0 0 0-2.8 0L4 16v4Z" /><path d="M13.5 6.5l4 4" />',
        'trash' => '<path d="M5 7h14" /><path d="M9 7V5.5A1.5 1.5 0 0 1 10.5 4h3A1.5 1.5 0 0 1 15 5.5V7" /><path d="M7 7l1 12.5A1.5 1.5 0 0 0 9.5 21h5a1.5 1.5 0 0 0 1.5-1.5L17 7" /><path d="M10 11v6" /><path d="M14 11v6" />',
        'eye' => '<path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z" /><circle cx="12" cy="12" r="3" />',
        'paperclip' => '<path d="M17.5 8.5 10 16a3 3 0 1 1-4.2-4.2L13.5 4a2 2 0 1 1 2.8 2.8L9 14.1a1 1 0 1 1-1.4-1.4L14 6.4" />',
        'chevron-left' => '<path d="M14.5 5.5 8 12l6.5 6.5" />',
        'chevron-right' => '<path d="M9.5 5.5 16 12l-6.5 6.5" />',
        'chevron-down' => '<path d="M5.5 9.5 12 16l6.5-6.5" />',
        'check' => '<path d="M4 12.5l5 5L20 6" />',
        'check-circle' => '<circle cx="12" cy="12" r="9" /><path d="M8 12.5l2.5 2.5L16 9" />',
        'exclamation' => '<circle cx="12" cy="12" r="9" /><path d="M12 8v5" /><path d="M12 16.2v.1" />',
        'exclamation-triangle' => '<path d="M12 4 3 20h18L12 4Z" /><path d="M12 10v4" /><path d="M12 17v.1" />',
        'clock' => '<circle cx="12" cy="12" r="9" /><path d="M12 7v5.5l3.5 2" />',
        'download' => '<path d="M12 4v11" /><path d="M8 11.5 12 15.5 16 11.5" /><path d="M5 18.5h14" />',
        'arrow-left' => '<path d="M19 12H5" /><path d="M11 6l-6 6 6 6" />',
        'refresh' => '<path d="M4 12a8 8 0 0 1 14-5.3L20 8" /><path d="M20 4v4h-4" /><path d="M20 12a8 8 0 0 1-14 5.3L4 16" /><path d="M4 20v-4h4" />',
        'building' => '<path d="M5 21V5.5A1.5 1.5 0 0 1 6.5 4h11A1.5 1.5 0 0 1 19 5.5V21" /><path d="M3 21h18" /><path d="M9 8h1M14 8h1M9 12h1M14 12h1M9 16h1M14 16h1" />',
        'archive' => '<path d="M4 7h16" /><path d="M5 7l.5 12.5A1.5 1.5 0 0 0 7 21h10a1.5 1.5 0 0 0 1.5-1.5L19 7" /><path d="M4 4h16v3H4Z" /><path d="M10 11.5h4" />',
    ];

    $path = $paths[$name] ?? $paths['exclamation'];
@endphp

<svg viewBox="0 0 24 24" class="{{ $class }}" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    {!! $path !!}
</svg>
