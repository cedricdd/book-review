<?php

use App\Constants;
use App\Models\Book;
use App\Models\Review;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    foreach (Constants::CATEGORIES as $name) {
        $category = new Category();
        $category->name = $name;
        $category->slug = Str::slug($name);
        $category->save();
    }
});

test('categories_index', function () {
    $categories = Category::all();

    //We need to create books with reviews for each category
    foreach($categories as $category) {
        Book::factory()
            ->count(1)
            ->create()
            ->each(function ($book) use ($category) {
                $book->categories()->attach($category->id);
                $book->reviews()->saveMany(Review::factory(Constants::MIN_REVIEWS_FOR_CATEGORY_COVER)->make());
            });
    }

    DB::enableQueryLog();

    $response = $this->get(route('categories.index'))
        ->assertStatus(200)
        ->assertViewIs('categories.index')
        ->assertViewHas('categories', fn($viewCategories) => count($viewCategories) == $categories->count());

    foreach($categories as $category) {
        $response->assertSee($category->name);
        $response->assertSee(route('categories.show', $category->slug));
    }

    $queryCount = count(DB::getQueryLog()); //Count the number of queries executed

    DB::flushQueryLog();

    $this->get(route('categories.index')); //Recall the page to check if the cache is working

    expect(count(DB::getQueryLog()))->toBeLessThan($queryCount);
});
