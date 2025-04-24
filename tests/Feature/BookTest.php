<?php

use App\Constants;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

test('books_index', function () {
    $this->get(route('books.index'))
        ->assertStatus(200)
        ->assertSeeText('No books available.');

    $books = $this->getBooks();

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
    $books = $this->getBooks(override: ['title' => 'Test Book' . rand(1, 1000)]);

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
    $books = $this->getBooks(Constants::BOOKS_PER_PAGE * 2);

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
    $this->getBooks(count: Constants::BOOKS_PER_PAGE * 2, reviewCount: 10);

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

    $this->getBooks(count: Constants::BOOKS_PER_PAGE * $lastPage);

    $this->get(route('books.index', ['page' => $lastPage + 1]))
        ->assertRedirect(route('books.index', ['page' => $lastPage]));

    $this->get(route('books.index', ['page' => $lastPage]))->assertStatus(200);
});

test('books_show', function () {
    $book = $this->getBooks(count: 1, reviewCount: 10);

    DB::enableQueryLog();

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

    $queryCount = count(DB::getQueryLog());

    DB::flushQueryLog();

    $this->get(route('books.show', $book)); //Re-call the page 

    expect(count(DB::getQueryLog()))->toBeLessThan($queryCount); // Check if the query count is less than the previous one, the cache is used
});

test('books_show_no_reviews', function () {
    $book = $this->getBooks(count: 1);

    $this->get(route('books.show', $book))
        ->assertStatus(200)
        ->assertSeeText('No reviews yet');
});