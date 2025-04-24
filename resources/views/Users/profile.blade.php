@extends('layouts.main')

@section('title', $user->name . ' Reviews')

@section('content')
    <x-header-title>{{ $user->name }}'s Reviews ({{ $reviews->total() }})</x-header-title>

    @if ($reviews->isEmpty())
        <p>No reviews found.</p>
    @else
        @foreach ($reviews as $review)
            <x-reviews.card-with-book :$review />
        @endforeach

        {{ $reviews->links() }}
    @endif
@endsection
