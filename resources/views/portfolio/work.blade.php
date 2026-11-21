@extends('layouts.portfolio')

@section('structured-data')
    @php
        $workImage = $work->media->first(fn ($media): bool => $media->kind === \App\MediaKind::Image)?->fileUrl();
        $workTechnologies = $work->technologies();
        $workSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => $work->title,
            'description' => $work->summary,
            'url' => $work->detailUrl(),
            'inLanguage' => 'id-ID',
            'dateModified' => $work->updated_at?->toAtomString(),
            'author' => [
                '@type' => 'Person',
                '@id' => route('home').'#person',
                'name' => $portfolio['name'],
                'url' => route('home'),
            ],
        ];

        if ($workImage) {
            $workSchema['image'] = $workImage;
        }

        if ($workTechnologies !== []) {
            $workSchema['keywords'] = implode(', ', $workTechnologies);
        }
    @endphp
    <script type="application/ld+json">{!! \Illuminate\Support\Js::encode($workSchema) !!}</script>
@endsection

@section('content')
    @php
        $images = $work->media->filter(fn ($media): bool => $media->kind === \App\MediaKind::Image)->values();
        $pdfs = $work->media->filter(fn ($media): bool => $media->kind === \App\MediaKind::Pdf)->values();
        $links = $work->media->filter(fn ($media): bool => $media->kind === \App\MediaKind::Link)->values();
        $primaryLink = $links->first();
        $remainingLinks = $links->slice(1);
        $technologies = $work->technologies();
    @endphp

    <article class="flex flex-col">
        <header class="border-b border-line bg-paper">
            <div class="mx-auto flex w-full max-w-5xl flex-col gap-8 px-5 pt-10 pb-12 md:px-8 md:pt-14 md:pb-16">
                <a href="{{ route('home') }}#bukti" class="w-fit text-sm font-medium text-accent transition-colors hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                    ← Kembali ke portofolio
                </a>

                <div class="flex flex-col items-start gap-5">
                    <p class="section-label">
                        {{ $work->category }}@if ($work->period) · {{ $work->period }}@endif
                    </p>
                    <h1 class="max-w-4xl text-3xl leading-[1.12] font-semibold tracking-[-0.04em] sm:text-5xl">{{ $work->title }}</h1>
                    <p class="max-w-3xl text-base leading-8 text-soft sm:text-lg">{{ $work->summary }}</p>
                </div>

                @if ($primaryLink?->url || $technologies !== [])
                    <div class="flex flex-wrap items-center justify-between gap-6 pt-1">
                        @if ($primaryLink?->url)
                            <a href="{{ $primaryLink->url }}" target="_blank" rel="noopener noreferrer" class="portfolio-button" data-magnet>
                                {{ $primaryLink->label }}
                                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M7 17 17 7M7 7h10v10" /></svg>
                            </a>
                        @endif

                        @if ($technologies !== [])
                            <div class="flex flex-wrap items-center gap-2" aria-label="Teknologi">
                                <span class="mr-1 text-xs font-medium text-soft">Teknologi:</span>
                                @foreach ($technologies as $technology)
                                    <span class="rounded-md border border-line bg-wash px-2.5 py-1 text-xs font-medium text-ink">{{ $technology }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </header>

        <div class="mx-auto flex w-full max-w-5xl flex-col gap-12 px-5 py-12 md:px-8 md:py-16">
            <section class="flex flex-col divide-y divide-line border-y border-line" aria-label="Ringkasan pengerjaan">
                <div class="grid gap-3 py-7 md:grid-cols-[13rem_minmax(0,1fr)] md:gap-10">
                    <h2 class="section-label">Yang dikerjakan</h2>
                    <div class="flex flex-col gap-3">
                        <p class="text-base leading-8 font-medium text-ink sm:text-lg">{{ $work->role }}</p>
                        @if ($work->caption)
                            <p class="text-sm leading-7 text-soft sm:text-base">{{ $work->caption }}</p>
                        @endif
                    </div>
                </div>

                @if ($work->outcome)
                    <div class="grid gap-3 py-7 md:grid-cols-[13rem_minmax(0,1fr)] md:gap-10">
                        <h2 class="section-label">Hasil proyek</h2>
                        <p class="text-base leading-8 text-ink sm:text-lg">{{ $work->outcome }}</p>
                    </div>
                @endif
            </section>

            @if ($images->isNotEmpty())
                @php($featuredImage = $images->first())
                @php($remainingImages = $images->slice(1))

                <section id="dokumentasi" class="flex flex-col gap-8" aria-label="Dokumentasi">
                    <figure class="flex flex-col gap-3">
                        <button type="button" data-lightbox-open data-lightbox-src="{{ $featuredImage->fileUrl() }}" data-lightbox-alt="{{ $featuredImage->label }}" data-lightbox-caption="{{ $featuredImage->caption ?? '' }}" aria-label="Perbesar {{ $featuredImage->label }}" class="group block w-full cursor-zoom-in overflow-hidden rounded-2xl border border-line bg-wash/40 transition-shadow hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                            <img src="{{ $featuredImage->fileUrl() }}" alt="{{ $featuredImage->label }}" class="max-h-[36rem] w-full object-contain">
                        </button>
                        <figcaption class="flex flex-col gap-1 text-sm leading-relaxed">
                            <span class="font-medium text-ink">{{ $featuredImage->label }}</span>
                            @if ($featuredImage->caption)
                                <span class="text-soft">{{ $featuredImage->caption }}</span>
                            @endif
                        </figcaption>
                    </figure>

                    @if ($remainingImages->isNotEmpty())
                        <div class="grid gap-8 md:grid-cols-2">
                            @foreach ($remainingImages as $media)
                                <figure class="flex flex-col gap-3">
                                    <button type="button" data-lightbox-open data-lightbox-src="{{ $media->fileUrl() }}" data-lightbox-alt="{{ $media->label }}" data-lightbox-caption="{{ $media->caption ?? '' }}" aria-label="Perbesar {{ $media->label }}" class="group block w-full cursor-zoom-in overflow-hidden rounded-2xl border border-line bg-wash/40 transition-shadow hover:shadow-md focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                                        <img src="{{ $media->fileUrl() }}" alt="{{ $media->label }}" loading="lazy" class="aspect-[16/10] w-full object-cover">
                                    </button>
                                    <figcaption class="flex flex-col gap-1 text-sm leading-relaxed">
                                        <span class="font-medium text-ink">{{ $media->label }}</span>
                                        @if ($media->caption)
                                            <span class="text-soft">{{ $media->caption }}</span>
                                        @endif
                                    </figcaption>
                                </figure>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            @if ($pdfs->isNotEmpty() || $remainingLinks->isNotEmpty())
                <section class="flex flex-col gap-4 border-t border-line pt-8" aria-label="Lampiran tambahan">
                    <h2 class="section-label">Lampiran</h2>
                    <div class="flex flex-col divide-y divide-line">
                        @foreach ($remainingLinks as $media)
                            <div class="flex flex-wrap items-center justify-between gap-4 py-4">
                                <div class="flex flex-col gap-1">
                                    <p class="font-medium text-ink">{{ $media->label }}</p>
                                    @if ($media->caption)
                                        <p class="text-sm text-soft">{{ $media->caption }}</p>
                                    @endif
                                </div>
                                <a href="{{ $media->url }}" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-accent hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Buka tautan ↗</a>
                            </div>
                        @endforeach

                        @foreach ($pdfs as $media)
                            <div class="flex flex-wrap items-center justify-between gap-4 py-4">
                                <div class="flex flex-col gap-1">
                                    <p class="font-medium text-ink">{{ $media->label }}</p>
                                    @if ($media->caption)
                                        <p class="text-sm text-soft">{{ $media->caption }}</p>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-3 text-xs font-semibold">
                                    <a href="{{ $media->fileUrl() }}" target="_blank" rel="noopener noreferrer" class="rounded-full bg-accent px-4 py-2 text-white hover:bg-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Buka PDF ↗</a>
                                    <a href="{{ $media->fileUrl() }}" download class="rounded-full border border-line px-4 py-2 text-ink hover:border-accent hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Unduh PDF</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>

        @if ($otherWorks->isNotEmpty())
            <section class="border-t border-line bg-wash" aria-label="Proyek lainnya">
                <div class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-5 py-14 md:px-8 md:py-20">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Proyek & solusi lainnya</h2>
                        <a href="{{ route('home') }}#bukti" class="text-sm font-semibold text-accent hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Lihat semua →</a>
                    </div>
                    <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        @foreach ($otherWorks as $otherWork)
                            @include('portfolio.partials.work-card', ['work' => $otherWork])
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif
    </article>

    <dialog data-lightbox-dialog class="m-auto max-h-[92vh] w-[min(94vw,68rem)] rounded-2xl border border-line bg-paper p-0 text-ink shadow-2xl backdrop:bg-ink/75 backdrop:backdrop-blur-xs">
        <div class="flex items-center justify-between gap-4 border-b border-line bg-wash px-5 py-3.5">
            <p data-lightbox-title class="text-sm font-semibold text-ink"></p>
            <button type="button" data-lightbox-close class="rounded-full border border-line bg-paper px-3.5 py-1.5 text-xs font-semibold text-ink transition-colors hover:border-accent hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">Tutup ✕</button>
        </div>
        <div class="flex flex-col bg-paper">
            <img data-lightbox-image src="" alt="" class="max-h-[75vh] w-full bg-wash/40 object-contain">
            <p data-lightbox-desc class="border-t border-line px-5 py-4 text-sm leading-7 text-soft" hidden></p>
        </div>
    </dialog>
@endsection
