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

                @if ($reviews->count())
                    <div class="mt-6 flex gap-x-4 justify-center">
                        <x-star-rating :$rating />
                        <p>{{ $rating }}/5</p>
                    </div>
                @endif
            </div>
        </div>
        <p class="p-4 rounded my-4 border border-2 border-white/25">Summary: {{ $book->summary }}</p>

        @if ($userReview)
            <div>
                <p class="text-2xl font-bold mb-4">Your Review</p>

                <x-reviews.card :review="$userReview" />
            </div>
        @else
            <div class="flex justify-center">
                <x-link-button color='blue' size='big' href="{{ route('reviews.create', $book->id) }}">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" id="Pen--Streamline-Solar-Broken" height="24" width="24"><desc>Pen Streamline Icon: https://streamlinehq.com</desc><path d="m14.3601 4.07866 0.9268 -0.92688c1.5357 -1.53571 4.0256 -1.53571 5.5613 0 1.5357 1.5357 1.5357 4.02557 0 5.56128l-0.9269 0.92687m-5.5612 -5.56127s0.1158 1.96962 1.8537 3.70752c1.7379 1.73789 3.7075 1.85375 3.7075 1.85375m-5.5612 -5.56127L12 6.43872m7.9213 3.20121 -5.2606 5.26067L11.5613 18l-0.1612 0.1612c-0.5772 0.5771 -0.8657 0.8657 -1.1839 1.1139 -0.37538 0.2928 -0.78151 0.5438 -1.21122 0.7486 -0.36428 0.1736 -0.75146 0.3026 -1.5258 0.5607l-3.28126 1.0938m0 0 -0.80208 0.2674c-0.38106 0.127 -0.80118 0.0278 -1.08521 -0.2562 -0.28403 -0.2841 -0.3832 -0.7042 -0.25618 -1.0852l0.26736 -0.8021m1.87611 1.8761 -1.87611 -1.8761m0 0 1.09375 -3.2813c0.25812 -0.7743 0.38717 -1.1615 0.56078 -1.5258 0.2048 -0.4297 0.45579 -0.8358 0.74856 -1.2112 0.24818 -0.3182 0.53676 -0.6067 1.11392 -1.1839L8.5 9.93872" stroke="#FFFFFF" stroke-linecap="round" stroke-width="1.5"></path></svg>
                    Add your review
                </x-link-button>
            </div>
        @endif

        <p class="text-2xl font-bold my-10">{{ $reviews->count() . ' ' . Str::plural('Review', $reviews->count()) }}</p>

        @forelse($reviews as $review)
            <x-reviews.card :$review />
        @empty
            <p class="text-center text-4xl text-gray-500">No reviews yet. Be the first one!</p>
        @endforelse
    </div>

@endsection
