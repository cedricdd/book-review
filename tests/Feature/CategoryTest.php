<?php

use App\Constants;
use Illuminate\Support\Facades\DB;

test('categories_index', function () {
    //We need to create books with reviews for each category
    foreach ($this->categories as $category) {
        $book = $this->getBooks(count: 1, reviewCount: Constants::MIN_REVIEWS_FOR_CATEGORY_COVER);
        $book->categories()->attach($category->id);
    }

    DB::enableQueryLog();

    $response = $this->get(route('categories.index'))
        ->assertStatus(200)
        ->assertViewIs('categories.index')
        ->assertViewHas('categories', fn($viewCategories) => count($viewCategories) == $this->categories->count());

    foreach ($this->categories as $category) {
        $response->assertSee($category->name);
        $response->assertSee(route('categories.show', $category->slug));
    }

    $queryCount = count(DB::getQueryLog()); //Count the number of queries executed

    DB::flushQueryLog();

    $this->get(route('categories.index')); //Recall the page to check if the cache is working

    expect(count(DB::getQueryLog()))->toBeLessThan($queryCount);
});

test('categories_show', function () {
    $category = $this->categories->first();

    $this->get(route('categories.show', $category))
        ->assertStatus(200)
        ->assertViewIs('categories.show')
        ->assertViewHas('category', fn($viewCategory) => $viewCategory->is($category))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() == 0)
        ->assertSeeText("No books available in this category.");

    //We need to create books with reviews for each category
    $books = $this->getBooks(count: Constants::BOOKS_PER_PAGE, reviewCount: 10)->each(fn($book) => $book->categories()->attach($category));

    $this->get(route('categories.show', $category))
        ->assertStatus(200)
        ->assertViewIs('categories.show')
        ->assertViewHas('category', fn($viewCategory) => $viewCategory->is($category))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() == Constants::BOOKS_PER_PAGE)
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->last()))
        ->assertSee([$books->first()->title, $books->last()->title, $books->first()->summary, $books->last()->summary])
        ->assertSee(route('books.show', $books->first()), route('books.show', $books->last()));
});

test('categories_show_redirect_last_page', function () {
    $category = $this->categories->first();
    $lastPage = 2;

    //We need to create books with reviews for each category
    $books = $this->getBooks(count: Constants::BOOKS_PER_PAGE * $lastPage, reviewCount: 10)->each(fn($book) => $book->categories()->attach($category));

    $this->get(route('categories.show', [$category, 'page' => $lastPage + 1]))
        ->assertRedirect(route('categories.show', [$category, 'page' => $lastPage]));

    $this->get(route('categories.show', [$category, 'page' => $lastPage]))->assertStatus(200);
});

test('categories_show_pagination', function () {
    $category = $this->categories->first();

    //We need to create books with reviews for each category
    $books = $this->getBooks(count: Constants::BOOKS_PER_PAGE * 2, reviewCount: 10)
        ->each(fn($book) => $book->categories()->attach($category));

    $books = $books->sortBy('title'); // Sort books by title to ensure pagination works correctly, default sorting

    $this->get(route('categories.show',$category))
        ->assertStatus(200)
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => !$viewBooks->contains($books->last()))
        ->assertSeeText('Next');

    $this->get(route('categories.show', [$category, 'page' => 2]))
        ->assertStatus(200)
        ->assertViewHas('books', fn($viewBooks) => !$viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->last()))
        ->assertSeeText('Previous');
});

test('categories_show_sorting', function () {
    $category = $this->categories->first();

    //We need to create books with reviews for each category
    $this->getBooks(count: Constants::BOOKS_PER_PAGE * 2, reviewCount: 10)
        ->each(fn($book) => $book->categories()->attach($category));

    foreach (Constants::BOOK_SORTING as $key => $value) {
        $books = $category->books()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->setSorting($key)->get();

        $this->withSession(['book-sorting' => $key])
            ->get(route('categories.show', [$category]))
            ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
            ->assertViewHas('books', fn($viewBooks) => !$viewBooks->contains($books->last()));
    }
});