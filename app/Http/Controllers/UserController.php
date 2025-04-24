<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request, User $user)
    {
        $reviews = $user->reviews()->with('book')
            ->setSorting(session()->get('review-sorting', Constants::REVIEW_SORTING_DEFAULT))
            ->paginate(Constants::REVIEW_PER_PAGE);

        if(request()->has('page') && request()->input('page') > $reviews->lastPage()) {
            return redirect()->route('users.profile', [$user->id, 'page' => $reviews->lastPage()]); 
        }

        return view('users.profile', compact('user', 'reviews'));
    }
}
