<?php

use App\Constants;
use App\Models\Review;

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

test('profile_sorting', function () {
    $reviews = $this->getReviews(count: Constants::REVIEW_PER_PAGE * 2, user: $this->user);

    foreach (Constants::REVIEW_SORTING as $sorting => $label) {
        $reviews = Review::where('user_id', $this->user->id)->setSorting($sorting)->get();

        $this->withSession(['review-sorting' => $sorting])
            ->get(route('users.profile', [$this->user->id]))
            ->assertViewHas('reviews', fn($viewReviews) => $viewReviews->first()->is($reviews->first()))
            ->assertViewHas('reviews', fn($viewReviews) => !$viewReviews->contains($reviews->last()));
    }
});

test('profile_pagination', function () {
    $this->getReviews(count: Constants::REVIEW_PER_PAGE * 2, user: $this->user);

    $reviews = $this->user->reviews()->with('book')->setSorting(Constants::REVIEW_SORTING_DEFAULT)->get();

    $this->get(route('users.profile', $this->user))
        ->assertStatus(200)
        ->assertViewHas('reviews', fn($viewReviews) => $viewReviews->count() === Constants::REVIEW_PER_PAGE)
        ->assertViewHas('reviews', fn($viewReviews) => $viewReviews->contains($reviews->first()))
        ->assertViewHas('reviews', fn($viewReviews) => !$viewReviews->contains($reviews->last()));
});

test('profile_redirects_last_page', function () {
    $lastPage = 2;
    $reviews = $this->getReviews(count: Constants::REVIEW_PER_PAGE * $lastPage, user: $this->user);

    $this->get(route('users.profile', [$this->user->id, 'page' => $lastPage + 1]))
        ->assertRedirect(route('users.profile', [$this->user->id, 'page' => $lastPage]));

    $this->get(route('users.profile', [$this->user->id, 'page' => $lastPage]))->assertStatus(200);
});

test('profile_show_review_options', function() {
    $reviews = $this->getReviews(count: Constants::REVIEW_PER_PAGE, user: $this->user);

    $this->actingAs($this->user)
        ->get(route('users.profile', $this->user))
        ->assertSeeTextInOrder(['Edit', 'Delete']);
});

test('login', function () {
    $this->get(route('login'))
        ->assertStatus(200)
        ->assertSeeTextInOrder(['Email', 'Password', 'Login']);
});

test('login_cant_be_accessed_by_authenticated_user', function () {
    $this->actingAs($this->user)
        ->get(route('login'))
        ->assertRedirectToRoute('books.index');
});

test('login_successful', function () {
    $this->post(route('login'), $this->getLoginFormData())
        ->assertValid()
        ->assertRedirectToRoute('users.profile', [$this->user->id]);

    $this->assertAuthenticatedAs($this->user);
});

test('login_failed', function () {
    $this->post(route('login'), $this->getLoginFormData(['password' => 'invalid-password']))
        ->assertRedirectToRoute('books.index')
        ->assertSessionHasErrors(['email']);

    $this->assertGuest();
    expect(session()->hasOldInput('email'))->toBeTrue();
    expect(session()->hasOldInput('password'))->toBeFalse();
});

test('login_form_validation', function () {
    $this->checkForm(route('login'), $this->getLoginFormData(), [
        [['email', 'password'], 'required', ''],
        ['email', 'email', 'invalid-email'],
    ]);
});

test('logout_cant_be_accessed_by_guest', function () {
    $this->delete(route('logout'))
        ->assertRedirectToRoute('login');
});

test('logout', function () {
    $this->be($this->user);

    $this->actingAs($this->user)
        ->delete(route('logout'))
        ->assertSessionHasNoErrors()
        ->assertRedirectToRoute('books.index');

    $this->assertGuest();
});