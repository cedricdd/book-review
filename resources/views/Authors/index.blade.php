@extends('layouts.main')

@section('title', 'Authors')

@section('content')
    <div class="p-4">
        <x-header-title>Authors</x-header-title>

        @if ($authors->isEmpty())
            <p>No authors found.</p>
        @else
            <div>
                <x-nav-sorting type="author" />

                @foreach ($authors as $author)
                    <x-authors.card :$author />
                @endforeach

                {{ $authors->links() }}
            </div>
        @endif
    </div>
@endsection