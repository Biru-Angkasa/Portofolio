<svg data-social-icon="{{ $platform }}" viewBox="0 0 24 24" class="size-5" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
    @switch($platform)
        @case('instagram')
            <rect x="3" y="3" width="18" height="18" rx="5"/>
            <circle cx="12" cy="12" r="4"/>
            <circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/>
            @break
        @case('whatsapp')
            <path d="M20.5 11.7a8.5 8.5 0 0 1-12.7 7.4L3 20.5l1.4-4.7a8.5 8.5 0 1 1 16.1-4.1Z"/>
            <path d="m9 7.5-1 1.2c-.3.4-.2 1 .1 1.6a11 11 0 0 0 5.6 5.6c.6.3 1.2.4 1.6.1l1.2-1-2.5-1.6-1.1 1a8.7 8.7 0 0 1-3.3-3.3l1-1.1L9 7.5Z"/>
            @break
        @case('facebook')
            <path d="M14.5 22v-8.5h2.9l.5-3.5h-3.4V7.8c0-1 .3-1.7 1.8-1.7h1.8V3.2c-.3 0-1.4-.2-2.7-.2-2.7 0-4.5 1.6-4.5 4.6V10H8v3.5h2.9V22h3.6Z" fill="currentColor" stroke="none"/>
            @break
        @case('linkedin')
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="7.5" cy="7.5" r="1" fill="currentColor" stroke="none"/>
            <path d="M7.5 11v6M11 17v-6M11 13.5a2.5 2.5 0 0 1 5 0V17"/>
            @break
        @case('github')
            <path d="M9 19c-4.3 1.3-4.3-2.2-6-2.7M15 22v-3.9c0-1.1-.4-1.9-1-2.4 3.3-.4 6.7-1.6 6.7-7.2a5.6 5.6 0 0 0-1.5-3.9 5.1 5.1 0 0 0-.1-3.8s-1.2-.4-4 1.5a13.8 13.8 0 0 0-7.2 0c-2.8-1.9-4-1.5-4-1.5a5.1 5.1 0 0 0-.1 3.8 5.6 5.6 0 0 0-1.5 3.9c0 5.6 3.4 6.8 6.7 7.2-.6.5-1 1.3-1 2.4V22"/>
            @break
        @case('youtube')
            <rect x="2" y="5" width="20" height="14" rx="4"/>
            <path d="m10 9 6 3-6 3V9Z" fill="currentColor" stroke="none"/>
            @break
        @case('x')
            <path d="M4 3h4l12 18h-4L4 3ZM20 3l-7 8M4 21l7-8"/>
            @break
        @case('tiktok')
            <path d="M14 3v13.5a4.5 4.5 0 1 1-4.5-4.5M14 3c.5 3 2.5 5 6 5V5c-2.3-.3-3.2-1-4-2h-2Z"/>
            @break
        @case('telegram')
            <path d="m21 3-4 18-6-5-4 3 .7-6L3 11l18-8Z"/>
            <path d="m7.7 13 9.8-6.5L11 16"/>
            @break
        @default
            <circle cx="12" cy="12" r="9"/>
            <ellipse cx="12" cy="12" rx="4" ry="9"/>
            <path d="M3 12h18M5 6.5h14M5 17.5h14"/>
    @endswitch
</svg>
