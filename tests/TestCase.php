<?php

namespace Tests;

use App\Constants;
use App\Models\Book;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\Lang;
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

    protected function getLoginFormData(array $override = []): array
    {
        return $override + [
            'email' => $this->user->email,
            'password' => 'password',
        ];
    }
    protected function getRegisterFormData(array $override = []): array
    {
        return array_merge([
            'name' => $this->user->name,
            'email' => $this->user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $override);
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
    protected function checkForm(string $route, array $defaults, array $rules, ?User $user = null): void{

        foreach($rules as $infos) {
            // Allow $infos[0] to be a string or an array of strings
            $fields = is_array($infos[0]) ? $infos[0] : [$infos[0]];

            foreach($fields as $field) {
                //Get the error message based on the rule used
                $attribute = Lang::has("validation.attributes.{$field}") ? Lang::get("validation.attributes.{$field}") : $field;
                $error = Lang::get("validation.{$infos[1]}", compact('attribute') + ($infos[3] ?? []));
    
                // dump("Checking field: {$field} with value: {$infos[2]} and error: {$error}");
    
                $request = $user ? $this->actingAs($user): $this;

                $request->post($route, [$field => $infos[2]] + $defaults)
                    ->assertStatus(302)
                    ->assertInvalid([$field => $error]); // Assert validation errors
            }
        }
    }
}
