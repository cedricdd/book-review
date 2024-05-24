<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
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
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Book $book): View
    {
        return view("books.reviews.create", ["book" => $book, "title" => "Add a Review for $book->title"]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Book $book): RedirectResponse
    {
        $validator = $request->validate([
            'review' => 'required|min:50',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $book->reviews()->create($validator + ['ip_address' => $request->ip()]);

        return redirect()->route("books.show", $book)->with("success","Your review was successfully added!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
