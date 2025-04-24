<?php

use App\Constants;
use App\Models\Review;
use PHPUnit\TextUI\Configuration\Constant;

test('profile', function () {
    $reviews = $this->getReviews(count: Constants::REVIEW_PER_PAGE * 2, user: $this->user)->sortBy([['created_at', 'desc']]);

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

test('profile_sorting', function() {
    $reviews = $this->getReviews(count: Constants::REVIEW_PER_PAGE * 2, user: $this->user);

    foreach(Constants::REVIEW_SORTING as $sorting => $label) {
        $reviews = Review::where('user_id', $this->user->id)->setSorting($sorting)->get();

        $this->withSession(['review-sorting' => $sorting])
            ->get(route('users.profile', [$this->user->id]))
            ->assertViewHas('reviews', fn($viewReviews) => $viewReviews->first()->is($reviews->first()))
            ->assertViewHas('reviews', fn($viewReviews) => !$viewReviews->contains($reviews->last()));
    }
});

test('profile_redirects_last_page', function () {
    $lastPage = 2;
    $reviews = $this->getReviews(count: Constants::REVIEW_PER_PAGE * $lastPage, user: $this->user);

    $this->get(route('users.profile', [$this->user->id, 'page' => $lastPage + 1]))
        ->assertRedirect(route('users.profile', [$this->user->id, 'page' => $lastPage]));

    $this->get(route('users.profile', [$this->user->id, 'page' => $lastPage]))->assertStatus(200);
});