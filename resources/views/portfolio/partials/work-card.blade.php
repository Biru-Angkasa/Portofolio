@php($image = $work->media->first(fn ($media) => $media->kind === \App\MediaKind::Image))
@php($link = $work->media->first(fn ($media) => $media->kind === \App\MediaKind::Link))
@php($workUrl = $work->detailUrl())
<li class="portfolio-card flex flex-col overflow-hidden">
    <a href="{{ $workUrl }}" aria-label="{{ $work->title }}" class="group flex flex-1 flex-col focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">
        @if ($image)
            <img src="{{ $image->fileUrl() }}" alt="{{ $image->label }}" loading="lazy" class="aspect-[16/10] w-full object-cover">
        @else
            <span class="flex aspect-[16/10] items-center justify-center bg-wash text-4xl text-accent" aria-hidden="true">↗</span>
        @endif
        <span class="flex flex-1 flex-col gap-3 p-6 pb-3"><span class="text-xs font-medium text-accent">{{ $work->category }}@if ($work->period) · {{ $work->period }}@endif</span><span class="text-xl font-semibold tracking-tight transition-colors group-hover:text-accent">{{ $work->title }}</span><span class="text-sm leading-7 text-soft">{{ $work->summary }}</span></span>
    </a>
    @if ($work->outcome)
        <p class="px-6 pb-3 text-sm leading-7 text-ink"><span class="font-semibold text-accent">Hasil:</span> {{ $work->outcome }}</p>
    @endif
    <div class="flex flex-wrap items-center justify-between gap-x-6 gap-y-3 px-6 pt-2 pb-6 text-xs font-semibold text-accent">
        <a href="{{ $workUrl }}" class="inline-flex items-center gap-2 hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Lihat dokumentasi <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M7 17 17 7M7 7h10v10" /></svg></a>
        @if ($link?->url)
            <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" aria-label="Buka {{ $link->label }} (tab baru)" class="inline-flex items-center gap-2 hover:text-ink focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Link <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="size-4"><path d="M7 17 17 7M7 7h10v10" /></svg></a>
        @endif
    </div>
</li>
