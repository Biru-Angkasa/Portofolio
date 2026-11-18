@extends('layouts.admin')
@section('title', 'Artikel blog')
@section('content')
    <div class="flex flex-wrap items-end justify-between gap-5"><div class="flex flex-col gap-3"><h1 class="section-title">Artikel blog</h1><p class="text-sm text-soft">Tulis cerita kamu. Draf hanya terlihat di admin.</p></div><a href="{{ route('admin.blog.create') }}" class="portfolio-button">+ Tulis artikel</a></div>
    <div class="portfolio-card flex flex-col p-6">
        @forelse ($posts as $post)
            <div class="flex flex-wrap items-center justify-between gap-4 border-b border-line py-5 first:pt-0 last:border-0 last:pb-0"><div class="flex flex-col gap-2"><p class="font-semibold">{{ $post->title }}</p><p class="text-xs text-soft">{{ $post->status->value === 'published' ? 'Terbit' : 'Draf' }} · {{ $post->category }}</p></div><a href="{{ route('admin.blog.edit', $post) }}" class="editor-secondary">Edit artikel ↗</a></div>
        @empty
            <p class="py-8 text-center text-sm text-soft">Ruang ini masih kosong. Mulai dengan artikel pertamamu.</p>
        @endforelse
    </div>
    {{ $posts->links() }}
@endsection
