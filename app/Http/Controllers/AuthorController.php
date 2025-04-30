<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Author;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class AuthorController extends Controller
{
    public function index(Request $request): RedirectResponse|View
    {
        // Fetch authors from the database
        $authors = Author::withCount('books')->with(['books' => fn($query) => $query->select('id', 'title', 'author_id')->withAvg('reviews', 'rating')->orderBy('title')])
            ->setSorting(session('author-sorting', Constants::AUTHOR_SORTING_DEFAULT))
            ->paginate(Constants::AUTHOR_PER_PAGE);

        //Redirect to the last page if the requested page exceeds the last page
        if($request->input('page', 1) > $authors->lastPage()) {
            return redirect()->route('authors.index', ['page' => $authors->lastPage()]); 
        }

        // Return the view with the authors
        return view('authors.index', compact('authors'));
    }

    public function show(Request $request, Author $author): View
    {
        // Fetch the books for the author
        $books = $author->books()->withCount('reviews')->withAvg('reviews', 'rating')->latest()->get();

        foreach($books as $book) $book->setRelation('author', $author);

        // Return the view with the author and their books
        return view('authors.show', compact('author', 'books'));
    }
}
