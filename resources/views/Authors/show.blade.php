@extends('layouts.main')

@section('title', $author->name)

@section('content')
    <div class="w-full space-y-6 my-6">
        <div class="text-center text-4xl">{{ $author->name }}</div>
        <div class="flex flex-x-2 justify-between items-center">
            <p><b>Birthdate:</b> {{ $author->date_of_birth }}</p>
            <p><b>Country:</b> {{ $author->country }}</p>
        </div>
        <div class="text-justify">
            {{ $author->biography }}
        </div>
        <div>
            <b>Website:</b> <a href="{{ $author->website }}" target="_blank"
                class="text-blue-500 hover:underline">{{ $author->website }}</a>
        </div>
    </div>

    @if ($books->count())
        <div class="text-xl mb-4">Books by {{ $author->name }}:</div>
        <x-books.list :$books />
    @else
        <p class="text-center">No books found for this author.</p>
    @endforelse

@endsection
