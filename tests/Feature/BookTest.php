<?php

use App\Constants;
use App\Models\Book;
use App\Models\Review;
use PHPUnit\TextUI\Configuration\Constant;

test('books_index', function () {
    $this->get(route('books.index'))
        ->assertStatus(200)
        ->assertSeeText('No books available.');

    $books = Book::factory()->count(Constants::BOOKS_PER_PAGE)->create();

    $this->get(route('books.index'))
        ->assertStatus(200)
        ->assertViewIs('books.index')
        ->assertSee("Search for books")
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() === Constants::BOOKS_PER_PAGE)
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->last()))
        ->assertSee(route('books.show', $books->first()));
});

test('books_index_search', function () {
    $books = Book::factory()->count(Constants::BOOKS_PER_PAGE)->create(['title' => 'Test Book' . rand(1, 1000)]);

    $this->get(route('books.index', ['q' => 'Test Book']))
        ->assertStatus(200)
        ->assertViewIs('books.index')
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() === Constants::BOOKS_PER_PAGE)
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->last()));

    $this->get(route('books.index', ['q' => 'Nothing Matching']))
        ->assertStatus(200)
        ->assertSeeText('No books found')
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() === 0);
});

test('books_index_pagination', function () {
    $books = Book::factory()->count(Constants::BOOKS_PER_PAGE * 2)->create();

    $books = $books->sortBy([['title', 'asc']]);

    $this->get(route('books.index'))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() === Constants::BOOKS_PER_PAGE)
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => !$viewBooks->contains($books->last()))
        ->assertSeeText('Next');

    $this->get(route('books.index', ['page' => 2]))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() === Constants::BOOKS_PER_PAGE)
        ->assertViewHas('books', fn($viewBooks) => !$viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->last()))
        ->assertSeeText('Previous');
});

test('books_index_sorting', function () {
    Book::factory()->count(Constants::BOOKS_PER_PAGE * 2)->has(Review::factory()->count(10))->create();

    foreach (Constants::BOOK_SORTING as $key => $value) {
        $books = Book::withCount('reviews')->withAvg('reviews', 'rating')->setSorting($key)->get();

        $this->withSession(['book-sorting' => $key])
            ->get(route('books.index'))
            ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
            ->assertViewHas('books', fn($viewBooks) => !$viewBooks->contains($books->last()));
    }
});
