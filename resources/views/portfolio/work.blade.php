@extends('layouts.portfolio')

@section('content')
    <article class="mx-auto flex w-full max-w-7xl flex-col gap-12 px-5 py-12 md:gap-16 md:px-8 md:py-20">
        <p>
            <a href="{{ route('home') }}#bukti" class="border-b border-accent pb-1 text-sm text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">← Kembali ke dokumentasi</a>
        </p>

        <header class="grid gap-8 border-b border-line pb-12 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,0.8fr)] lg:gap-20">
            <div class="flex flex-col gap-5">
                <p class="text-xs font-semibold tracking-[0.16em] uppercase text-accent">{{ $work->category }}@if ($work->period) · {{ $work->period }}@endif</p>
                <h1 class="font-serif text-4xl leading-tight tracking-tight md:text-6xl">{{ $work->title }}</h1>
            </div>
            <p class="max-w-xl self-end text-lg leading-relaxed text-soft">{{ $work->summary }}</p>
        </header>

        <div class="grid gap-4 md:grid-cols-[12rem_minmax(0,1fr)] md:gap-12">
            <h2 class="text-xs font-semibold tracking-[0.16em] uppercase text-accent">Yang dikerjakan</h2>
            <p class="max-w-3xl text-lg leading-relaxed">{{ $work->role }}</p>
        </div>

        @if ($work->caption)
            <p class="max-w-3xl leading-relaxed text-soft md:ml-60">{{ $work->caption }}</p>
        @endif

        @if ($work->media->isNotEmpty())
            <div class="flex max-w-4xl flex-col gap-12 border-t border-line pt-12">
                @foreach ($work->media as $media)
                    <figure class="flex flex-col gap-3">
                        @if ($media->kind === \App\MediaKind::Image)
                            <img src="{{ $media->fileUrl() }}" alt="{{ $media->label }}" loading="lazy" class="w-full object-cover">
                        @elseif ($media->kind === \App\MediaKind::Pdf)
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ $media->fileUrl() }}" class="bg-accent px-4 py-2 text-sm font-semibold text-paper focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent" target="_blank" rel="noopener noreferrer">Buka PDF</a>
                                <a href="{{ $media->fileUrl() }}" class="border border-ink px-4 py-2 text-sm font-semibold focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent" download>Unduh PDF</a>
                            </div>
                            <figcaption>{{ $media->label }}</figcaption>
                        @else
                            <a href="{{ $media->url }}" class="w-fit border-b border-accent pb-1 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent" target="_blank" rel="noopener noreferrer">{{ $media->label }}</a>
                        @endif
                        @if ($media->caption)
                            <figcaption class="text-sm leading-relaxed text-soft">{{ $media->caption }}</figcaption>
                        @endif
                    </figure>
                @endforeach
            </div>
        @endif
    </article>
@endsection
