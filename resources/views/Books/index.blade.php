@extends('layouts.main')

@section('title', 'Book Reviews')

@section('content')
    <div class="container mx-auto p-4">
        <x-header-title>Books</x-header-title>

        <x-search-form :$term />

        <x-nav-sorting type="book" />

        <div>
            @forelse($books as $book)
                <x-books.card :$book />
            @empty
                @empty($term)
                    <p class="text-center text-4xl text-gray-500">No books available.</p>
                @else
                    <div class="text-center mt-10">
                        <h2 class="text-2xl font-bold">No books found</h2>
                        <p class="text-gray-500">Try searching for something else.</p>
                    </div>
                @endempty
            @endforelse

            {{ $books->links() }}
        </div>
    </div>

@endsection
