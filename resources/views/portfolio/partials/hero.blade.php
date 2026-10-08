<section id="beranda" class="border-b border-line">
    <div class="mx-auto w-full max-w-7xl px-5 pt-10 pb-12 md:px-8 md:pt-16 md:pb-20">
        <div class="flex items-center justify-between gap-4 border-b border-line pb-5 text-xs font-medium tracking-[0.16em] uppercase">
            <p class="text-accent">{{ $portfolio['role'] }}</p>
            <p class="hidden text-soft sm:block">{{ $portfolio['location'] }}</p>
        </div>

        <div class="grid gap-12 pt-12 lg:grid-cols-[minmax(0,1.45fr)_minmax(16rem,0.55fr)] lg:gap-20 lg:pt-20">
            <div class="flex flex-col items-start gap-8">
                <h1 class="max-w-4xl text-[clamp(4.25rem,11vw,9rem)] leading-[0.88] font-semibold tracking-[-0.075em] text-balance">
                    {{ $portfolio['name'] }}<span class="text-accent">.</span>
                </h1>
                <p class="max-w-2xl font-serif text-2xl leading-snug tracking-tight text-ink md:text-4xl">
                    Menangani perangkat dan jaringan, dari diagnosis hingga dokumentasi.
                </p>
                <p class="max-w-xl leading-relaxed text-soft">{{ $portfolio['lead'] }}</p>
                <div class="flex flex-wrap items-center gap-x-8 gap-y-4 pt-2">
                    <a href="#kontak" class="inline-flex items-center gap-3 bg-ink px-6 py-3.5 text-sm font-semibold text-paper transition-colors hover:bg-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                        Hubungi saya <span aria-hidden="true">↗</span>
                    </a>
                    @if ($works->isNotEmpty())
                        <a href="#bukti" class="border-b border-ink pb-1 text-sm font-medium transition-colors hover:border-accent hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
                            Lihat dokumentasi kerja
                        </a>
                    @endif
                </div>
            </div>

            <aside class="flex flex-col justify-end gap-8 border-t border-line pt-8 lg:border-t-0 lg:border-l lg:pt-0 lg:pl-10" aria-label="Ringkasan pengalaman">
                @if ($profile->photo_path)
                    <img src="{{ $profile->photoUrl() }}" alt="{{ $profile->photo_alt }}" class="aspect-[4/5] w-full max-w-xs object-cover">
                @else
                    <p class="w-fit border border-line px-3 py-2 text-xs font-semibold tracking-[0.2em] text-accent" aria-label="Monogram {{ $portfolio['name'] }}">{{ $portfolio['monogram'] }}</p>
                @endif

                <div class="grid grid-cols-2 gap-6 lg:grid-cols-1 lg:gap-8">
                    @foreach ($portfolio['highlights'] as $highlight)
                        <div class="border-t border-line pt-4">
                            <p class="font-serif text-4xl tracking-tight md:text-5xl">{{ $highlight['value'] }}</p>
                            <p class="mt-2 text-sm font-medium">{{ $highlight['label'] }}</p>
                            <p class="mt-1 text-xs leading-relaxed text-soft">{{ $highlight['context'] }}</p>
                        </div>
                    @endforeach
                </div>
            </aside>
        </div>
        <p class="pt-10 text-xs tracking-[0.12em] uppercase text-soft sm:hidden">{{ $portfolio['location'] }}</p>
    </div>
</section>
