@extends('layouts.main')

@section('title', $book->title)

@section('content')
    <div class="p-4">
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
            <div class="flex justify-center items-center w-[300px]">
                <img src="{{ $book->cover }}" alt="{{ $book->title }}-cover" loading="lazy" class="rounded">
            </div>
            <div class="items-center">
                <h1 class="text-4xl font-bold mb-2">{{ $book->title }}</h1>
                <h2 class="text-xl font-bold mb-2">by <a class="hover:text-blue-500" href="{{ route('authors.show', $book->author) }}">{{ $book->author->name }}</a></h2>
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

        <div class="flex gap-2 justify-center items-center flex-wrap mb-6">
            @if (Auth::check() && !$userReview)
                <div class="flex justify-center">
                    <x-link-button color='blue' size='big' href="{{ route('reviews.create', $book->id) }}">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                            id="Pen--Streamline-Solar-Broken" height="24" width="24">
                            <desc>Pen Streamline Icon: https://streamlinehq.com</desc>
                            <path
                                d="m14.3601 4.07866 0.9268 -0.92688c1.5357 -1.53571 4.0256 -1.53571 5.5613 0 1.5357 1.5357 1.5357 4.02557 0 5.56128l-0.9269 0.92687m-5.5612 -5.56127s0.1158 1.96962 1.8537 3.70752c1.7379 1.73789 3.7075 1.85375 3.7075 1.85375m-5.5612 -5.56127L12 6.43872m7.9213 3.20121 -5.2606 5.26067L11.5613 18l-0.1612 0.1612c-0.5772 0.5771 -0.8657 0.8657 -1.1839 1.1139 -0.37538 0.2928 -0.78151 0.5438 -1.21122 0.7486 -0.36428 0.1736 -0.75146 0.3026 -1.5258 0.5607l-3.28126 1.0938m0 0 -0.80208 0.2674c-0.38106 0.127 -0.80118 0.0278 -1.08521 -0.2562 -0.28403 -0.2841 -0.3832 -0.7042 -0.25618 -1.0852l0.26736 -0.8021m1.87611 1.8761 -1.87611 -1.8761m0 0 1.09375 -3.2813c0.25812 -0.7743 0.38717 -1.1615 0.56078 -1.5258 0.2048 -0.4297 0.45579 -0.8358 0.74856 -1.2112 0.24818 -0.3182 0.53676 -0.6067 1.11392 -1.1839L8.5 9.93872"
                                stroke="#FFFFFF" stroke-linecap="round" stroke-width="1.5"></path>
                        </svg>
                        Add your review
                    </x-link-button>
                </div>
            @endif

            @can('update', $book)
                <div class="flex justify-center">
                    <x-link-button color='green' href="{{ route('books.edit', $book) }}">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="-0.75 -0.75 24 24" fill="none" stroke="#FFFFFF"
                            stroke-linecap="round" stroke-linejoin="round" id="Edit--Streamline-Tabler" height="24"
                            width="24">
                            <desc>Edit Streamline Icon: https://streamlinehq.com</desc>
                            <path
                                d="M6.5625 6.5625H5.625a1.875 1.875 0 0 0 -1.875 1.875v8.4375a1.875 1.875 0 0 0 1.875 1.875h8.4375a1.875 1.875 0 0 0 1.875 -1.875v-0.9375"
                                stroke-width="1.5"></path>
                            <path
                                d="M19.110937500000002 6.1734375a1.96875 1.96875 0 0 0 -2.7843750000000003 -2.7843750000000003L8.4375 11.25v2.8125h2.8125l7.8609374999999995 -7.8890625z"
                                stroke-width="1.5"></path>
                            <path d="m15 4.6875 2.8125 2.8125" stroke-width="1.5"></path>
                        </svg>
                        Edit Book
                    </x-link-button>
                </div>
            @endcan

            @can('destroy', $book)
                <form action="{{ route('books.destroy', $book) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-center">
                        <x-forms.button color='red'>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                id="Cleaning-Bucket--Streamline-Sharp-Material" height="24" width="24">
                                <desc>Cleaning Bucket Streamline Icon: https://streamlinehq.com</desc>
                                <path fill="#FFFFFF"
                                    d="m7 23 -2 -15h14l-2 15H7Zm1.325 -1.5H15.7l1.575 -12h-10.55l1.6 12Zm3.678 -5.75c0.76465 0 1.41365 -0.26735 1.947 -0.802 0.53335 -0.53485 0.8 -1.18415 0.8 -1.948v-1.75h-1.5V13c0 0.35415 -0.11935 0.651 -0.358 0.8905 -0.23885 0.23965 -0.53465 0.3595 -0.8875 0.3595 -0.353 0 -0.65035 -0.11985 -0.892 -0.3595 -0.24165 -0.2395 -0.3625 -0.53635 -0.3625 -0.8905v-1.75h-1.5V13c0 0.76385 0.26765 1.41315 0.803 1.948 0.53535 0.53465 1.18535 0.802 1.95 0.802ZM15 6.5c-0.33335 0 -0.625 -0.125 -0.875 -0.375s-0.375 -0.54585 -0.375 -0.8875c0 -0.341665 0.125 -0.633335 0.375 -0.875 0.25 -0.241665 0.54585 -0.3625 0.8875 -0.3625s0.63335 0.1215 0.875 0.3645c0.24165 0.243165 0.3625 0.538335 0.3625 0.8855 0 0.33335 -0.1215 0.625 -0.3645 0.875 -0.24315 0.25 -0.53835 0.375 -0.8855 0.375Zm-5 -1c-0.61665 0 -1.14585 -0.22085 -1.5875 -0.6625 -0.44165 -0.441665 -0.6625 -0.975 -0.6625 -1.6 0 -0.625 0.22085 -1.154165 0.6625 -1.5875 0.44165 -0.433335 0.975 -0.65 1.6 -0.65 0.625 0 1.15415 0.2175 1.5875 0.6525 0.43335 0.435 0.65 0.9675 0.65 1.5975 0 0.616665 -0.2175 1.145835 -0.6525 1.5875C11.1625 5.27915 10.63 5.5 10 5.5Z"
                                    stroke-width="0.5"></path>
                            </svg>
                            Delete Book
                        </x-forms.button>
                    </div>
                </form>
            @endcan
        </div>

        @if ($userReview)
            <div>
                <p class="text-2xl font-bold mb-4">Your Review</p>

                <x-reviews.card :review="$userReview" />
            </div>
        @endif


        @if ($reviews->count())
            <p class="text-2xl font-bold my-10">{{ $reviews->count() . ' ' . Str::plural('Review', $reviews->count()) }}</p>

            @foreach ($reviews as $review)
                <x-reviews.card :$review />
            @endforeach
        @elseif (!$userReview)
            <p class="text-center text-4xl text-gray-500 mt-10">No reviews yet. Be the first one!</p>
        @endif
    </div>

@endsection
