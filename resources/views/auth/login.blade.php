<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Masuk — Tian Maysa</title>
        <meta name="robots" content="noindex">
        @fonts
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-paper text-ink antialiased">
        <main class="mx-auto flex min-h-screen w-full max-w-md flex-col justify-center gap-8 px-5 py-16">
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-semibold">Masuk</h1>
                <p class="text-soft">Hanya untuk pemilik situs.</p>
            </div>
            <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
                @csrf
                <label class="flex flex-col gap-2 text-sm">
                    Email
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="border border-line bg-paper px-3 py-2 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
                </label>
                <label class="flex flex-col gap-2 text-sm">
                    Kata sandi
                    <input type="password" name="password" required autocomplete="current-password" class="border border-line bg-paper px-3 py-2 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
                </label>
                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" name="remember" value="1" class="focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
                    Ingat saya
                </label>
                @if ($errors->any())
                    <p class="text-sm text-accent">{{ $errors->first() }}</p>
                @endif
                <button type="submit" class="bg-accent px-4 py-3 text-sm font-semibold text-paper focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Masuk</button>
            </form>
        </main>
    </body>
</html>
