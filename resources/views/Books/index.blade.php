@extends('layouts.main')

@section('title', 'Book Reviews')

@section('content')
    <div class="container mx-auto p-4">
        <h1 class="text-5xl font-bold mb-4 text-center">Books</h1>
        <div>
            @forelse($books as $book)
                <x-books.card :$book />
            @empty
                <p class="text-center text-4xl text-gray-500">No books available.</p>
            @endforelse

            {{ $books->links() }}
        </div>
    </div>
    
@endsection