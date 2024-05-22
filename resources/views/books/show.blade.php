@extends("layouts.main")

@section("content")
    <h1 class="title text-left">{{ $book->title }}</h1>
    <div class="text-2xl">by <span class="italic">{{ $book->author }}</span></div>
    <div class="mt-10 flex items-center">
        <div class="text-3xl font-bold grow">Reviews:</div>
        <div class="flex items-center"><span class="mr-3 mb-1">{{ $reviews->count() }} {{ Str::plural("Review", $reviews->count()) }}</span><x-star-rating :rating="$reviews->avg('rating')" /></div>
    </div>

    @forelse ($reviews as $review)
        <div class="card flex-col">
            <div class="w-full mb-3">
                <div class="float-left"><x-star-rating :rating="$review->rating" /></div>
                <div class="float-right">{{ $review->updated_at->format("M j, Y") }}</div>
            </div>
            <div>
                {{ $review->review }}
            </div>
        </div>
    @empty
        <div class="card">
            There are no review for this book at this time.
        </div>
    @endforelse
@endsection