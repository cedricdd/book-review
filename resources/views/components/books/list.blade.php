@props(['books'])

@php
    $ratedBooks = collect();
    if (Auth::check() && $books->count()) {
        $ratedBooks = Auth::user()->reviews()->whereIn('book_id', $books->pluck('id'))->pluck('rating', 'book_id');
    }
@endphp

@foreach ($books as $book)
    <x-card>
        <a href={{ route('books.show', $book->id) }}>
            <img src="{{ $book->cover }}" alt="{{ $book->title }}-cover" loading="lazy"
                class="w-40 sm:w-30 object-cover rounded-md mr-4">
        </a>
        <div class="flex-1">
            <a href={{ route('books.show', $book->id) }}
                class="text-2xl group-hover:text-blue-500">{{ $book->title }}</a>
            <p class="text-gray-400 mt-1">by <a
                    href="{{ route('authors.show', $book->author) }}">{{ $book->author->name }}</a></p>
            <p class="mt-4">{{ $book->summary }}</p>

            <div class="flex gap-2 items-center flex-wrap mt-6">
                @foreach($book->categories as $category)
                    <x-link-button :color="$loop->even ? 'blue' : 'green'" size='small' href="{{ route('categories.show', $category) }}">
                        {{ $category->name }}
                    </x-link-button>
                @endforeach
            </div>
        </div>
        <div class="w-[135px] text-center">
            @if ($book->reviews_count != 0)
                <x-star-rating :rating="$book->rating" />
                <p>out of {{ $book->reviews_count }} {{ Str::plural('review', $book->reviews_count) }}</p>
                @if ($ratedBooks->has($book->id))
                    <p class="text-gray-400">Your Rating: {{ $ratedBooks->get($book->id) }}</p>
                @endif
            @else
                <div>
                    <p class="font-bold">No reviews yet</p>
                    <p>Be the first!</p>
                </div>
            @endif
        </div>
    </x-card>
@endforeach
