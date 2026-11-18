<section id="blog" class="interactive-section border-y border-line bg-wash" data-network-background="nodes">
    <div class="mx-auto w-full max-w-7xl px-5 py-16 md:px-8 md:py-24">
        <div class="mx-auto mb-12 flex max-w-2xl flex-col gap-4 text-center"><p class="section-label">Blog</p><h2 class="section-title">{{ $portfolio['blog_title'] }}</h2><p class="leading-8 text-soft">{{ $portfolio['blog_intro'] }}</p></div>
        @if ($posts->isEmpty())
            <p class="rounded-2xl border border-dashed border-line bg-paper px-6 py-12 text-center text-sm text-soft">Tulisan pertama sedang disiapkan.</p>
        @else
            <ul class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($posts as $post)
                    <li class="portfolio-card overflow-hidden">
                        <a href="{{ route('blog.show', $post) }}" class="group flex h-full flex-col focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                            @if ($post->cover_path)
                                <img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" loading="lazy" class="aspect-[16/10] w-full object-cover">
                            @endif
                            <span class="flex flex-1 flex-col gap-4 p-6"><span class="text-xs font-medium text-accent">{{ $post->category }}</span><span class="text-xl font-semibold tracking-tight group-hover:text-accent">{{ $post->title }}</span><span class="text-sm leading-7 text-soft">{{ $post->excerpt }}</span><span class="mt-auto pt-2 text-xs font-semibold text-accent">Baca selengkapnya ↗</span></span>
                        </a>
                    </li>
                @endforeach
            </ul>
            @if (!empty($hasMorePosts))
                <div class="mt-10 flex justify-center">
                    <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 rounded-full border border-accent bg-paper px-6 py-3 text-sm font-semibold text-accent transition-colors hover:bg-accent hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                        Lihat semua artikel <span aria-hidden="true">→</span>
                    </a>
                </div>
            @endif
        @endif
    </div>
</section>
