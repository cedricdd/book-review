<?php

use App\Constants;
use App\Models\Author;

test('authors_index', function () {
    $authors = $this->getAuthors(Constants::AUTHOR_PER_PAGE);

    $this->get(route('authors.index'))
        ->assertStatus(200)
        ->assertViewIs('authors.index')
        ->assertViewHas('authors', fn ($viewAuthors) => $viewAuthors->count() === Constants::AUTHOR_PER_PAGE)
        ->assertViewHas('authors', fn ($viewAuthors) => $viewAuthors->contains($authors->first()))
        ->assertViewHas('authors', fn ($viewAuthors) => $viewAuthors->contains($authors->last()))
        ->assertSeeText(['Authors', $authors->first()->name, $authors->first()->biography, $authors->last()->name, $authors->last()->biography])
        ->assertSee([route('authors.show', $authors->first()), route('authors.show', $authors->last())]);
});

test('authors_index_pagination', function () {
    $authors = $this->getAuthors(Constants::AUTHOR_PER_PAGE * 2);

    $authors = $authors->sortBy([['name', 'asc']]); //Default sorting by name

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
    $a = $this->getAuthors(count: Constants::AUTHOR_PER_PAGE * 2, createBooks: true);

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

    $this->getAuthors(count: Constants::AUTHOR_PER_PAGE * $lastPage);

    $this->get(route('authors.index', ['page' => $lastPage + 10]))
        ->assertRedirect(route('authors.index', ['page' => $lastPage]));

    $this->get(route('authors.index', ['page' => $lastPage]))->assertStatus(200);
});
