{!! '<'.'?xml version="1.0" encoding="UTF-8"?'.'>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ rtrim(route('home'), '/').'/' }}</loc>
        @if ($homeLastModified)
            <lastmod>{{ $homeLastModified->toAtomString() }}</lastmod>
        @endif
    </url>
    @foreach ($works as $work)
        <url>
            <loc>{{ $work->detailUrl() }}</loc>
            <lastmod>{{ $work->updated_at->toAtomString() }}</lastmod>
        </url>
    @endforeach
    @foreach ($posts as $post)
        <url>
            <loc>{{ route('blog.show', $post) }}</loc>
            <lastmod>{{ $post->updated_at->toAtomString() }}</lastmod>
        </url>
    @endforeach
</urlset>
