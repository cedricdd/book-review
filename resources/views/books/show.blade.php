@extends("layouts.main")

@section("content")
    <h1 class="title text-left">{{ $book->title }}</h1>
    <div class="text-2xl">by <span class="italic">{{ $book->author }}</span></div>
    <div class="text-3xl mt-10 font-bold">Reviews:</div>

    @forelse ($book->reviews as $review)
        <div class="card flex-col">
            <div class="w-full mb-3">
                <div class="float-left">{{ $review->rating }}</div>
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