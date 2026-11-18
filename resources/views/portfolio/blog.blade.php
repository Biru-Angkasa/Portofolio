@extends('layouts.portfolio')

@section('structured-data')
    @php
        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $post->title,
            'description' => $post->excerpt,
            'articleSection' => $post->category,
            'url' => route('blog.show', $post),
            'inLanguage' => 'id-ID',
            'datePublished' => $post->created_at?->toAtomString(),
            'dateModified' => $post->updated_at?->toAtomString(),
            'author' => [
                '@type' => 'Person',
                '@id' => route('home').'#person',
                'name' => $portfolio['name'],
                'url' => route('home'),
            ],
        ];

        if ($post->cover_path) {
            $articleSchema['image'] = $post->coverUrl();
        }
    @endphp
    <script type="application/ld+json">{!! \Illuminate\Support\Js::encode($articleSchema) !!}</script>
@endsection

@section('content')
    <article class="mx-auto flex w-full max-w-3xl flex-col gap-8 px-5 py-14 md:py-20">
        <div class="flex flex-wrap items-center justify-between gap-4 text-sm">
            <a href="{{ route('home') }}#blog" class="w-fit text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">← Kembali ke blog</a>
            <a href="{{ route('blog.index') }}" class="w-fit text-soft transition-colors hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Semua artikel</a>
        </div>
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs font-medium text-accent">
            <span class="section-label">{{ $post->category }}</span>
            @if ($post->created_at)
                <span aria-hidden="true">·</span>
                <time datetime="{{ $post->created_at->toDateString() }}" class="text-soft">{{ $post->created_at->locale('id')->translatedFormat('d F Y') }}</time>
            @endif
            <span aria-hidden="true">·</span>
            <span class="text-soft">{{ $post->readingTimeMinutes() }} menit baca</span>
        </div>
        <h1 class="section-title">{{ $post->title }}</h1>
        <p class="text-lg leading-8 text-soft">{{ $post->excerpt }}</p>
        @if ($post->cover_path)
            <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" class="w-full rounded-2xl">
        @endif
        <div class="blog-prose border-t border-line pt-8 text-base leading-8 text-ink">
            {!! $post->renderedBody() !!}
        </div>

        @if ($relatedPosts->isNotEmpty())
            <aside class="mt-6 flex flex-col gap-5 border-t border-line pt-8" aria-label="Artikel lainnya">
                <h2 class="text-lg font-semibold tracking-tight">Artikel lainnya</h2>
                <ul class="grid gap-4 sm:grid-cols-2">
                    @foreach ($relatedPosts as $related)
                        <li class="portfolio-card">
                            <a href="{{ route('blog.show', $related) }}" class="group flex h-full flex-col gap-2 p-5 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                                <span class="text-xs font-medium text-accent">{{ $related->category }}</span>
                                <span class="text-base font-semibold tracking-tight transition-colors group-hover:text-accent">{{ $related->title }}</span>
                                <span class="text-xs leading-6 text-soft">{{ $related->excerpt }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>
        @endif
    </article>
@endsection
