@props(['author'])

<x-card>
    <div class="w-full space-y-4">
        <div>
            <a href="{{ route('authors.show', $author) }}"
                class="text-2xl group-hover:text-blue-500">{{ $author->name }}</a>
        </div>
        <div class="flex flex-x-2 justify-between items-center">
            <p><b>Birthdate:</b> {{ $author->date_of_birth }}</p>
            <p><b>Country:</b> {{ $author->country }}</p>
        </div>
        <div class="text-justify"">
            {{ $author->biography }}
        </div>
        <div>
            <b>Website:</b> <a href="{{ $author->website }}" target="_blank"
                class="text-blue-500 hover:underline">{{ $author->website }}</a>
        </div>
        <div>
            <b>Books:</b>
            <div class="flex flex-wrap gap-2 mt-2 text-center">
                @foreach ($author->books as $book)
                    <a href="{{ route('books.show', $book) }}" class="bg-white/10 hover:bg-white/15 px-2 py-1 rounded flex-grow">
                        {{ $book->title }}
                        <x-star-rating :rating='$book->reviews_avg_rating' />
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-card>
