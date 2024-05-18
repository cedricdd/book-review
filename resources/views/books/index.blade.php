@extends('layouts.main')

@section("content")
    <div class="container mx-auto px-10 pb-20">
        <form action="{{ route("books.index")}}" method="GET" class="flex items-center">
            <input class="grow input justify-self-center" type="text" name="title" placeholder="Enter Book Title" value="{{ request("title") }}" />
            <button class="btn ml-10" type="submit">Submit</button>
        </form>

        @forelse ($books as $book)
        <a href="{{ route("books.show", $book) }}" class="card flex-row">
            <div class="grow flex flex-col">
                <span class="text-lg font-bold">{{ $book->title }}</span>
                <span>by <span class="italic">{{ $book->author }}</span></span>
            </div>
            <div class="flex flex-col" style="min-width: 150px">
                <span class="font-bold">{{ number_format($book->reviews_avg_rating, 2) }}</span>
                <span>out of <span class="italic">{{ $book->reviews_count }}</span> {{ Str::plural("review", $book->reviews_count) }}</span>
            </div>
        </a>
    @empty
        
    @endforelse
    </div>
@endsection