<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'Admin') — Admin portofolio</title><meta name="robots" content="noindex">
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-wash text-ink antialiased">
        <header class="border-b border-line bg-paper">
            <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center justify-between gap-4 px-5 py-5">
                <a href="{{ route('admin.content.index') }}" class="text-lg font-semibold tracking-tight text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Studio portofolio<span class="text-ink">.</span></a>
                <div class="flex items-center gap-5 text-sm"><a href="{{ route('home') }}" class="text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent" target="_blank" rel="noopener noreferrer">Lihat situs ↗</a><form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="text-soft focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Keluar</button></form></div>
            </div>
            <nav class="mx-auto flex w-full max-w-6xl flex-wrap gap-3 px-5 pb-4 text-sm" aria-label="Admin">
                <a href="{{ route('admin.content.index') }}" @class(['rounded-lg px-4 py-2', 'bg-accent text-white' => request()->routeIs('admin.content.*'), 'bg-wash text-soft' => !request()->routeIs('admin.content.*')])>Isi situs</a>
                <a href="{{ route('admin.works.index') }}" @class(['rounded-lg px-4 py-2', 'bg-accent text-white' => request()->routeIs('admin.works.*'), 'bg-wash text-soft' => !request()->routeIs('admin.works.*')])>Proyek & foto</a>
                <a href="{{ route('admin.blog.index') }}" @class(['rounded-lg px-4 py-2', 'bg-accent text-white' => request()->routeIs('admin.blog.*'), 'bg-wash text-soft' => !request()->routeIs('admin.blog.*')])>Artikel blog</a>
                <a href="{{ route('admin.account.edit') }}" @class(['rounded-lg px-4 py-2', 'bg-accent text-white' => request()->routeIs('admin.account.*'), 'bg-wash text-soft' => !request()->routeIs('admin.account.*')])>Pengaturan akun</a>
            </nav>
        </header>
        <main class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-5 py-10">
            @if (session('status') && !request()->routeIs('admin.works.index'))
                <p role="status" class="rounded-xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-accent">{{ session('status') }} @unless(request()->routeIs('admin.account.*'))<a href="{{ route('home') }}" class="underline" target="_blank" rel="noopener noreferrer">Lihat hasil ↗</a>@endunless</p>
            @endif
            @yield('content')
        </main>
    </body>
</html>
