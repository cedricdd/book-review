@extends('layouts.main')

@section('title', $book->title)

@section('content')
    <div class="p-4">
        <div class="flex justify-center items-center gap-x-4">
            <div class="flex justify-center items-center w-[300px]">
                <img src="{{ $book->cover_image }}" alt="{{ $book->title }}-cover" loading="lazy" class="rounded">
            </div>
            <div class="items-center">
                <h1 class="text-4xl font-bold mb-2">{{ $book->title }}</h1>
                <h2 class="text-xl font-bold mb-2">by {{ $book->author }}</h2>
                <p>Publish: {{ \Carbon\Carbon::parse($book->published_at)->format('Y-m-d') }}</p>
                
                @if($book->reviews->count())
                <div class="mt-6 flex gap-x-4 justify-center">
                    <x-star-rating :$rating />
                    <p>{{ $rating }}/5</p>
                </div>
                @endif
            </div>
        </div>
        <p class="p-4 rounded my-4 border border-2 border-white/25">Summary: {{ $book->summary }}</p>

        <p class="text-2xl font-bold my-10">{{ $book->reviews->count() . " " . Str::plural('Review', $book->reviews->count()) }}</p>

        @forelse($book->reviews as $review)
            <x-reviews.card :$review />
        @empty
            <p class="text-center text-4xl text-gray-500">No reviews yet. Be the first one!</p>
        @endforelse
    </div>
    
@endsection
