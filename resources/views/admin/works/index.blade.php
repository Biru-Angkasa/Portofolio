@extends('layouts.admin')

@section('title', 'Bukti kerja')

@section('content')
    <section class="flex flex-col gap-4">
        <h1 class="text-3xl font-semibold">Foto profil</h1>
        <p class="text-sm leading-relaxed text-soft">Sebelum mengunggah, samarkan nama pelanggan, nomor seri, alamat IP, wajah orang lain, dan data perusahaan yang tidak boleh dipublikasikan. CV asli dan nomor telepon tidak diunggah dari sini.</p>
        @if (session('status'))
            <p role="status" class="text-sm">
                {{ session('status') }}
                <a href="{{ route('home') }}" class="border-b border-accent text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent" target="_blank" rel="noopener noreferrer">Lihat hasil di portofolio</a>
            </p>
        @endif
        <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
            <div class="aspect-[3/4] w-40 overflow-hidden border border-line">
                @if ($profile->photo_path)
                    <img src="{{ $profile->photoUrl() }}" alt="{{ $profile->photo_alt }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full items-center justify-center text-2xl font-semibold" aria-hidden="true">TM</div>
                @endif
            </div>
            <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="flex flex-1 flex-col gap-3">
                @csrf
                <label class="flex flex-col gap-2 text-sm">
                    Gambar JPEG, PNG, atau WebP, maksimal 5 MB
                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required class="text-sm">
                </label>
                <label class="flex flex-col gap-2 text-sm">
                    Teks alternatif
                    <input type="text" name="alt" value="{{ old('alt', $profile->photo_alt) }}" required maxlength="160" class="border border-line bg-paper px-3 py-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">
                </label>
                @error('photo')<p class="text-sm text-accent">{{ $message }}</p>@enderror
                @error('alt')<p class="text-sm text-accent">{{ $message }}</p>@enderror
                <button type="submit" class="w-fit bg-accent px-4 py-2 text-sm font-semibold text-paper focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Simpan foto</button>
            </form>
        </div>
        @if ($profile->photo_path)
            <form method="POST" action="{{ route('admin.profile.destroy') }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Hapus foto profil</button>
            </form>
        @endif
    </section>

    <section class="flex flex-col gap-4">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-2xl font-semibold">Bukti kerja</h2>
            <a href="{{ route('admin.works.create') }}" class="bg-accent px-4 py-2 text-sm font-semibold text-paper focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Tambah</a>
        </div>
        @if ($works->isEmpty())
            <p class="text-soft">Belum ada bukti. Situs publik tidak menampilkan galeri kosong.</p>
        @else
            <ul class="flex flex-col">
                @foreach ($works as $work)
                    <li class="flex flex-col gap-2 border-t border-line py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="font-semibold">{{ $work->title }}</p>
                            <p class="text-sm text-soft">{{ $work->status->value === 'published' ? 'Terbit' : 'Draf' }} · {{ $work->media_count }} media</p>
                        </div>
                        <div class="flex flex-wrap gap-3 text-sm">
                            <form method="POST" action="{{ route('admin.works.move', [$work, 'naik']) }}">@csrf<button type="submit" class="focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Naik</button></form>
                            <form method="POST" action="{{ route('admin.works.move', [$work, 'turun']) }}">@csrf<button type="submit" class="focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Turun</button></form>
                            <a href="{{ route('admin.works.edit', $work) }}" class="focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Edit</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </section>
@endsection
