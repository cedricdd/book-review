<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookPolicy
{

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Book $book): bool
    {
        return $book->user()->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(User $user, Book $book): bool
    {
        return $book->user()->is($user);
    }
}
