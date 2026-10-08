@if ($works->isNotEmpty())
    <section id="bukti" class="border-t border-line">
        <div class="mx-auto w-full max-w-7xl px-5 py-20 md:px-8 md:py-28">
            <div class="grid gap-6 pb-12 lg:grid-cols-[minmax(0,0.6fr)_minmax(0,1fr)] lg:gap-20">
                <div>
                    <p class="mb-5 text-xs font-semibold tracking-[0.16em] uppercase text-accent">02 / Dokumentasi</p>
                    <h2 class="font-serif text-4xl leading-tight tracking-tight md:text-5xl">Pekerjaan yang bisa dilihat lebih dekat.</h2>
                </div>
                <p class="max-w-xl self-end leading-relaxed text-soft">Catatan, foto, dan berkas dari pekerjaan yang dapat dibagikan.</p>
            </div>
            <ul class="border-t border-line">
                @foreach ($works as $work)
                    @php
                        $image = $work->media->first(fn ($media) => $media->kind === \App\MediaKind::Image);
                    @endphp
                    <li class="border-b border-line py-8 md:py-10">
                        <a href="{{ route('work.show', $work) }}" class="group grid gap-6 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent md:grid-cols-[minmax(0,18rem)_minmax(0,1fr)_2rem] md:items-center md:gap-10">
                            @if ($image)
                                <img src="{{ $image->fileUrl() }}" alt="{{ $image->label }}" loading="lazy" class="aspect-[4/3] w-full object-cover">
                            @endif
                            <span class="flex flex-col gap-3 {{ $image ? '' : 'md:col-span-2' }}">
                                <span class="text-xs font-medium tracking-[0.12em] uppercase text-accent">{{ $work->category }}@if ($work->period) · {{ $work->period }}@endif</span>
                                <span class="font-serif text-2xl tracking-tight transition-colors group-hover:text-accent md:text-3xl">{{ $work->title }}</span>
                                <span class="max-w-2xl leading-relaxed text-soft">{{ $work->summary }}</span>
                            </span>
                            <span class="hidden text-2xl text-accent md:block" aria-hidden="true">↗</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
