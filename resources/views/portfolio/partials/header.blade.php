<header class="sticky top-0 z-40 border-b border-line bg-paper/95 backdrop-blur-md">
    <div class="mx-auto flex w-full max-w-7xl flex-wrap items-center justify-between gap-x-6 px-5 py-5 md:px-8">
        <a href="{{ route('home') }}#beranda" class="text-xl font-semibold tracking-tight text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">{{ $portfolio['name'] }}</a>
        <button type="button" class="rounded-xl border border-line px-4 py-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent lg:hidden" data-menu-toggle aria-expanded="false" aria-controls="site-nav">Menu</button>
        <nav id="site-nav" data-menu class="basis-full flex-col gap-1 pt-4 lg:basis-auto lg:flex-row lg:items-center lg:gap-5 lg:pt-0" aria-label="Utama">
            @foreach ($navigation as $key => $menu)
                <a href="{{ route('home') }}#{{ $key === 'portofolio' ? 'bukti' : $key }}" class="rounded-lg px-2 py-2 text-sm font-medium text-soft transition-colors hover:bg-wash hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">{{ $menu['label'] }}</a>
            @endforeach
        </nav>
    </div>
</header>
