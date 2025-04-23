<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $term = trim(htmlspecialchars($request->input('q', '')));

        // Fetch books from the database, applying the search filter if provided
        $books = Book::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->when($term, fn($query) => $query->where('title', 'LIKE', "%{$term}%"))
            ->setSorting(session('book-sorting', Constants::BOOK_SORTING_DEFAULT))
            ->paginate(Constants::BOOKS_PER_PAGE);

        if(!empty($term)) $books->appends(['q' => $term]);

        // Return the view with the books and the search query
        return view('books.index', compact('books', 'term'));
    }
}
