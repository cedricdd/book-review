@extends("layouts.main")

@section("content")
    <h1 class="title text-left">{{ $book->title }}</h1>
    <div class="text-2xl">by <span class="italic">{{ $book->author }}</span></div>

    <div class="w-full text-center mt-8">
        <a href="{{ route("books.reviews.create", $book) }}" class="btn">Add A Review</a>
        <a href="{{ route("books.index") }}" class="btn">Back To Book List</a>
    </div>

    <div class="mt-14 flex items-center">
        <div class="text-3xl font-bold grow">Reviews:</div>
        <div class="flex items-center"><span class="mr-3 mb-1">{{ $reviews->count() }} {{ Str::plural("Review", $reviews->count()) }}</span><x-star-rating :rating="$reviews->avg('rating')" /></div>
    </div>

    @forelse ($reviews as $review)
        <div class="card flex-col">
            <div class="w-full mb-3">
                <div class="float-left"><x-star-rating :rating="$review->rating" /></div>
                <div class="float-right">{{ $review->updated_at->format("M j, Y") }}</div>
            </div>
            <div class="self-start text-justify">{{ $review->review }}</div>
            @if(!empty($review->ip_address) && $ip_address == $review->ip_address)
            <div class="mt-4 w-full flex justify-end gap-1">
                <a href="{{ route('books.reviews.edit', [$book, $review]) }}" class="btn w-28">Edit</a>
                <form action="{{ route('books.reviews.destroy', [$book, $review]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn w-28">Delete</button>
                </form>
            </div>
            @endif
        </div>
    @empty
        <div class="card">
            There are no review for this book at this time.
        </div>
    @endforelse
@endsection