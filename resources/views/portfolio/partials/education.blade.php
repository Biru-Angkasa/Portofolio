<section id="pendidikan" class="border-t border-line">
    <div class="mx-auto grid w-full max-w-7xl gap-10 px-5 py-20 md:px-8 md:py-24 lg:grid-cols-[minmax(0,0.5fr)_minmax(0,1fr)] lg:gap-20">
        <div>
            <p class="mb-5 text-xs font-semibold tracking-[0.16em] uppercase text-accent">{{ $works->isNotEmpty() ? '04' : '03' }} / Pendidikan</p>
            <h2 class="font-serif text-4xl leading-tight tracking-tight md:text-5xl">Dasar yang terus dibangun.</h2>
        </div>
        <div class="grid gap-12 md:grid-cols-2">
            <div>
                <h3 class="mb-4 text-xs font-semibold tracking-[0.14em] uppercase text-soft">Pendidikan</h3>
                <ul class="border-t border-line">
                    @foreach ($portfolio['education'] as $item)
                        <li class="flex flex-col gap-1 border-b border-line py-5">
                            <p class="font-semibold">{{ $item['institution'] }}</p>
                            <p class="text-sm text-soft">{{ $item['program'] }}</p>
                            <p class="text-xs text-soft">{{ $item['period'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h3 class="mb-4 text-xs font-semibold tracking-[0.14em] uppercase text-soft">Sertifikasi</h3>
                <ul class="border-t border-line">
                    @foreach ($portfolio['certifications'] as $item)
                        <li class="flex justify-between gap-4 border-b border-line py-5">
                            <p class="font-semibold">{{ $item['name'] }}</p>
                            <p class="text-xs text-soft">{{ $item['year'] }}</p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>
