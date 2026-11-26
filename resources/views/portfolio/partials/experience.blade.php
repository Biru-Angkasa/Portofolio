<section id="pengalaman" class="interactive-section" data-network-background="grid" data-pinned-experience>
    <div class="mx-auto grid w-full max-w-7xl items-start gap-12 px-5 py-16 md:px-8 md:py-24 lg:grid-cols-[minmax(0,0.7fr)_minmax(0,1.3fr)] lg:gap-20">
        <div class="flex flex-col items-start gap-5 lg:sticky lg:top-28 lg:self-start">
            <p class="section-label">Pengalaman</p>
            <h2 class="section-title">{{ $portfolio['experience_title'] }}</h2>
            @if (count($portfolio['experiences']) > 1)
                <div class="hidden w-full max-w-xs flex-col gap-3 pt-4 lg:flex" data-experience-hud aria-hidden="true">
                    <div class="flex items-center justify-between text-xs font-medium">
                        <span class="text-accent" data-experience-label>{{ $portfolio['experiences'][0]['organization'] }}</span>
                        <span class="font-mono text-soft" data-experience-counter>01 / {{ str_pad((string) count($portfolio['experiences']), 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="h-0.5 w-full overflow-hidden rounded-full bg-line">
                        <div class="h-full origin-left bg-accent transition-transform duration-150" data-experience-progress style="transform: scaleX(0.5)"></div>
                    </div>
                </div>
            @endif
        </div>
        <ol class="flex flex-col">
            @forelse ($portfolio['experiences'] as $experience)
                <li class="relative border-l border-line pb-12 pl-7 last:pb-0 sm:pl-10" data-experience-item data-organization="{{ $experience['organization'] }}">
                    <span class="absolute top-1 -left-[5px] size-[9px] rounded-full bg-accent ring-4 ring-wash" aria-hidden="true"></span>
                    <div class="flex flex-col gap-3">
                        <p class="text-xs font-medium text-accent">{{ $experience['period'] }}</p>
                        <h3 class="text-2xl font-semibold tracking-tight">{{ $experience['organization'] }}</h3>
                        <p class="text-sm font-medium text-ink">{{ $experience['role'] }}</p>
                        <ul class="mt-2 flex list-disc flex-col gap-2 pl-4 text-sm leading-7 text-soft marker:text-accent">
                            @foreach ($experience['points'] as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @empty
                <li class="text-sm text-soft">Pengalaman akan ditambahkan.</li>
            @endforelse
        </ol>
    </div>
</section>
