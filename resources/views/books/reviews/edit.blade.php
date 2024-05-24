@extends("layouts.main")

@section("content")
    <h1 class="title">{{ $title }}</h1>

    @include('books.reviews.form')
@endsection