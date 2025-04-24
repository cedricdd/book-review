<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    public function update(User $user, Review $review): bool
    {
        return $review->user()->is($user);
    }
    public function destroy(User $user, Review $review): bool
    {
        return $review->user()->is($user);
    }
}
