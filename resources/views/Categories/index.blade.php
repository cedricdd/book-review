@extends('layouts.main')

@section('title', 'Categories')

@section('content')
    <x-header-title>Book Categories</x-header-title>

    @foreach($categories as $name => [, $cover, $slug, $booksCount])
        <x-card>
            <a href="{{ route('categories.show', $slug) }}" class="w-full flex flex-col sm:flex-row justify-between items-center gap-2">
                <div>
                    <img src="{{ $cover }}" alt="{{ $name}}-cover" class="w-full h-40 rounded">
                </div>
                <h2 class="text-4xl">{{ $name }}</h2>
                <div>
                    <p class="font-bold">{{ $booksCount }} Books</p>
                </div>
            </a>
        </x-card>
    @endforeach
@endsection