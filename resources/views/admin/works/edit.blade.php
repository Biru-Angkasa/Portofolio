@extends('layouts.admin')

@section('title', 'Edit bukti')

@section('content')
    <h1 class="text-3xl font-semibold">Edit bukti</h1>
    <p class="text-sm leading-relaxed text-soft">Sebelum mengunggah, samarkan nama pelanggan, nomor seri, alamat IP, wajah orang lain, dan data perusahaan yang tidak boleh dipublikasikan. Jangan unggah CV asli atau nomor telepon.</p>

    <form method="POST" action="{{ route('admin.works.update', $work) }}" class="flex flex-col gap-4">
        @csrf
        @method('PUT')
        @include('admin.works.partials.form')
    </form>

    <section class="flex flex-col gap-4 border-t border-line pt-8">
        <h2 class="text-2xl font-semibold">Media</h2>
        @if ($work->media->isEmpty())
            <p class="text-soft">Belum ada gambar, PDF, atau tautan.</p>
        @else
            <ul class="flex flex-col">
                @foreach ($work->media as $media)
                    <li class="flex items-start justify-between gap-4 border-t border-line py-4">
                        <div class="flex flex-col gap-2">
                            @if ($media->kind === \App\MediaKind::Image)
                                <img src="{{ $media->fileUrl() }}" alt="{{ $media->label }}" class="aspect-[4/3] w-40 object-cover">
                            @endif
                            <p class="font-semibold">{{ $media->label }}</p>
                            <p class="text-sm text-soft">{{ $media->kind->value }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.media.destroy', [$work, $media]) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Hapus</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        @endif

        <form method="POST" action="{{ route('admin.media.store', $work) }}" enctype="multipart/form-data" class="flex flex-col gap-3">
            @csrf
            <label class="flex flex-col gap-2 text-sm">
                Jenis
                <select name="kind" class="border border-line bg-paper px-3 py-2">
                    <option value="image" @selected(old('kind') === 'image')>Gambar</option>
                    <option value="pdf" @selected(old('kind') === 'pdf')>PDF</option>
                    <option value="link" @selected(old('kind') === 'link')>Tautan HTTPS</option>
                </select>
            </label>
            <label class="flex flex-col gap-2 text-sm">
                Label atau teks alternatif
                <input type="text" name="label" value="{{ old('label') }}" required maxlength="160" class="border border-line bg-paper px-3 py-2">
            </label>
            <label class="flex flex-col gap-2 text-sm">
                Caption, jika perlu
                <input type="text" name="caption" value="{{ old('caption') }}" maxlength="500" class="border border-line bg-paper px-3 py-2">
            </label>
            <label class="flex flex-col gap-2 text-sm">
                Berkas gambar atau PDF
                <input type="file" name="file" accept="image/jpeg,image/png,image/webp,application/pdf">
            </label>
            <label class="flex flex-col gap-2 text-sm">
                URL HTTPS
                <input type="url" name="url" value="{{ old('url') }}" placeholder="https://" class="border border-line bg-paper px-3 py-2">
            </label>
            @if ($errors->any())
                <ul class="flex flex-col gap-1 text-sm text-accent">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif
            <button type="submit" class="w-fit border border-ink px-4 py-2 text-sm font-semibold focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Lampirkan</button>
        </form>
    </section>

    <form method="POST" action="{{ route('admin.works.destroy', $work) }}">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-sm text-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent">Hapus bukti ini</button>
    </form>
@endsection
