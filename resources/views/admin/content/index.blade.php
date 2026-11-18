@extends('layouts.admin')
@section('title', 'Isi situs')
@section('content')
    <div class="flex flex-col gap-3"><p class="section-label">Ruang edit kamu</p><h1 class="section-title">Isi situs, sesukamu.</h1><p class="max-w-2xl leading-7 text-soft">Pilih bagian yang ingin diubah. Isi teks, tambahkan item, lalu simpan. Perubahan langsung tampil di portofolio.</p></div>
    <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($sections as $key => $definition)
            <a href="{{ route('admin.content.edit', $key) }}" class="portfolio-card flex flex-col gap-4 p-6 transition-colors hover:border-accent focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-accent"><span class="text-xs font-medium text-soft">Menu {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span class="text-xl font-semibold">{{ $definition['label'] }} <span class="float-right text-accent" aria-hidden="true">↗</span></span><span class="text-sm leading-7 text-soft">{{ $definition['description'] }}</span></a>
        @endforeach
    </div>
@endsection
