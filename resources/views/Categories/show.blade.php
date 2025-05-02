@extends('layouts.main')

@section('title', $category->name . "'s Books")

@section('content')
    <x-header-title>{{ $category->name }}'s Books</x-header-title>

    @if ($books->isEmpty())
        <p class="text-4xl text-center">No books available in this category.</p>
    @else
        <x-nav-sorting type='book' />

        <x-books.list :$books />

        {{ $books->links() }}
    @endif
@endsection
