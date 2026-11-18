@foreach ($socials as $social)
    @continue(blank($social['url'] ?? null))
    <a
        href="{{ $social['url'] }}"
        aria-label="{{ $social['label'] }}"
        title="{{ $social['label'] }}"
        data-social-link="{{ $social['platform'] }}"
        @class([
            'inline-flex size-12 shrink-0 items-center justify-center rounded-full border transition-colors focus-visible:outline-2 focus-visible:outline-offset-4',
            'border-line text-accent hover:border-accent hover:bg-accent hover:text-white focus-visible:outline-accent' => $variant === 'light',
            'border-white/25 text-slate-300 hover:border-white hover:bg-white/10 hover:text-white focus-visible:outline-white' => $variant === 'dark',
        ])
        target="_blank"
        rel="noopener noreferrer"
    >
        @include('portfolio.partials.social-icon', ['platform' => $social['platform']])
        <span class="sr-only">{{ $social['label'] }}</span>
    </a>
@endforeach
