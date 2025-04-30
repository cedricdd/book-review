@extends('layouts.main')

@section('title', 'Your Books')

@section('content')
    <div class="p-4">
        <h1 class="text-4xl font-bold mb-4">Your Books</h1>
        <p class="mb-4">Here are the books you have added.</p>

        @if ($books->count())
            <x-books.list :$books />
        @else
            <p class="text-center text-4xl text-gray-500">No books added yet. Be the first one!</p>
        @endif
    </div>
@endsection
