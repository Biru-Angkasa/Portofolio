<section id="tentang" class="interactive-section border-y border-line bg-wash" data-network-background="grid">
    <div class="mx-auto grid w-full max-w-7xl items-start gap-10 px-5 py-16 md:px-8 md:py-24 lg:grid-cols-2 lg:gap-20">
        <div class="flex flex-col gap-5 lg:sticky lg:top-28 lg:self-start">
            <p class="section-label">Tentang Saya</p>
            <h2 class="section-title">{{ $portfolio['about_title'] }}</h2>
            <p class="text-base leading-8 whitespace-pre-line text-soft">{{ $portfolio['about'] }}</p>
        </div>
        @include('portfolio.partials.skills')
    </div>
</section>
