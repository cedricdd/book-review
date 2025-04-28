@extends('layouts.main')

@section('title', 'Edit Your Review')

@section('content')
    <div>
        <div class="flex justify-center items-center gap-x-4">
            <div class="flex justify-center items-center w-[300px]">
                <img src="{{ $book->cover }}" alt="{{ $book->title }}-cover" loading="lazy" class="rounded">
            </div>
            <div class="items-center">
                <h1 class="text-4xl font-bold mb-2">{{ $book->title }}</h1>
                <h2 class="text-xl font-bold mb-2">by {{ $book->author }}</h2>
                <p>Publish: {{ \Carbon\Carbon::parse($book->published_at)->format('Y-m-d') }}</p>
            </div>
        </div>
        <p class="p-4 rounded my-4 border border-2 border-white/25">Summary: {{ $book->summary }}</p>
    </div>

    <x-reviews.form action='Edit' :book="$review->book" :$review />
@endsection