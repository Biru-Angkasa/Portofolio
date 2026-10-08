<section id="kontak" class="bg-ink text-paper">
    <div class="mx-auto grid w-full max-w-7xl gap-12 px-5 py-20 md:px-8 md:py-28 lg:grid-cols-[minmax(0,1fr)_minmax(0,0.45fr)] lg:gap-20">
        <div class="flex flex-col items-start gap-7">
            <p class="text-xs font-semibold tracking-[0.16em] uppercase text-paper/65">{{ $works->isNotEmpty() ? '05' : '04' }} / Kontak</p>
            <h2 class="max-w-2xl font-serif text-4xl leading-tight tracking-tight md:text-6xl">Ada pekerjaan teknis yang perlu dibicarakan?</h2>
            <a href="mailto:{{ $portfolio['email'] }}" class="max-w-full border-b border-paper/60 pb-2 text-lg font-medium break-all transition-colors hover:text-paper/70 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-paper md:text-2xl">
                {{ $portfolio['email'] }} <span aria-hidden="true">↗</span>
            </a>
        </div>
        <div class="flex flex-col justify-end gap-4 border-t border-paper/25 pt-6 text-sm lg:border-t-0 lg:border-l lg:pt-0 lg:pl-8">
            <p>{{ $portfolio['location'] }}</p>
            <a href="{{ $portfolio['instagram']['url'] }}" class="w-fit text-paper/70 transition-colors hover:text-paper focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-paper" target="_blank" rel="noopener noreferrer">
                Instagram {{ $portfolio['instagram']['handle'] }} ↗
            </a>
        </div>
    </div>
</section>
