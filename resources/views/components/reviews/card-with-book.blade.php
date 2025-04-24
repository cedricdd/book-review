<div class="bg-white/10 rounded-lg p-4 mb-4 shadow-md hover:shadow-lg hover:bg-white/15 border-1 border-transparent hover:border-blue-500 transition duration-300 p-4 shadow/20 shadow-white flex gap-x-2">
    <a class="w-[150px] shrink-0 flex flex-col justify-center items-center" href={{ route('books.show', $review->book) }}>
        <img src="{{ $review->book->cover_image }}" alt="{{ $review->book->title }}-cover" loading="lazy" class="w-20 h-28 rounded-md">
        <p class="text-center">{{ $review->book->title }}</p>
    </a>
    <div class="flex-1">
        <div class="flex justify-between">
            <x-star-rating :rating="$review->rating" />
            <p class="text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</p>
        </div>
        <p class="my-4">{{ $review->review}}</p>
    </div>
</div>