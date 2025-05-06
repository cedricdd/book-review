<?php

namespace Tests;

use App\Constants;
use App\Models\Book;
use App\Models\User;
use App\Models\Author;
use App\Models\Review;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected User $user;
    protected Author $author;
    protected Collection $categories;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->author = Author::factory()->create();
        $this->categories = new Collection();

        foreach (Constants::CATEGORIES as $name) {
            $category = new Category();
            $category->name = $name;
            $category->slug = Str::slug($name);
            $category->save();
    
            $this->categories[] = $category;
        }
    }

    protected function getBooks(int $count = Constants::BOOKS_PER_PAGE, array $override = [], int $reviewCount = 0, ?User $user = null, ?Author $author = null): Book|Collection
    {
        // Disable Eloquent events for the Review model
        // This will prevent any events (like creating, updating, deleting) from being triggered
        $books = Review::withoutEvents(
            fn() => Book::factory()->count($count)
                ->when($reviewCount, fn($query) => $query->has(Review::factory()->count($reviewCount)))
                ->when($user, fn($query) => $query->for($user, 'user'))
                ->when($author, fn($query) => $query->for($author, 'author'))
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

    protected function getAuthors(int $count = Constants::AUTHOR_PER_PAGE, array $override = [], bool $createBooks = false): Author|Collection
    {
        // Disable Eloquent events for the Review model
        // This will prevent any events (like creating, updating, deleting) from being triggered
        $authors = Review::withoutEvents(function () use ($count, $override, $createBooks) {
                $authors = Author::factory()->count($count)->create($override);

                if ($createBooks) {
                    foreach ($authors as $author) {
                        $author->books()->saveMany(Book::factory()->count(random_int(1, 50))->create());
                    }
                }

                return $authors;
            }
        );

        if ($count === 1)
            return $authors->first();
        else
            return $authors;
    }

    protected function getLoginFormData(array $override = []): array
    {
        return $override + [
            'email' => $this->user->email,
            'password' => 'password',
        ];
    }
    protected function getReviewFormData(array $override = []): array
    {
        return $override + [
            'review' => 'This is a review',
            'rating' => 5,
        ];
    }

    protected function getBookFormData(array $override = []): array
    {
        $size = (Constants::BOOK_COVER_MIN_RES + Constants::BOOK_COVER_MAX_RES) / 2;

        return $override + [
            'title' => 'Book Title',
            'published_at' => now()->subYear()->format('Y-m-d'),
            'summary' => 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum.',
            'cover' => UploadedFile::fake()->image('cover.jpg', width: $size, height: $size)->size(Constants::BOOK_COVER_MAX_WEIGHT / 2), // Assuming you want to test without a cover image
            'user_id' => $this->user->id,
            'author_id' => $this->author->id,
            'categories' => [1, 8, 16],
        ];
    }

    /**
     * Checks form validation by submitting POST requests to a given route with various field values and asserting validation errors.
     *
     * @param string $route The route to which the form is submitted.
     * @param array $defaults Default form data to include in every request.
     * @param array $rules An array of validation rules, where each rule is an array containing:
     *                     - string|array $field: The field name(s) to test.
     *                     - string $rule: The validation rule key (e.g., 'required', 'email').
     *                     - mixed $value: The value to test for the field.
     *                     - array|null $params: (Optional) Additional parameters for the validation message.
     * @param User|null $user (Optional) The user to authenticate as when making the request.
     *
     * @return void
     */
    protected function checkForm(string $route, array $defaults, array $rules, ?User $user = null): void
    {

        foreach ($rules as $infos) {
            // Allow $infos[0] to be a string or an array of strings
            $fields = is_array($infos[0]) ? $infos[0] : [$infos[0]];

            foreach ($fields as $field) {
                //Get the error message based on the rule used
                $attribute = Lang::has("validation.attributes.{$field}") ? Lang::get("validation.attributes.{$field}") : str_replace('_', ' ', $field);
                $error = Lang::get("validation.{$infos[1]}", compact('attribute') + ($infos[3] ?? []));

                // dump("Checking field: {$field} with value: {$infos[2]} and error: {$error}");

                $request = $user ? $this->actingAs($user) : $this;

                $request->post($route, [$field => $infos[2]] + $defaults)
                    ->assertStatus(302)
                    ->assertInvalid([$field => $error]); // Assert validation errors
            }
        }
    }
}
