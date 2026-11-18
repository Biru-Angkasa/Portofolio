<section id="pendidikan" class="interactive-section border-y border-line bg-wash" data-network-background="nodes">
    <div class="mx-auto w-full max-w-7xl px-5 py-16 md:px-8 md:py-24">
        <div class="mb-10 flex flex-col gap-4 text-center"><p class="section-label">Pendidikan</p><h2 class="section-title">{{ $portfolio['education_title'] }}</h2></div>
        <div class="grid gap-6 lg:grid-cols-2">
            <div class="portfolio-card p-6 md:p-8">
                <h3 class="mb-6 text-lg font-semibold">Riwayat pendidikan</h3>
                <ul class="flex flex-col gap-6">
                    @forelse ($portfolio['education'] as $item)
                        <li class="flex items-center justify-between gap-4 border-t border-line pt-5">
                            <div class="flex min-w-0 flex-1 flex-col gap-2"><p class="text-xs font-medium text-accent">{{ $item['period'] }}</p><p class="text-lg font-semibold tracking-tight">{{ $item['institution'] }}</p><p class="text-sm text-soft">{{ $item['program'] }}</p></div>
                            @if (!empty($item['logo_path']))
                                @if (!empty($item['url']))
                                    <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Kunjungi situs {{ $item['institution'] }} (tab baru)" class="flex size-20 shrink-0 items-center justify-center rounded-xl border border-line bg-paper p-2 transition-colors hover:bg-wash focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent sm:size-24"><img src="{{ url('/'.ltrim($item['logo_path'], '/')) }}" alt="Logo {{ $item['institution'] }}" loading="lazy" class="h-full w-full object-contain"></a>
                                @else
                                    <img src="{{ url('/'.ltrim($item['logo_path'], '/')) }}" alt="Logo {{ $item['institution'] }}" loading="lazy" class="size-20 shrink-0 rounded-xl border border-line bg-paper p-2 object-contain sm:size-24">
                                @endif
                            @endif
                        </li>
                    @empty
                        <li class="text-sm text-soft">Riwayat pendidikan akan ditambahkan.</li>
                    @endforelse
                </ul>
            </div>
            <div class="portfolio-card p-6 md:p-8">
                <h3 class="mb-6 text-lg font-semibold">Sertifikasi</h3>
                <ul class="flex flex-col gap-6">
                    @forelse ($portfolio['certifications'] as $item)
                        <li class="flex justify-between gap-5 border-t border-line pt-5"><p class="font-semibold tracking-tight">{{ $item['name'] }}</p><p class="shrink-0 text-xs text-accent">{{ $item['year'] }}</p></li>
                    @empty
                        <li class="text-sm text-soft">Sertifikasi akan ditambahkan.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</section>
