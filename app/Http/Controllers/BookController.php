<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Book;
use App\Models\Author;
use App\Models\Review;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Requests\BookRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class BookController extends Controller
{
    public function index(Request $request): RedirectResponse|View
    {
        $term = trim(htmlspecialchars($request->input('q', '')));

        // Fetch books from the database, applying the search filter if provided
        $books = Book::with(['author', 'categories' => fn($query) => $query->orderBy('name')])
            ->withCount('reviews')
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

    public function show(Book $book): View
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

        $book->load(['categories' => fn($query) => $query->orderBy('name')]);

        // Return the view with the book
        return view('books.show', compact('book', 'reviews', 'rating', 'userReview'));
    }

    public function create(): View
    {
        if(url()->current() != url()->previous()) {
            session()->put('url.back', url()->previous());
        }

        return view('books.create');
    }

    public function store(BookRequest $request): RedirectResponse
    {
        $book = new Book();
        $book->title = $request->input('title');
        $book->summary = $request->input('summary');
        $book->published_at = $request->input('published_at');

        $book->user()->associate(Auth::user());
        $book->author()->associate($request->input('author_id'));
        $book->save();

        $book->categories()->attach($request->input('categories'));

        if($request->hasFile('cover')) {
            $cover = $request->file('cover');

            $book->cover_image = $request->file('cover')->storeAs('covers',  $book->id . '.' . $cover->extension(), ['disk' => 'public']);
            $book->save();
        }

        return redirect()->route('books.show', ['book' => $book])->with('success', 'Book created successfully!');
    }

    public function destroy(Book $book): RedirectResponse
    {
        $book->delete();

        return redirect()->route('books.owner')->with('success', 'Book deleted successfully!');
    }

    public function owner(Request $request): View
    {
        $books = $request->user()->books()
            ->with(['author', 'categories' => fn($query) => $query->orderBy('name')])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()->get();

        return view('books.owner', compact('books',));
    }

    public function edit(Book $book): View|RedirectResponse
    {   
        if(url()->current() != url()->previous()) {
            session()->put('url.back', url()->previous());
        }

        return view('books.edit', compact('book'));
    }

    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        $book->title = $request->input('title');
        $book->summary = $request->input('summary');
        $book->published_at = $request->input('published_at');

        $book->author()->associate($request->input('author_id'));

        $book->categories()->sync($request->input('categories'));

        if($request->hasFile('cover')) {
            $cover = $request->file('cover');

            $book->cover_image = $request->file('cover')->storeAs('covers',  $book->id . '.' . $cover->extension(), ['disk' => 'public']);
        }

        $book->save();

        return redirect()->route('books.show', ['book' => $book])->with('success', 'Book updated successfully!');
    }   
}
