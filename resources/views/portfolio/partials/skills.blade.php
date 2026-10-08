<section id="keahlian" class="border-t border-line bg-white/45">
    <div class="mx-auto grid w-full max-w-7xl gap-10 px-5 py-20 md:px-8 md:py-28 lg:grid-cols-[minmax(0,0.5fr)_minmax(0,1fr)] lg:gap-20">
        <div>
            <p class="mb-5 text-xs font-semibold tracking-[0.16em] uppercase text-accent">{{ $works->isNotEmpty() ? '03' : '02' }} / Keahlian</p>
            <h2 class="max-w-sm font-serif text-4xl leading-tight tracking-tight md:text-5xl">Peralatan dan cara kerja.</h2>
        </div>
        <dl class="border-t border-line">
            @foreach ($portfolio['skills'] as $skill)
                <div class="grid gap-3 border-b border-line py-6 sm:grid-cols-[10rem_minmax(0,1fr)] sm:gap-8">
                    <dt class="font-semibold">{{ $skill['name'] }}</dt>
                    <dd class="leading-relaxed text-soft">{{ implode(' · ', $skill['items']) }}</dd>
                </div>
            @endforeach
        </dl>
    </div>
</section>
