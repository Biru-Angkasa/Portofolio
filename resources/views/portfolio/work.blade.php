@extends('layouts.portfolio')

@section('content')
    @php
        $images = $work->media->filter(fn ($media): bool => $media->kind === \App\MediaKind::Image)->values();
        $pdfs = $work->media->filter(fn ($media): bool => $media->kind === \App\MediaKind::Pdf)->values();
        $links = $work->media->filter(fn ($media): bool => $media->kind === \App\MediaKind::Link)->values();
        $primaryLink = $links->first();
        $technologies = $work->technologies();
    @endphp

    <article class="flex flex-col">
        <section class="interactive-section border-b border-line bg-wash" data-network-background="nodes">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-5 py-12 md:px-8 md:py-16">
                <a href="{{ route('home') }}#bukti" class="inline-flex w-fit items-center gap-2 rounded-full border border-line bg-paper px-4 py-2 text-xs font-semibold text-accent transition-colors hover:border-accent hover:bg-paper focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                    <span aria-hidden="true">←</span> Kembali ke portofolio
                </a>

                <div class="grid items-start gap-8 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,0.65fr)] lg:gap-12">
                    <div class="flex flex-col items-start gap-5">
                        <span class="inline-flex items-center gap-2 rounded-full border border-line bg-paper px-3.5 py-1 text-xs font-semibold text-accent">
                            {{ $work->category }}@if ($work->period) <span aria-hidden="true">·</span> {{ $work->period }}@endif
                        </span>
                        <h1 class="text-3xl leading-[1.15] font-semibold tracking-[-0.04em] sm:text-4xl lg:text-5xl">{{ $work->title }}</h1>
                        <p class="max-w-2xl text-base leading-8 text-soft sm:text-lg">{{ $work->summary }}</p>

                        @if ($primaryLink?->url || $images->isNotEmpty() || $pdfs->isNotEmpty())
                            <div class="flex flex-wrap items-center gap-4 pt-2">
                                @if ($primaryLink?->url)
                                    <a href="{{ $primaryLink->url }}" target="_blank" rel="noopener noreferrer" class="portfolio-button" data-magnet>
                                        {{ $primaryLink->label }}
                                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M7 17 17 7M7 7h10v10" /></svg>
                                    </a>
                                @endif
                                @if ($images->isNotEmpty())
                                    <a href="#dokumentasi" class="inline-flex items-center gap-2 rounded-full border border-line bg-paper px-5 py-3.5 text-sm font-semibold text-ink transition-colors hover:border-accent hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                                        Lihat {{ $images->count() }} dokumentasi <span aria-hidden="true">↓</span>
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <aside class="portfolio-card flex flex-col gap-5 p-6" aria-label="Ringkasan informasi proyek">
                        <h2 class="section-label">Info proyek</h2>
                        <dl class="flex flex-col gap-4 text-sm">
                            <div class="flex items-center justify-between gap-4 border-b border-line pb-3">
                                <dt class="text-soft">Kategori</dt>
                                <dd class="font-semibold text-ink">{{ $work->category }}</dd>
                            </div>
                            @if ($work->period)
                                <div class="flex items-center justify-between gap-4 border-b border-line pb-3">
                                    <dt class="text-soft">Periode</dt>
                                    <dd class="font-semibold text-ink">{{ $work->period }}</dd>
                                </div>
                            @endif
                            <div class="flex items-center justify-between gap-4 @if ($technologies !== []) border-b border-line pb-3 @endif">
                                <dt class="text-soft">Lampiran</dt>
                                <dd class="font-semibold text-ink">{{ $work->media->count() }} item</dd>
                            </div>
                        </dl>
                        @if ($technologies !== [])
                            <div class="flex flex-col gap-3">
                                <p class="text-xs font-semibold text-soft">Teknologi</p>
                                <ul class="flex flex-wrap gap-2">
                                    @foreach ($technologies as $technology)
                                        <li class="rounded-lg border border-line bg-wash px-3 py-1 text-xs font-medium text-accent">{{ $technology }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </aside>
                </div>
            </div>
        </section>

        <section class="mx-auto flex w-full max-w-7xl flex-col gap-12 px-5 py-12 md:px-8 md:py-16">
            <div class="grid gap-6 @if ($work->outcome) lg:grid-cols-2 @endif">
                <div class="portfolio-card flex flex-col gap-4 p-6 md:p-8">
                    <p class="section-label">Yang dikerjakan</p>
                    <h2 class="text-xl font-semibold tracking-tight text-ink">{{ $work->role }}</h2>
                    @if ($work->caption)
                        <p class="border-t border-line pt-4 text-sm leading-7 text-soft">{{ $work->caption }}</p>
                    @endif
                </div>

                @if ($work->outcome)
                    <div class="portfolio-card flex flex-col gap-4 border-l-4 border-l-accent bg-wash/60 p-6 md:p-8">
                        <h2 class="section-label">Hasil proyek</h2>
                        <p class="text-lg leading-relaxed font-medium text-ink">{{ $work->outcome }}</p>
                    </div>
                @endif
            </div>

            @if ($images->isNotEmpty())
                @php($featuredImage = $images->first())
                @php($remainingImages = $images->slice(1))

                <section id="dokumentasi" class="flex flex-col gap-6" aria-label="Galeri dokumentasi">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div class="flex flex-col gap-2">
                            <p class="section-label">Dokumentasi</p>
                            <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Tampilan & hasil implementasi</h2>
                        </div>
                        <p class="text-xs text-soft">Klik gambar untuk memperbesar.</p>
                    </div>

                    <figure class="portfolio-card overflow-hidden">
                        <div class="flex items-center justify-between gap-4 border-b border-line bg-wash px-5 py-3">
                            <span class="text-xs font-semibold text-ink">{{ $featuredImage->label }}</span>
                            <button type="button" data-lightbox-open data-lightbox-src="{{ $featuredImage->fileUrl() }}" data-lightbox-alt="{{ $featuredImage->label }}" data-lightbox-caption="{{ $featuredImage->caption ?? '' }}" class="text-xs font-semibold text-accent hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">Perbesar ↗</button>
                        </div>
                        <button type="button" data-lightbox-open data-lightbox-src="{{ $featuredImage->fileUrl() }}" data-lightbox-alt="{{ $featuredImage->label }}" data-lightbox-caption="{{ $featuredImage->caption ?? '' }}" class="block w-full cursor-zoom-in bg-wash/40 text-left focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
                            <img src="{{ $featuredImage->fileUrl() }}" alt="{{ $featuredImage->label }}" class="max-h-[34rem] w-full object-contain">
                        </button>
                        @if ($featuredImage->caption)
                            <figcaption class="border-t border-line px-5 py-4 text-sm leading-7 text-soft">{{ $featuredImage->caption }}</figcaption>
                        @endif
                    </figure>

                    @if ($remainingImages->isNotEmpty())
                        <div class="grid gap-6 md:grid-cols-2">
                            @foreach ($remainingImages as $media)
                                <figure class="portfolio-card flex flex-col overflow-hidden">
                                    <div class="flex items-center justify-between gap-3 border-b border-line bg-wash px-5 py-3">
                                        <span class="text-xs font-semibold text-ink">{{ $media->label }}</span>
                                        <button type="button" data-lightbox-open data-lightbox-src="{{ $media->fileUrl() }}" data-lightbox-alt="{{ $media->label }}" data-lightbox-caption="{{ $media->caption ?? '' }}" class="shrink-0 text-xs font-semibold text-accent hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">Perbesar ↗</button>
                                    </div>
                                    <button type="button" data-lightbox-open data-lightbox-src="{{ $media->fileUrl() }}" data-lightbox-alt="{{ $media->label }}" data-lightbox-caption="{{ $media->caption ?? '' }}" class="block w-full cursor-zoom-in bg-wash/40 text-left focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
                                        <img src="{{ $media->fileUrl() }}" alt="{{ $media->label }}" loading="lazy" class="aspect-[16/10] w-full object-cover">
                                    </button>
                                    @if ($media->caption)
                                        <figcaption class="border-t border-line px-5 py-4 text-sm leading-7 text-soft">{{ $media->caption }}</figcaption>
                                    @endif
                                </figure>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            @if ($pdfs->isNotEmpty() || $links->isNotEmpty())
                <section class="flex flex-col gap-5" aria-label="Lampiran dan tautan">
                    <h2 class="section-label">Tautan & berkas</h2>
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($links as $media)
                            <div class="portfolio-card flex flex-wrap items-center justify-between gap-4 p-5">
                                <div class="flex min-w-0 flex-1 flex-col gap-1">
                                    <p class="text-xs font-semibold text-accent">Tautan</p>
                                    <p class="font-semibold text-ink">{{ $media->label }}</p>
                                    @if ($media->caption)
                                        <p class="text-sm text-soft">{{ $media->caption }}</p>
                                    @endif
                                </div>
                                <a href="{{ $media->url }}" target="_blank" rel="noopener noreferrer" class="inline-flex shrink-0 items-center gap-2 rounded-full border border-accent px-4 py-2 text-xs font-semibold text-accent transition-colors hover:bg-accent hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                                    Buka tautan <span aria-hidden="true">↗</span>
                                </a>
                            </div>
                        @endforeach

                        @foreach ($pdfs as $media)
                            <div class="portfolio-card flex flex-col gap-4 p-5">
                                <div class="flex flex-col gap-1">
                                    <p class="text-xs font-semibold text-accent">Dokumen PDF</p>
                                    <p class="font-semibold text-ink">{{ $media->label }}</p>
                                    @if ($media->caption)
                                        <p class="text-sm text-soft">{{ $media->caption }}</p>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ $media->fileUrl() }}" target="_blank" rel="noopener noreferrer" class="rounded-full bg-accent px-4 py-2 text-xs font-semibold text-white transition-colors hover:bg-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Buka PDF ↗</a>
                                    <a href="{{ $media->fileUrl() }}" download class="rounded-full border border-line bg-paper px-4 py-2 text-xs font-semibold text-ink transition-colors hover:border-accent hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Unduh PDF</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </section>

        @if ($otherWorks->isNotEmpty())
            <section class="interactive-section border-t border-line bg-wash" data-network-background="grid" aria-label="Proyek lainnya">
                <div class="mx-auto flex w-full max-w-7xl flex-col gap-8 px-5 py-14 md:px-8 md:py-20">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div class="flex flex-col gap-2">
                            <p class="section-label">Jelajahi lainnya</p>
                            <h2 class="text-2xl font-semibold tracking-tight sm:text-3xl">Proyek & solusi lainnya</h2>
                        </div>
                        <a href="{{ route('home') }}#bukti" class="text-sm font-semibold text-accent hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Lihat semua di beranda →</a>
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
