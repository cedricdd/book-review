<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Author;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class AuthorController extends Controller
{
    public function index(Request $request): RedirectResponse|View
    {
        // Fetch authors from the database
        $authors = Author::withCount('books')->with(['books' => fn($query) => $query->select('id', 'title', 'author_id')->orderBy('title')])
            ->setSorting(session('author-sorting', Constants::AUTHOR_SORTING_DEFAULT))
            ->paginate(Constants::AUTHOR_PER_PAGE);

        //Redirect to the last page if the requested page exceeds the last page
        if($request->input('page', 1) > $authors->lastPage()) {
            return redirect()->route('authors.index', ['page' => $authors->lastPage()]); 
        }

        // Return the view with the authors
        return view('authors.index', compact('authors'));
    }
}
