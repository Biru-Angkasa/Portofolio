<div id="keahlian" class="flex min-w-0 flex-col gap-6">
    <h3 class="text-xl font-semibold tracking-tight">{{ $portfolio['skills_title'] }}</h3>
    <dl class="flex flex-col gap-5">
        @foreach ($portfolio['skills'] as $skill)
            <div class="flex flex-col gap-3 border-b border-line pb-5 last:border-0 last:pb-0">
                <dt class="text-sm font-semibold text-accent">{{ $skill['name'] }}</dt>
                <dd class="flex flex-wrap gap-2">
                    @foreach ($skill['items'] as $item)
                        <span class="rounded-lg border border-line bg-paper px-3 py-1.5 text-xs text-soft">{{ $item }}</span>
                    @endforeach
                </dd>
            </div>
        @endforeach
    </dl>
</div>
