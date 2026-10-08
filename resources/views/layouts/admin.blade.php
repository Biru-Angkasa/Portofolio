<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Admin')</title>
        <meta name="robots" content="noindex">
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-paper text-ink antialiased">
        <header class="border-b border-line">
            <div class="mx-auto flex w-full max-w-3xl items-center justify-between gap-4 px-5 py-4">
                <a href="{{ route('admin.works.index') }}" class="font-semibold focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Admin</a>
                <div class="flex items-center gap-4 text-sm">
                    <a href="{{ route('home') }}" class="focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Lihat situs</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Keluar</button>
                    </form>
                </div>
            </div>
        </header>
        <main class="mx-auto flex w-full max-w-3xl flex-col gap-10 px-5 py-10">
            @yield('content')
        </main>
    </body>
</html>
