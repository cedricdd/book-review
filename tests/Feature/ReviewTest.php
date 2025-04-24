<?php

use App\Models\Review;
use Illuminate\Support\Facades\Cache;

test('review_model_event_update_cache', function() {
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

test('review_delete_cant_be_accessed_by_others', function() {
    $review = $this->getReviews(count: 1);
    
    $this->actingAs($this->user)
        ->delete(route('reviews.destroy', $review->id))
        ->assertForbidden();
});

test('review_delete', function() {
    $review = $this->getReviews(count: 1, user: $this->user);
    
    $this->actingAs($review->user)
        ->delete(route('reviews.destroy', $review->id))
        ->assertStatus(302)
        ->assertSessionHasNoErrors();
    
    // Check if the review was deleted
    $this->assertDatabaseMissing('reviews', ['id' => $review->id]);
});