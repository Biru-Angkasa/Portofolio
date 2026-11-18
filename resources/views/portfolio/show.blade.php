@extends('layouts.portfolio')

@section('structured-data')
    @include('portfolio.partials.profile-schema')
@endsection

@section('content')
    @include('portfolio.partials.hero')
    @include('portfolio.partials.about')
    @include('portfolio.partials.experience')
    @include('portfolio.partials.education')
    @include('portfolio.partials.evidence')
    @if ($posts->isNotEmpty())
        @include('portfolio.partials.blog')
    @endif
    @include('portfolio.partials.contact')
@endsection
