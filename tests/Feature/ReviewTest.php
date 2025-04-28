<?php

use App\Constants;
use App\Models\Review;
use Illuminate\Support\Facades\Cache;

test('review_model_event_update_cache', function () {
    Cache::spy();

    $book = $this->getBooks(1);

    // Add a review to the book
    $book->reviews()->save(Review::factory()->make());

    // Check if the cache was updated
    Cache::shouldHaveReceived('forget')->once()->with("book_reviews_{$book->id}");

    $review = $book->reviews()->first();

    // Update the review
    $review->review = 'Updated review';
    $review->save();

    // Check if the cache was updated
    Cache::shouldHaveReceived('forget')->twice()->with("book_reviews_{$book->id}");

    // Delete the review
    $review->delete();

    // Check if the cache was updated three times
    Cache::shouldHaveReceived('forget')->times(3)->with("book_reviews_{$book->id}");
});

test('review_delete_owner', function () {
    $review = $this->getReviews(count: 1);

    $this->actingAs($this->user)
        ->delete(route('reviews.destroy', $review->id))
        ->assertForbidden();
});

test('review_delete_auth', function () {
    $review = $this->getReviews(count: 1);

    $this->delete(route('reviews.destroy', $review->id))->assertRedirectToRoute('login');
});

test('review_delete', function () {
    $review = $this->getReviews(count: 1, user: $this->user);

    $this->actingAs($review->user)
        ->delete(route('reviews.destroy', $review->id))
        ->assertStatus(302)
        ->assertSessionHasNoErrors();

    // Check if the review was deleted
    $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
});

test('review_edit', function () {
    $book = $this->getBooks(count: 1);
    $review = $this->getReviews(count: 1, book: $book, user: $this->user);

    $this->actingAs($this->user)
        ->get(route('reviews.edit', [$book, $review]))
        ->assertValid()
        ->assertStatus(200)
        ->assertViewIs('reviews.edit')
        ->assertViewHas('book', fn($viewBook) => $viewBook->is($book))
        ->assertViewHas('review', fn($viewReview) => $viewReview->is($review))
        ->assertSeeTextInOrder(['Review', 'Rating', 'Edit']);
});

test('review_edit_auth', function () {
    $book = $this->getBooks(count: 1);
    $review = $this->getReviews(count: 1, book: $book, user: $this->user);

    $this->get(route('reviews.edit', [$book, $review]))->assertRedirectToRoute('login');
});

test('review_edit_owner', function () {
    $book = $this->getBooks(count: 1);
    $review = $this->getReviews(count: 1, book: $book);

    $this->actingAs($this->user)
        ->get(route('reviews.edit', [$book, $review]))
        ->assertForbidden();
});

test('review_update_success', function () {
    $review = $this->getReviews(count: 1, user: $this->user);

    $this->actingAs($review->user)
        ->put(route('reviews.update', [$review->book, $review]), $this->getReviewFormData())
        ->assertValid()
        ->assertStatus(302);

    // Check if the review was updated
    $this->assertDatabaseHas('reviews', ['id' => $review->id] + $this->getReviewFormData());
});

test('review_update_auth', function () {
    $book = $this->getBooks(count: 1);
    $review = $this->getReviews(count: 1, book: $book);

    $this->put(route('reviews.update', [$review->book, $review]), $this->getReviewFormData())->assertRedirectToRoute('login');
});

test('review_update_owner', function () {
    $review = $this->getReviews(count: 1);

    $this->actingAs($this->user)->get(route('reviews.edit', [$review->book, $review]))->assertForbidden();

    $this->actingAs($this->user)
        ->put(route('reviews.update', [$review->book, $review]), $this->getReviewFormData())
        ->assertForbidden();
});

test('review_form_validation', function () {
    $book = $this->getBooks(count: 1);

    $this->checkForm(
        route('reviews.store', [$book]),
        $this->getReviewFormData(),
        [
            [['review', 'rating'], 'required', ''],
            ['review', 'min.string', str_repeat('a', Constants::REVIEW_MIN_LENGTH - 1), ['min' => Constants::REVIEW_MIN_LENGTH]],
            ['review', 'max.string', str_repeat('a', Constants::REVIEW_MAX_LENGTH + 1), ['max' => Constants::REVIEW_MAX_LENGTH]],
            ['rating', 'numeric', 'rating'],
            ['rating', 'min.numeric', Constants::REVIEW_MIN_RATING - 1, ['min' => Constants::REVIEW_MIN_RATING]],
            ['rating', 'max.numeric', Constants::REVIEW_MAX_RATING + 1, ['max' => Constants::REVIEW_MAX_RATING]],
        ],
        $this->user
    );
});

test('review_create', function () {
    $book = $this->getBooks(count: 1);

    $this->actingAs($this->user)
        ->get(route('reviews.create', [$book]))
        ->assertStatus(200)
        ->assertViewIs('reviews.create')
        ->assertViewHas('book', fn($viewBook) => $viewBook->is($book))
        ->assertSeeTextInOrder(['Review', 'Rating', 'Create']);
});

test('review_create_auth', function () {
    $book = $this->getBooks(count: 1);

    $this->get(route('reviews.create', [$book]))->assertRedirectToRoute('login');
});

test('review_duplicate', function () {
    $book = $this->getBooks(count: 1);
    $this->getReviews(count: 1, book: $book, user: $this->user);

    $this->actingAs($this->user)
        ->get(route('reviews.create', [$book]))
        ->assertStatus(302)
        ->assertSessionHas('failure', 'You have already reviewed this book!');

    $this->actingAs($this->user)
        ->post(route('reviews.store', [$book]), $this->getReviewFormData())
        ->assertStatus(302)
        ->assertSessionHas('failure', 'You have already reviewed this book!');
});

test('review_store_success', function () {
    $book = $this->getBooks(count: 1);

    $this->actingAs($this->user)
        ->post(route('reviews.store', [$book]), $this->getReviewFormData())
        ->assertValid()
        ->assertStatus(302);

    // Check if the review was created
    $this->assertDatabaseHas('reviews', ['user_id' => $this->user->id, 'book_id' => $book->id] + $this->getReviewFormData());
});

test('review_store_auth', function () {
    $book = $this->getBooks(count: 1);

    $this->post(route('reviews.store', [$book]), $this->getReviewFormData())->assertRedirectToRoute('login');
});

test('review_store_duplicate', function () {
    $book = $this->getBooks(count: 1);
    $this->getReviews(count: 1, book: $book, user: $this->user);

    $this->actingAs($this->user)
        ->post(route('reviews.store', [$book]), $this->getReviewFormData())
        ->assertStatus(302)
        ->assertSessionHas('failure', 'You have already reviewed this book!');
});
