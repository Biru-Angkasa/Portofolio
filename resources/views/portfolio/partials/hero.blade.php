<section id="beranda" data-parallax-scene>
    <svg class="portfolio-threads" data-threads-still data-parallax-speed="0.16" viewBox="0 0 1440 900" preserveAspectRatio="none" aria-hidden="true">
        <path d="M-40 190 C 380 -110, 640 700, 970 230 S 1290 60, 1480 200" />
        <path d="M-40 216 C 380 -80, 640 730, 970 256 S 1290 86, 1480 226" />
        <path d="M-40 242 C 380 -50, 640 760, 970 282 S 1290 112, 1480 252" />
        <path d="M-40 268 C 380 -20, 640 790, 970 308 S 1290 138, 1480 278" />
        <path d="M-40 294 C 380 10, 640 820, 970 334 S 1290 164, 1480 304" />
        <path d="M-40 320 C 380 40, 640 850, 970 360 S 1290 190, 1480 330" />
    </svg>
    <canvas class="portfolio-threads" data-threads hidden aria-hidden="true"></canvas>
    <div class="mx-auto grid w-full max-w-7xl items-center gap-12 px-5 pt-6 pb-12 md:px-8 md:pt-8 md:pb-16 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,0.85fr)] lg:gap-20 lg:pt-10">
        <div class="hero-copy flex flex-col items-start gap-6">
            <p class="text-lg font-medium text-accent">{{ $portfolio['greeting'] }}</p>
            <h1 class="text-[clamp(3.5rem,6.5vw,6rem)] leading-[1.02] font-semibold tracking-[-0.055em]"><span data-tech-text>{{ $portfolio['name'] }}</span><span class="text-accent">.</span></h1>
            <div class="flex max-w-full flex-col gap-4">
                <p class="max-w-xl text-xl leading-snug font-medium text-accent sm:text-2xl">{{ $portfolio['role'] }}</p>
                @if ($portfolio['skills'] !== [])
                    <div class="hero-focus" data-rotating>
                        <span class="hero-focus-label">Fokus di</span>
                        <span class="sr-only">{{ implode(', ', array_column(array_slice($portfolio['skills'], 0, 3), 'name')) }}</span>
                        <span class="rotating-text" aria-hidden="true">
                            @foreach (array_slice($portfolio['skills'], 0, 3) as $skill)
                                <span class="rotating-word {{ $loop->first ? 'is-active' : '' }}" data-rotating-word>{{ $skill['name'] }}</span>
                            @endforeach
                        </span>
                        <button type="button" class="rotation-toggle" data-rotation-toggle hidden aria-label="Jeda pergantian keahlian" aria-pressed="false">
                            <svg data-rotation-pause aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path d="M6 5h3v10H6zm5 0h3v10h-3z" /></svg>
                            <svg data-rotation-play hidden aria-hidden="true" viewBox="0 0 20 20" fill="currentColor"><path d="m7 4 9 6-9 6z" /></svg>
                        </button>
                    </div>
                @endif
            </div>
            <p class="max-w-lg text-base leading-8 text-soft">{{ $portfolio['lead'] }}</p>
            <div class="flex flex-wrap items-center gap-x-7 gap-y-5 pt-2">
                <a href="#kontak" class="portfolio-button" data-magnet>Hubungi saya <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M7 17 17 7M7 7h10v10" /></svg></a>
                <a href="{{ route('cv.download') }}" class="inline-flex items-center gap-3 rounded-xl border border-accent px-5 py-3 text-sm font-semibold text-accent transition-colors hover:bg-wash focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent" download>Unduh CV <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M12 3v12m-5-5 5 5 5-5M5 17v4h14v-4" /></svg></a>
                <a href="#tentang" class="inline-flex items-center gap-3 text-sm font-medium text-ink transition-colors hover:text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Kenali lebih dekat <span aria-hidden="true">↓</span></a>
            </div>
        </div>
        <div class="portfolio-portrait relative mx-auto w-full max-w-md" data-depth>
            <div class="absolute inset-6 rounded-full bg-[#e6eef9]" data-parallax-speed="0.09" aria-hidden="true"></div>
            <div class="profile-photo-shape relative flex aspect-square items-center justify-center overflow-hidden border-[12px] border-paper bg-accent" data-parallax-speed="-0.04">
                @if ($profile->photo_path)
                    <img src="{{ $profile->photoUrl() }}" alt="{{ $profile->photo_alt }}" class="h-full w-full object-cover">
                @else
                    <span class="text-[7rem] font-semibold tracking-[-0.08em] text-white sm:text-[9rem]" aria-label="Monogram {{ $portfolio['name'] }}">{{ $portfolio['monogram'] }}</span>
                @endif
            </div>
            <p class="absolute right-1 bottom-8 max-w-[85%] rounded-2xl border border-line bg-paper px-5 py-4 text-xs font-medium text-accent shadow-sm" data-parallax-speed="-0.11">{{ $portfolio['location'] }}</p>
        </div>
    </div>
    @if ($portfolio['highlights'] !== [])
        <aside class="mx-auto grid w-full max-w-7xl gap-5 px-5 pb-14 sm:grid-cols-2 md:px-8 lg:grid-cols-3 lg:pb-20" aria-label="Ringkasan pengalaman">
            @foreach ($portfolio['highlights'] as $highlight)
                <div class="flex items-center gap-5 rounded-2xl bg-wash px-6 py-5">
                    <p class="text-4xl font-semibold tracking-tight text-accent">{{ $highlight['value'] }}</p>
                    <div class="flex flex-col gap-1"><p class="text-sm font-semibold">{{ $highlight['label'] }}</p><p class="text-xs text-soft">{{ $highlight['context'] }}</p></div>
                </div>
            @endforeach
        </aside>
    @endif
</section>
