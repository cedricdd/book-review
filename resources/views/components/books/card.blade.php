@props(['book', 'ratedBooks' => collect()])

<x-card>
    <a href={{ route('books.show', $book->id) }}>
        <img src="{{ $book->cover }}" alt="{{ $book->title }}-cover" loading="lazy"
            class="w-40 sm:w-30 object-cover rounded-md mr-4">
    </a>
    <div class="flex-1">
        <a href={{ route('books.show', $book->id) }} class="text-2xl group-hover:text-blue-500">{{ $book->title }}</a>
        <p class="text-gray-400 mt-1">by {{ $book->author->name }}</p>
        <p class="mt-4">{{ $book->summary }}</p>
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
