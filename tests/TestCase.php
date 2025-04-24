<?php

namespace Tests;

use App\Constants;
use App\Models\Book;
use App\Models\User;
use App\Models\Review;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    protected function getBooks(int $count = Constants::BOOKS_PER_PAGE, array $override = [], int $reviewCount = 0): Book|Collection
    {
        // Disable Eloquent events for the Review model
        // This will prevent any events (like creating, updating, deleting) from being triggered
        $books = Review::withoutEvents(
            fn() => Book::factory()->count($count)
                ->when($reviewCount, fn($query) => $query->has(Review::factory()->count($reviewCount)))
                ->create($override)
        );

        if ($count === 1)
            return $books->first();
        else
            return $books;
    }

    protected function getReviews(int $count = Constants::REVIEW_PER_PAGE, array $override = [], ?Book $book = null, ?User $user = null): Review|Collection
    {
        // Disable Eloquent events for the Review model
        // This will prevent any events (like creating, updating, deleting) from being triggered
        $reviews = Review::withoutEvents(
            fn() => Review::factory()->count($count)
                ->when($book, fn($query) => $query->for($book, 'book'))
                ->when($user, fn($query) => $query->for($user, 'user'))
                ->create($override)
        );

        if ($count === 1)
            return $reviews->first();
        else
            return $reviews;
    }
}
