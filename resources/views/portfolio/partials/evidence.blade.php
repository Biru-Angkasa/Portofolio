<section id="bukti" class="interactive-section" data-network-background="nodes">
    <div class="mx-auto w-full max-w-7xl px-5 py-16 md:px-8 md:py-24">
        <div class="mx-auto mb-12 flex max-w-2xl flex-col gap-4 text-center"><p class="section-label">Portofolio</p><h2 class="section-title">{{ $portfolio['portfolio_title'] }}</h2><p class="text-base leading-8 text-soft">{{ $portfolio['portfolio_intro'] }}</p></div>
        @if ($works->isEmpty())
            <p class="rounded-2xl border border-dashed border-line bg-wash px-6 py-12 text-center text-sm text-soft">Dokumentasi sedang disiapkan.</p>
        @else
            <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($works as $work)
                    @include('portfolio.partials.work-card', ['work' => $work])
                @endforeach
            </ul>
        @endif
    </div>
</section>
