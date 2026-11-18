@extends('layouts.admin')
@section('title', 'Edit '.$definition['label'])
@section('content')
    <a href="{{ route('admin.content.index') }}" class="w-fit text-sm text-accent">← Semua menu</a>
    <div class="flex flex-col gap-3"><h1 class="section-title">Edit {{ $definition['label'] }}</h1><p class="max-w-2xl leading-7 text-soft">{{ $definition['description'] }}</p></div>
    <nav class="flex flex-wrap gap-2" aria-label="Menu yang dapat diedit">
        @foreach (config('portfolio-editor') as $key => $menu)
            <a href="{{ route('admin.content.edit', $key) }}" @class(['rounded-full border px-4 py-2 text-xs font-medium', 'border-accent bg-accent text-white' => $section === $key, 'border-line bg-paper text-soft' => $section !== $key])>{{ $menu['label'] }}</a>
        @endforeach
    </nav>
    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-5 text-sm text-red-800"><p class="mb-2 font-semibold">Periksa kolom berikut:</p><ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form method="POST" action="{{ route('admin.content.update', $section) }}" enctype="multipart/form-data" class="flex flex-col gap-7">
        @csrf @method('PUT')
        <div class="portfolio-card grid gap-5 p-6 md:grid-cols-2 md:p-8">
            @foreach ($definition['fields'] as $key => $field)
                <div class="{{ $field['type'] === 'textarea' ? 'md:col-span-2' : '' }}">@include('admin.content.field', ['field' => $field, 'inputName' => "content[{$key}]", 'errorKey' => "content.{$key}", 'value' => $portfolio[$key] ?? ''])</div>
            @endforeach
        </div>
        @foreach ($definition['collections'] as $collectionKey => $collection)
            <section data-repeater class="portfolio-card flex flex-col gap-5 p-6 md:p-8">
                <div class="flex flex-wrap items-center justify-between gap-4"><h2 class="text-lg font-semibold">{{ $collection['label'] }}</h2><button type="button" data-add-row class="editor-secondary">+ Tambah item</button></div>
                <p class="text-xs text-soft">Urutkan dengan Naik / Turun. Hapus semua item jika ingin mengosongkan bagian ini.</p>
                @isset($collection['help'])<p class="text-sm leading-7 text-soft">{{ $collection['help'] }}</p>@endisset
                <div data-rows class="flex flex-col gap-4">
                    @php
                        $rows = old('content.'.$collectionKey, $portfolio[$collectionKey] ?? []);
                        $rows = is_array($rows) ? $rows : [];
                    @endphp
                    @foreach ($rows as $index => $row)
                        @continue(!is_array($row))
                        @include('admin.content.row', compact('collectionKey', 'collection', 'index', 'row'))
                    @endforeach
                </div>
                <p data-empty class="rounded-xl border border-dashed border-line p-6 text-center text-sm text-soft">Belum ada item. Klik Tambah item untuk mulai mengisi.</p>
                <template data-row-template>@include('admin.content.row', ['collectionKey' => $collectionKey, 'collection' => $collection, 'index' => '__INDEX__', 'row' => []])</template>
            </section>
        @endforeach
        <div class="sticky bottom-4 z-10 flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-line bg-paper/95 p-4 shadow-sm backdrop-blur-sm"><p class="text-xs text-soft">Simpan untuk menampilkan perubahan di situs.</p><button type="submit" class="portfolio-button">Simpan perubahan ↗</button></div>
    </form>
    @if ($section === 'beranda')
        <section class="portfolio-card flex flex-col gap-5 p-6 md:p-8">
            <h2 class="text-lg font-semibold">Foto profil</h2>
            @include('admin.profile-photo-form', ['profile' => $profile])
            @if ($profile->photo_path)<form method="POST" action="{{ route('admin.profile.destroy') }}">@csrf @method('DELETE')<button type="submit" class="text-sm text-red-700">Hapus foto profil</button></form>@endif
        </section>
    @elseif ($section === 'portofolio')
        <a href="{{ route('admin.works.index') }}" class="editor-secondary w-fit">Kelola proyek, foto & dokumen ↗</a>
    @elseif ($section === 'blog')
        <a href="{{ route('admin.blog.index') }}" class="editor-secondary w-fit">Kelola artikel blog ↗</a>
    @endif
@endsection
