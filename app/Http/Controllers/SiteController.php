<?php

namespace App\Http\Controllers;

use App\Constants;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function sorting(Request $request, $type)
    {
        $sorting = $request->input('sorting', '');

        if ($type == 'book') {
            //Invalid sorting, reset to default
            if (!isset(Constants::BOOK_SORTING[$sorting])) {
                session()->forget('book-sorting');
            } else {
                session()->put('book-sorting', $sorting);
            }
        } elseif ($type == 'review') {
            //Invalid sorting, reset to default
            if (!isset(Constants::REVIEW_SORTING[$sorting])) {
                session()->forget('review-sorting');
            } else {
                session()->put('review-sorting', $sorting);
            }
        } else
            abort(404);

        $parts = parse_url(url()->previous());
        parse_str($parts['query'] ?? '', $parameters);

        //We remove the 'page' parameter.
        unset($parameters['page']);

        $redirectUrl = $parts['path'] ?? route('books.index');

        //We still have some parameters
        if (count($parameters)) {
            $redirectUrl .= "?" . http_build_query($parameters);
        }

        //Redirect to the page without the bad param
        return redirect($redirectUrl, 301);
    }
}
