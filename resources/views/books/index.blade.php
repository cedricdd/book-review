@extends('layouts.main')

@push('style')
    <style>
        .radio:not(:first-child) {
            margin-left: 3px;
        }
        .radio:not(:last-child) {
            margin-right: 3px;
        }
        input[type="radio"]:checked + span {
            display: block;
        }
    </style>
@endpush

@section("content")
    <div class="container mx-auto px-10 pb-20">
        <form action="{{ route("books.index")}}" method="GET" class="mb-6 max-w-screen-md mx-auto">
            <input class="input" type="text" name="title" placeholder="Enter Book Title" value="{{ request("title") }}" />
            <div class="flex my-2">
                @foreach ($filters as $filter => $name)
                <label for="filter_{{ $filter }}" class="relative radio">
                    <span class="font-semibold text-gray-500 leading-tight">{{ $name }}</span>
                    <input type="radio" name="filter" id="filter_{{ $filter }}" value="{{ $filter }}" class="absolute h-0 w-0 appearance-none" @if(request("filter") == $filter) checked @endif />
                    <span aria-hidden="true" class="hidden absolute inset-0 border-2 border-blue-500 bg-blue-200 bg-opacity-10 rounded-lg"></span>
                </label>
                @endforeach
            </div>
            <div class="w-full flex">
                <button class="btn grow mr-1" type="submit">Submit</button>
                <a href="{{ route("books.index") }}" class="btn grow ml-1">Clear</a>
            </div>
        </form>

        @forelse ($books as $book)
        <a href="{{ route("books.show", $book) }}" class="card flex-row">
            <div class="grow flex flex-col">
                <span class="text-lg font-bold">{{ $book->title }}</span>
                <span>by <span class="italic">{{ $book->author }}</span></span>
            </div>
            <div class="flex flex-col min-w-32">
                <span class="font-bold">{{ number_format($book->reviews_avg_rating, 2) }}</span>
                <span>out of <span class="italic">{{ $book->reviews_count }}</span> {{ Str::plural("review", $book->reviews_count) }}</span>
            </div>
        </a>
        @empty
        <h1 class="text-center text-5xl mt-28">There are no books matching your search!</h1>     
        @endforelse
    </div>
@endsection