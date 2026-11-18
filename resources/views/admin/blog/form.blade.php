@extends('layouts.admin')
@section('title', $post->exists ? 'Edit artikel' : 'Tulis artikel')
@section('content')
    <a href="{{ route('admin.blog.index') }}" class="w-fit text-sm text-accent">← Semua artikel</a>
    <h1 class="section-title">{{ $post->exists ? 'Edit artikel' : 'Tulis artikel baru' }}</h1>
    @if ($errors->any())<ul role="alert" class="rounded-xl bg-red-50 p-5 text-sm text-red-800">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
    <form method="POST" action="{{ $post->exists ? route('admin.blog.update', $post) : route('admin.blog.store') }}" enctype="multipart/form-data" class="portfolio-card flex flex-col gap-5 p-6 md:p-8">
        @csrf @if ($post->exists) @method('PUT') @endif
        <label class="flex flex-col gap-2 text-sm">Judul<input type="text" name="title" value="{{ old('title', $post->title) }}" required maxlength="160" class="editor-input"></label>
        <label class="flex flex-col gap-2 text-sm">Kategori<input type="text" name="category" value="{{ old('category', $post->category) }}" required maxlength="80" class="editor-input"></label>
        <label class="flex flex-col gap-2 text-sm">Ringkasan<textarea name="excerpt" required maxlength="2000" rows="3" class="editor-input">{{ old('excerpt', $post->excerpt) }}</textarea></label>
        <label class="flex flex-col gap-2 text-sm">Isi artikel<textarea name="body" required maxlength="100000" rows="16" class="editor-input">{{ old('body', $post->body) }}</textarea><span class="text-xs text-soft">Mendukung teks biasa dan Markdown ringan: <code>## Subjudul</code>, <code>- daftar</code>, <code>**tebal**</code>, <code>`kode`</code>, atau <code>[tautan](https://...)</code>.</span></label>
        @if ($post->cover_path)<img src="{{ $post->coverUrl() }}" alt="{{ $post->title }}" class="w-full max-w-sm rounded-xl"><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remove_cover" value="1" @checked(old('remove_cover'))> Hapus sampul</label>@endif
        <label class="flex flex-col gap-2 text-sm">Sampul (opsional; JPEG, PNG, WebP; maksimal 5 MB)<input type="file" name="cover" accept="image/jpeg,image/png,image/webp" class="editor-input"></label>
        <label class="flex flex-col gap-2 text-sm">Status<select name="status" class="editor-input"><option value="draft" @selected(old('status', $post->status?->value ?? 'draft') === 'draft')>Draf — simpan dulu</option><option value="published" @selected(old('status', $post->status?->value ?? 'draft') === 'published')>Terbit — tampil di situs</option></select></label>
        <button type="submit" class="portfolio-button w-fit">Simpan artikel ↗</button>
    </form>
    @if ($post->exists)
        <form method="POST" action="{{ route('admin.blog.destroy', $post) }}" data-confirm-delete="Hapus artikel ini? Artikel dan sampulnya akan dihapus.">
            @csrf @method('DELETE')
            <button type="submit" class="w-fit text-sm text-red-700">Hapus artikel</button>
        </form>
    @endif
@endsection
