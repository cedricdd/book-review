<?php

use App\Constants;
use App\Models\Author;

test('authors_index', function () {
    $authors = $this->getAuthors(Constants::AUTHOR_PER_PAGE - 1); // One author is automatically created for each test

    $this->get(route('authors.index'))
        ->assertStatus(200)
        ->assertViewIs('authors.index')
        ->assertViewHas('authors', fn ($viewAuthors) => $viewAuthors->count() === Constants::AUTHOR_PER_PAGE)
        ->assertViewHas('authors', fn ($viewAuthors) => $viewAuthors->contains($authors->first()))
        ->assertViewHas('authors', fn ($viewAuthors) => $viewAuthors->contains($authors->last()))
        ->assertSee(['Authors', $authors->first()->name, $authors->first()->biography, $authors->last()->name, $authors->last()->biography])
        ->assertSee([route('authors.show', $authors->first()), route('authors.show', $authors->last())]);
});

test('authors_index_pagination', function () {
    $this->getAuthors(Constants::AUTHOR_PER_PAGE * 2 - 1); // One author is automatically created for each test

    $authors = Author::setSorting(Constants::AUTHOR_SORTING_DEFAULT)->get();

    $this->get(route('authors.index'))
        ->assertViewHas('authors', fn($viewAuthors) => $viewAuthors->count() === Constants::AUTHOR_PER_PAGE)
        ->assertViewHas('authors', fn($viewAuthors) => $viewAuthors->contains($authors->first()))
        ->assertViewHas('authors', fn($viewAuthors) => !$viewAuthors->contains($authors->last()))
        ->assertSeeText('Next');

    $this->get(route('authors.index', ['page' => 2]))
        ->assertViewHas('authors', fn($viewAuthors) => $viewAuthors->count() === Constants::AUTHOR_PER_PAGE)
        ->assertViewHas('authors', fn($viewAuthors) => !$viewAuthors->contains($authors->first()))
        ->assertViewHas('authors', fn($viewAuthors) => $viewAuthors->contains($authors->last()))
        ->assertSeeText('Previous');
});

test('authors_index_sorting', function () {
    $this->getAuthors(count: Constants::AUTHOR_PER_PAGE * 2 - 1, createBooks: true);   // One author is automatically created for each test

    foreach (Constants::AUTHOR_SORTING as $key => $value) {
        $authors = Author::setSorting($key)->get();

        $this->withSession(['author-sorting' => $key])
            ->get(route('authors.index'))
            ->assertViewHas('authors', fn($viewAuthors) => $viewAuthors->contains($authors->first()))
            ->assertViewHas('authors', fn($viewAuthors) => !$viewAuthors->contains($authors->last()));
    }
});

test("authors_index_last_page", function () {
    $lastPage = 2;

    $this->getAuthors(count: Constants::AUTHOR_PER_PAGE * $lastPage - 1); // One author is automatically created for each test

    $this->get(route('authors.index', ['page' => $lastPage + 10]))
        ->assertRedirect(route('authors.index', ['page' => $lastPage]));

    $this->get(route('authors.index', ['page' => $lastPage]))->assertStatus(200);
});


test('authors_show', function () {
    $books = $this->getBooks(count: Constants::BOOKS_PER_PAGE, author: $this->author); 

    $this->get(route('authors.show', $this->author))
        ->assertStatus(200)
        ->assertViewIs('authors.show')
        ->assertViewHas('author', fn ($viewAuthor) => $viewAuthor->is($this->author))
        ->assertSeeText([$this->author->name, $this->author->biography, $books->first()->title, $books->last()->title])
        ->assertViewHas('books', fn ($viewBooks) => $viewBooks->count() === Constants::BOOKS_PER_PAGE)
        ->assertSee([route('books.show', $books->first()), route('books.show', $books->last())]);

    //Add a review to the first book
    $review = $this->getReviews(count: 1, book: $books->first(), user: $this->user);

    $this->actingAs($this->user)
        ->get(route('authors.show', $this->author))
        ->assertSeeText('Your Rating: ' . $review->rating);
});