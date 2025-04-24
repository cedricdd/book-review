<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(User $user) {
        $reviews = $user->reviews()->with('book')->paginate(Constants::REVIEW_PER_PAGE);

        return view('users.profile', compact('user', 'reviews'));
    }
}
