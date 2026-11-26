<section id="bukti" class="interactive-section" @if ($works->count() > 1) data-horizontal-showcase @endif>
    @if ($works->isEmpty())
        <div class="mx-auto w-full max-w-7xl px-5 py-16 md:px-8 md:py-24">
            <div class="mx-auto mb-12 flex max-w-2xl flex-col gap-4 text-center">
                <p class="section-label">Portofolio</p>
                <h2 class="section-title">{{ $portfolio['portfolio_title'] }}</h2>
                <p class="text-base leading-8 text-soft">{{ $portfolio['portfolio_intro'] }}</p>
            </div>
            <p class="rounded-2xl border border-dashed border-line bg-wash px-6 py-12 text-center text-sm text-soft">Dokumentasi sedang disiapkan.</p>
        </div>
    @else
        <div class="horizontal-track" data-horizontal-track>
            <div class="horizontal-pin mx-auto flex w-full max-w-7xl flex-col gap-8 px-5 py-16 md:px-8 md:py-20" data-horizontal-pin>
                <div class="flex flex-wrap items-end justify-between gap-6">
                    <div class="flex max-w-2xl flex-col gap-3">
                        <p class="section-label">Portofolio</p>
                        <h2 class="section-title">{{ $portfolio['portfolio_title'] }}</h2>
                        <p class="text-base leading-8 text-soft">{{ $portfolio['portfolio_intro'] }}</p>
                    </div>

                    @if ($works->count() > 1)
                        <div class="flex items-center gap-4" data-horizontal-controls>
                            <span class="font-mono text-xs font-medium text-soft" data-horizontal-counter>01 / {{ str_pad((string) $works->count(), 2, '0', STR_PAD_LEFT) }}</span>
                            <div class="flex items-center gap-2">
                                <button type="button" data-horizontal-prev aria-label="Proyek sebelumnya" class="inline-flex size-10 items-center justify-center rounded-full border border-line bg-paper text-sm text-ink transition-colors hover:border-accent hover:text-accent disabled:opacity-35 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">←</button>
                                <button type="button" data-horizontal-next aria-label="Proyek berikutnya" class="inline-flex size-10 items-center justify-center rounded-full border border-line bg-paper text-sm text-ink transition-colors hover:border-accent hover:text-accent disabled:opacity-35 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">→</button>
                            </div>
                        </div>
                    @endif
                </div>

                @if ($works->count() > 1)
                    <div class="h-0.5 w-full overflow-hidden rounded-full bg-line" aria-hidden="true">
                        <div class="h-full origin-left bg-accent" data-horizontal-bar style="transform: scaleX({{ number_format(1 / $works->count(), 4, '.', '') }})"></div>
                    </div>
                @endif

                <div class="horizontal-viewport" data-horizontal-viewport>
                    <ul class="horizontal-rail grid gap-6 md:grid-cols-2 lg:grid-cols-3" data-horizontal-rail>
                        @foreach ($works as $work)
                            @include('portfolio.partials.work-card', ['work' => $work])
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif
</section>
