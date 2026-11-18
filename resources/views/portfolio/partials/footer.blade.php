<footer class="bg-[#0d213c] text-white">
    <div class="mx-auto grid w-full max-w-7xl gap-10 px-5 py-14 md:grid-cols-[1.2fr_1fr_1fr] md:px-8 md:py-20">
        <div class="flex flex-col items-start gap-4"><p class="text-3xl font-semibold tracking-tight">{{ $portfolio['name'] }}</p><p class="text-sm text-slate-300">{{ $portfolio['role'] }}</p><a href="mailto:{{ $portfolio['email'] }}" class="max-w-full text-sm break-all text-slate-300 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">{{ $portfolio['email'] }}</a><p class="text-sm text-slate-300">{{ $portfolio['location'] }}</p></div>
        <div class="flex flex-col items-start gap-4"><h2 class="text-base font-semibold">Jelajahi</h2>@foreach ($navigation as $key => $menu)<a href="{{ route('home') }}#{{ $key === 'portofolio' ? 'bukti' : $key }}" class="text-sm text-slate-300 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">{{ $menu['label'] }}</a>@endforeach</div>
        <div class="flex flex-col items-start gap-4">
            <h2 class="text-base font-semibold">Terhubung</h2>
            <div class="flex flex-wrap gap-3">
                @include('portfolio.partials.social-links', ['socials' => $portfolio['socials'], 'variant' => 'dark'])
            </div>
        </div>
    </div>
    <div class="mx-auto flex w-full max-w-7xl flex-wrap items-center justify-between gap-4 border-t border-white/15 px-5 py-6 text-xs text-slate-400 md:px-8"><p>© {{ date('Y') }} {{ $portfolio['name'] }}</p><a href="{{ route('home') }}#beranda" class="text-slate-300 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white">Kembali ke atas ↑</a></div>
</footer>
