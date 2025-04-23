<?php

use App\Constants;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;

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

test("books_index_redirect_last_page", function () {
    $lastPage = 2;

    Book::factory()->count(Constants::BOOKS_PER_PAGE * $lastPage)->create();

    $this->get(route('books.index', ['page' => 10]))
        ->assertRedirect(route('books.index', ['page' => $lastPage]));

    $this->get(route('books.index', ['page' => $lastPage]))->assertStatus(200);
});

test('books_show', function () {
    $book = Book::factory()->has(Review::factory()->count(10), 'reviews')->create();

    Cache::shouldReceive('remember')
        ->once()
        ->with("book_reviews_{$book->id}", Constants::CACHE_REVIEWS, Mockery::type('Closure'))
        ->andReturn($book->reviews->sortBy('created_at'));

    Cache::makePartial();

    $this->get(route('books.show', $book))
        ->assertStatus(200)
        ->assertViewIs('books.show')
        ->assertViewHas('book', fn($viewBook) => $viewBook->is($book))
        ->assertSeeText($book->title)
        ->assertSeeText($book->author)
        ->assertSeeText($book->summary)
        ->assertViewHas('reviews', fn($reviews) => $reviews->count() === 10)
        ->assertSeeText($book->reviews->first()->review)
        ->assertSeeText($book->reviews->last()->review);
});

test('books_show_no_reviews', function () {
    $book = Book::factory()->create();

    $this->get(route('books.show', $book))
        ->assertStatus(200)
        ->assertSeeText('No reviews yet');
});