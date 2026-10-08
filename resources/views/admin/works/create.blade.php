@extends('layouts.admin')

@section('title', 'Bukti baru')

@section('content')
    <h1 class="text-3xl font-semibold">Bukti baru</h1>
    <form method="POST" action="{{ route('admin.works.store') }}" class="flex flex-col gap-4">
        @include('admin.works.partials.form')
    </form>
@endsection
