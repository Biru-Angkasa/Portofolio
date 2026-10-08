@php
    $showEvidence = (isset($works) && $works->isNotEmpty()) || request()->routeIs('work.show');
@endphp

<header class="sticky top-0 z-40 border-b border-line bg-paper/95 backdrop-blur-sm">
    <div class="mx-auto flex w-full max-w-7xl flex-wrap items-center justify-between gap-x-6 px-5 py-4 md:px-8">
        <a href="{{ route('home') }}#beranda" class="text-sm font-semibold tracking-tight focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
            {{ $portfolio['name'] }}<span class="text-accent">.</span>
        </a>

        <div class="flex items-center gap-3 md:order-3">
            <a href="{{ route('home') }}#kontak" class="hidden border-b border-ink pb-1 text-sm font-semibold transition-colors hover:border-accent hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent md:inline-flex">
                Hubungi saya ↗
            </a>
            <button
                type="button"
                class="border border-line px-4 py-2 text-sm font-medium focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent md:hidden"
                data-menu-toggle
                aria-expanded="false"
                aria-controls="site-nav"
            >
                Menu
            </button>
        </div>

        <nav id="site-nav" data-menu class="basis-full flex-col gap-1 py-3 md:order-2 md:basis-auto md:flex-row md:items-center md:gap-6 md:py-0" aria-label="Utama">
            <a href="{{ route('home') }}#pengalaman" class="px-2 py-2 text-sm text-soft hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Pengalaman</a>
            @if ($showEvidence)
                <a href="{{ route('home') }}#bukti" class="px-2 py-2 text-sm text-soft hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Dokumentasi</a>
            @endif
            <a href="{{ route('home') }}#keahlian" class="px-2 py-2 text-sm text-soft hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Keahlian</a>
            <a href="{{ route('home') }}#kontak" class="px-2 py-2 text-sm text-soft hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent md:hidden">Hubungi saya</a>
        </nav>
    </div>
</header>
