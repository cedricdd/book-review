<?php

use App\Constants;
use App\Models\Book;

test('books_index', function () {
    $books = Book::factory()->count(Constants::BOOKS_PER_PAGE)->create();

    $this->get(route('books.index'))
        ->assertStatus(200)
        ->assertViewIs('books.index')
        ->assertViewHas('books', fn ($viewBooks) => $viewBooks->count() === Constants::BOOKS_PER_PAGE)
        ->assertViewHas('books', fn ($viewBooks) => $viewBooks->contains($books->first()))
        ->assertViewHas('books', fn ($viewBooks) => $viewBooks->contains($books->last()));
});
