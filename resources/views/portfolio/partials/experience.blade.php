<section id="pengalaman" class="interactive-section" data-network-background="grid">
    <div class="mx-auto grid w-full max-w-7xl gap-12 px-5 py-16 md:px-8 md:py-24 lg:grid-cols-[minmax(0,0.7fr)_minmax(0,1.3fr)] lg:gap-20">
        <div class="flex flex-col items-start gap-5"><p class="section-label">Pengalaman</p><h2 class="section-title">{{ $portfolio['experience_title'] }}</h2></div>
        <ol class="flex flex-col">
            @forelse ($portfolio['experiences'] as $experience)
                <li class="relative border-l border-line pb-12 pl-7 last:pb-0 sm:pl-10">
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
