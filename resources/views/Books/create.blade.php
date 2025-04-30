@extends('layouts.main')

@section('title', 'Add A Book')

@section('content')
    <div>
        <h1 class="text-4xl font-bold mb-2">Add A Book</h1>
        <p class="mb-4">Please fill in the details of the book you want to add.</p>
    </div>

    <x-books.form action='Create' :$authors />
@endsection