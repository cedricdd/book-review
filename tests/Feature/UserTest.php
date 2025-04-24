<?php

use App\Constants;
use PHPUnit\TextUI\Configuration\Constant;

test('profile', function () {
    $reviews = $this->getReviews(count: Constants::REVIEW_PER_PAGE * 2, user: $this->user);

    $this->get(route('users.profile', $this->user))
        ->assertStatus(200)
        ->assertSeeText($this->user->name . "'s Reviews", false)
        ->assertViewHas('user', $this->user)
        ->assertViewHas('reviews', fn($viewReviews) => $viewReviews->count() === Constants::REVIEW_PER_PAGE)
        ->assertViewHas('reviews', fn($viewReviews) => $viewReviews->contains($reviews->first()))
        ->assertViewHas('reviews', fn($viewReviews) => !$viewReviews->contains($reviews->last()));

    $this->get(route('users.profile', [$this->user->id, 'page' => 2]))
        ->assertStatus(200)
        ->assertViewHas('reviews', fn($viewReviews) => $viewReviews->count() === Constants::REVIEW_PER_PAGE)
        ->assertViewHas('reviews', fn($viewReviews) => !$viewReviews->contains($reviews->first()))
        ->assertViewHas('reviews', fn($viewReviews) => $viewReviews->contains($reviews->last()));
});
