<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Http\Requests\ReviewRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class ReviewController extends Controller
{
    public function create(Book $book): View|RedirectResponse
    {
        if (url()->current() != url()->previous()) {
            session()->put('url.back', url()->previous());
        }

        // Check if the user has already reviewed the book
        if ($book->reviews()->where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('failure', 'You have already reviewed this book!');
        }

        return view('reviews.create', compact('book'));
    }

    public function store(ReviewRequest $request, Book $book): RedirectResponse
    {
        // Check if the user has already reviewed the book
        if ($book->reviews()->where('user_id', Auth::id())->exists()) {
            return redirect()->back()->with('failure', 'You have already reviewed this book!');
        }

        $review = new Review();
        $review->review = $request->input('review');
        $review->rating = $request->input('rating');
        $review->user()->associate(Auth::user());
        $review->book()->associate($book);
        $review->save();

        return redirect()->to(session()->get('url.back', url()->previous()))->with('success', 'Review added successfully!');
    }

    public function edit(Book $book, Review $review)
    {
        if (url()->current() != url()->previous()) {
            session()->put('url.back', url()->previous());
        }

        return view('reviews.edit', compact('book', 'review'));
    }

    public function update(ReviewRequest $request, Book $book, Review $review): RedirectResponse
    {
        $review->review = $request->input('review');
        $review->rating = $request->input('rating');
        $review->save();

        return redirect()->to(session()->get('url.back', url()->previous()))->with('success', 'Review updated successfully!');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }
}
