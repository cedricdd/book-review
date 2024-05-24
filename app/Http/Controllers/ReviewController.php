<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;

use App\Http\Requests\ReviewRequest;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReviewController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(middleware: 'throttle:reviews', only: ['store'])
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, Book $book): View|RedirectResponse
    {
        if(Review::checkIfExist($book->id, $request->ip())) return redirect()->route('books.show', $book)->with("failure", "You have already added a review for this book!");

        return view("books.reviews.create", ["book" => $book, "title" => "Add a Review for $book->title"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReviewRequest $request, Book $book): RedirectResponse
    {
        if(Review::checkIfExist($book->id, $request->ip())) return redirect()->route('books.show', $book)->with("failure", "You have already added a review for this book!");

        $book->reviews()->create($request->validated() + ['ip_address' => $request->ip()]);

        return redirect()->route("books.show", $book)->with("success","Your review was successfully added!");
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Book $book, Review $review): View
    {
        if(empty($request->ip()) || $request->ip() != $review->ip_address) abort(403);

        return view("books.reviews.edit", ["book" => $book, "review" => $review, "title" => "Edit your Review for $book->title"]); 
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ReviewRequest $request, Book $book, Review $review): RedirectResponse
    {
        if(empty($request->ip()) || $request->ip() != $review->ip_address) abort(403);

        $review->update($request->validated());

        return redirect()->route("books.show", $book)->with("success","Your review was successfully edited!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Book $book, Review $review): RedirectResponse
    {
        if(empty($request->ip()) || $request->ip() != $review->ip_address) abort(403);

        $review->delete();

        return redirect()->route("books.show", $book)->with("success","Your review was successfully deleted!");
    }
}
