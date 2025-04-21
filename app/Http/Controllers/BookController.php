<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        // Fetch books from the database, applying the search filter if provided
        $books = Book::withAvg('reviews', 'rating')->withCount('reviews')->orderBy('title', 'ASC')->paginate(Constants::BOOKS_PER_PAGE);

        // Return the view with the books and the search query
        return view('books.index', compact('books'));
    }
}
