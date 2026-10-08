<section id="pengalaman">
    <div class="mx-auto w-full max-w-7xl px-5 py-20 md:px-8 md:py-28">
        <div class="grid gap-8 pb-14 lg:grid-cols-[minmax(0,0.6fr)_minmax(0,1fr)] lg:gap-20">
            <div>
                <p class="mb-5 text-xs font-semibold tracking-[0.16em] uppercase text-accent">01 / Pengalaman</p>
                <h2 class="max-w-md font-serif text-4xl leading-tight tracking-tight md:text-5xl">Pekerjaan nyata, dengan tanggung jawab yang jelas.</h2>
            </div>
            <p class="max-w-xl self-end text-lg leading-relaxed text-soft">{{ $portfolio['about'] }}</p>
        </div>

        <ol class="border-t border-line">
            @foreach ($portfolio['experiences'] as $experience)
                <li class="grid gap-5 border-b border-line py-10 md:grid-cols-[3rem_minmax(9rem,0.45fr)_minmax(0,1fr)] md:gap-8 md:py-12">
                    <p class="text-xs font-semibold tracking-[0.12em] text-accent">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</p>
                    <div class="flex flex-col gap-2">
                        <p class="text-sm leading-relaxed text-soft">{{ $experience['period'] }}</p>
                        <h3 class="text-2xl font-semibold tracking-tight">{{ $experience['organization'] }}</h3>
                        <p class="text-sm leading-relaxed">{{ $experience['role'] }}</p>
                    </div>
                    <ul class="flex max-w-2xl list-disc flex-col gap-3 pl-5 leading-relaxed text-soft marker:text-accent">
                        @foreach ($experience['points'] as $point)
                            <li>{{ $point }}</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ol>
    </div>
</section>
