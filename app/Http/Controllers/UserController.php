<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

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

    public function login(Request $request)
    {
        if(url()->current() != url()->previous()) {
            session()->put('url.login', url()->previous());
        }

        return view('users.login');
    }

    public function loginPost(LoginRequest $request)
    {
        if(!RateLimiter::attempt(key: 'login' . $request->ip(), maxAttempts: 5, callback: function() {})) {
            throw ValidationException::withMessages([
                'email' => "To many failled login attempts, try again later!",
            ]);
        }

        if(! Auth::attempt($request->only(["email", "password"]))) {
            throw ValidationException::withMessages([
                'email' => "The email/password you provided are invalid!",
            ]);
        }

        RateLimiter::clear('login' . $request->ip());

        Session::regenerate();

        return redirect()->to(session('url.login', route('users.profile', Auth::user())))->with("success", "You have been successfully logged in!");
    }

    public function logout(): RedirectResponse {
        Auth::logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect()->route("books.index")->with("success", "You have been successfully logged out!");
    }
}
