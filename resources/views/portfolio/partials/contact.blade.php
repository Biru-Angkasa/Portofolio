<section id="kontak" class="interactive-section" data-network-background="ripple">
    <div class="mx-auto flex w-full max-w-7xl flex-col items-center gap-6 px-5 py-16 text-center md:px-8 md:py-24">
        <p class="section-label">Kontak</p><h2 class="section-title">{{ $portfolio['contact_title'] }}</h2>
        <p class="max-w-xl text-base leading-8 text-soft">{{ $portfolio['contact_intro'] }}</p>
        <div class="flex flex-wrap items-center justify-center gap-4">
            <a href="mailto:{{ $portfolio['email'] }}" class="portfolio-button max-w-full" data-magnet><span class="break-all">{{ $portfolio['email'] }}</span><span class="shrink-0" aria-hidden="true">↗</span></a>
            <button type="button" data-copy-email="{{ $portfolio['email'] }}" class="inline-flex items-center gap-2 rounded-full border border-line bg-paper px-5 py-3.5 text-sm font-semibold text-accent transition-colors hover:border-accent hover:bg-wash focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Salin email</button>
        </div>
        <span class="sr-only" aria-live="polite" data-copy-email-status></span>
        <p class="text-sm text-soft">{{ $portfolio['location'] }}</p>
        <div class="flex flex-wrap justify-center gap-4">
            @include('portfolio.partials.social-links', ['socials' => $portfolio['socials'], 'variant' => 'light'])
        </div>
    </div>
</section>
