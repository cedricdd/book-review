<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        // Fetch the reviews and cache them for 24 hours
        $reviews = Cache::remember('book_reviews_' . $book->id, Constants::CACHE_REVIEWS, fn() => $book->reviews()->latest()->with('user')->get());

        $rating = $reviews->count() ? number_format($reviews->avg('rating'), 2) : 0;

        if(Auth::check()) {
            $userReview = Review::where('book_id', $book->id)->where('user_id', Auth::id())->first();

            // Filter out the user's own review from the list of reviews
            $reviews = $reviews->filter(fn($review) => !($review->user_id === Auth::id()));
        }
        else $userReview = null;

        // Return the view with the book
        return view('books.show', compact('book', 'reviews', 'rating', 'userReview'));
    }
}
