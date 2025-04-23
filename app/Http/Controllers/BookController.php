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

        //Redirect to the last page if the requested page exceeds the last page
        if($request->input('page', 1) > $books->lastPage()) {
            return redirect()->route('books.index', ['page' => $books->lastPage()]); 
        }

        if(!empty($term)) $books->appends(['q' => $term]);

        // Return the view with the books and the search query
        return view('books.index', compact('books', 'term'));
    }

    public function show(Book $book)
    {
        // Fetch the book with its reviews
        $book->load(['reviews' => fn($query) => $query->latest(), 'reviews.user']);

        $rating = number_format($book->reviews->avg('rating'), 2);

        // Return the view with the book
        return view('books.show', compact('book', 'rating'));
    }
}
