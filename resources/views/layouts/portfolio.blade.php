<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $portfolio['meta']['title'] }}</title>
        <meta name="description" content="{{ $portfolio['meta']['description'] }}">
        <meta name="robots" content="{{ $robots ?? 'index, follow' }}">
        <meta name="theme-color" content="#173e70">
        <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" sizes="32x32 64x64">
        <meta property="og:title" content="{{ $portfolio['meta']['title'] }}">
        <meta property="og:description" content="{{ $portfolio['meta']['description'] }}">
        <meta property="og:type" content="{{ $ogType ?? 'website' }}">
        <meta property="og:locale" content="id_ID">
        <meta property="og:url" content="{{ $canonicalUrl ?? url()->current() }}">
        @php($resolvedOgImage = $ogImage ?? $profile->photoUrl())
        @if ($resolvedOgImage)
            <meta property="og:image" content="{{ $resolvedOgImage }}">
        @endif
        <meta name="twitter:card" content="{{ $resolvedOgImage ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:title" content="{{ $portfolio['meta']['title'] }}">
        <meta name="twitter:description" content="{{ $portfolio['meta']['description'] }}">
        @if ($resolvedOgImage)
            <meta name="twitter:image" content="{{ $resolvedOgImage }}">
        @endif

        @yield('structured-data')

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="portfolio-site bg-paper text-ink antialiased">
        <a
            href="#konten"
            class="sr-only focus:not-sr-only focus:absolute focus:top-3 focus:left-3 focus:z-50 focus:bg-accent focus:px-4 focus:py-2 focus:text-paper focus:outline-none"
        >
            Loncat ke konten
        </a>

        @include('portfolio.partials.header')

        <main id="konten">
            @yield('content')
        </main>

        @include('portfolio.partials.footer')
    </body>
</html>
