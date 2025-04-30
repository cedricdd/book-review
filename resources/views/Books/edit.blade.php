@extends('layouts.main')

@section('title', 'Edit ' . $book->title)

@section('content')
    <x-header-title>Edit {{ $book->title }}</x-header-title>

    <x-books.form action='Edit' :$book :$authors />
@endsection
