<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $portfolio['meta']['title'] }}</title>
        <meta name="description" content="{{ $portfolio['meta']['description'] }}">
        <meta name="theme-color" content="#f6f3ec">
        <link rel="canonical" href="{{ url('/') }}">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <meta property="og:title" content="{{ $portfolio['meta']['title'] }}">
        <meta property="og:description" content="{{ $portfolio['meta']['description'] }}">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="id_ID">
        <meta property="og:url" content="{{ url('/') }}">
        @if ($profile->photo_path)
            <meta property="og:image" content="{{ $profile->photoUrl() }}">
        @endif

        @fonts

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-paper text-ink antialiased">
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
