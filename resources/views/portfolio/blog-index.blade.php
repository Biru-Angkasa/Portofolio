@extends('layouts.portfolio')

@section('content')
    <section class="mx-auto flex w-full max-w-7xl flex-col gap-10 px-5 py-14 md:px-8 md:py-20">
        <div class="flex flex-col gap-4">
            <a href="{{ $categories->isNotEmpty() ? route('home').'#blog' : route('home') }}" class="w-fit text-sm text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">← Kembali ke beranda</a>
            <p class="section-label">Blog</p>
            <h1 class="section-title">{{ $portfolio['blog_title'] }}</h1>
            <p class="max-w-2xl text-base leading-8 text-soft">{{ $portfolio['blog_intro'] }}</p>
        </div>

        @if ($categories->isNotEmpty())
            <nav class="flex flex-wrap gap-2" aria-label="Filter kategori artikel">
                <a
                    href="{{ route('blog.index') }}"
                    @class([
                        'rounded-full border px-4 py-2 text-xs font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent',
                        'border-accent bg-accent text-white' => $activeCategory === '',
                        'border-line bg-paper text-soft hover:border-accent hover:text-accent' => $activeCategory !== '',
                    ])
                >
                    Semua
                </a>
                @foreach ($categories as $category)
                    <a
                        href="{{ route('blog.index', ['kategori' => $category]) }}"
                        @class([
                            'rounded-full border px-4 py-2 text-xs font-semibold transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent',
                            'border-accent bg-accent text-white' => $activeCategory === $category,
                            'border-line bg-paper text-soft hover:border-accent hover:text-accent' => $activeCategory !== $category,
                        ])
                    >
                        {{ $category }}
                    </a>
                @endforeach
            </nav>
        @endif

        @if ($posts->isEmpty())
            <p class="rounded-2xl border border-dashed border-line bg-wash px-6 py-12 text-center text-sm text-soft">Belum ada artikel yang dipublikasikan{{ $activeCategory !== '' ? ' untuk kategori ini' : '' }}.</p>
        @else
            <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <li class="portfolio-card overflow-hidden">
                        <a href="{{ route('blog.show', $post) }}" class="group flex h-full flex-col focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                            @if ($post->cover_path)
                                <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" loading="lazy" class="aspect-[16/10] w-full object-cover">
                            @endif
                            <span class="flex flex-1 flex-col gap-4 p-6">
                                <span class="text-xs font-medium text-accent">{{ $post->category }}</span>
                                <span class="text-xl font-semibold tracking-tight group-hover:text-accent">{{ $post->title }}</span>
                                <span class="text-sm leading-7 text-soft">{{ $post->excerpt }}</span>
                                <span class="mt-auto pt-2 text-xs font-semibold text-accent">Baca selengkapnya ↗</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>

            @if ($posts->hasPages())
                <div class="pt-4">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </section>
@endsection
