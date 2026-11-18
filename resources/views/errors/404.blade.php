<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Halaman tidak ditemukan — Tian Maysa</title>
        <meta name="robots" content="noindex">
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css'])
        @endif
    </head>
    <body class="bg-paper text-ink antialiased">
        <main class="mx-auto flex min-h-screen w-full max-w-2xl flex-col items-start justify-center gap-6 px-5 py-16">
            <p class="text-sm font-semibold tracking-wide text-accent">404</p>
            <h1 class="text-3xl leading-tight font-semibold tracking-tight sm:text-4xl">Halaman yang dicari tidak ditemukan.</h1>
            <p class="text-base leading-8 text-soft">Tautan mungkin sudah berpindah, dihapus, atau alamat yang diketik keliru.</p>
            <div class="flex flex-wrap gap-4 pt-2">
                <a href="{{ url('/') }}" class="portfolio-button">Kembali ke beranda →</a>
            </div>
        </main>
    </body>
</html>
