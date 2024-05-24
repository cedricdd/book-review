<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Cache;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Contracts\Database\Query\Builder;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View {
        $title = $request->input("title", "");
        $filter = $request->input("filter", "latest");

        $query = Book::when($title, fn($query) => $query->title($title));

        switch($filter) {
            case "popular-month":
                $query->popular(now()->subMonth(), now())
                    ->highestRated(now()->subMonth(), now())
                    ->minReviews(2);
                break;
            case "popular-6-months":
                $query->popular(now()->subMonths(6), now())
                    ->highestRated(now()->subMonths(6), now())
                    ->minReviews(5);
                break;
            case "highest-rated-month":
                $query->highestRated(now()->subMonth(), now())
                    ->popular(now()->subMonth(), now())
                    ->minReviews(2);
                break;
            case "highest-rated-6-months":
                $query->highestRated(now()->subMonths(6), now())
                    ->popular(now()->subMonths(6), now())
                    ->minReviews(5);
                break;
            default:
                $query->withCount("reviews")->withAvg("reviews", "rating")->latest();
        }

        $books = Cache::remember("books_" . $title . "_" . $filter, 3600, fn() => $query->get());

        $filters = [
            'latest' => "Latest",
            'popular-month' => "Popular Last Month",
            'popular-6-months' => "Popular Last 6 Months",
            'highest-rated-month' => "Highest Rated Last Month",
            'highest-rated-6-months' => "Highest Rated Last 6 Months",
        ];

        return view("books.index", ['books' => $books, 'title' => "Books List", 'filters' => $filters]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Book $book): View {
        $reviews = Cache::remember('book-'. $book->id, 3600, fn() => $book->reviews()->latest()->get());

        return view("books.show", ['book' => $book, 'title' => $book->title, 'reviews' => $reviews, 'ip_address' => $request->ip()]);
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
