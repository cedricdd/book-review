<?php

namespace App\Http\Controllers;

use App\Constants;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function sorting(Request $request, $type)
    {
        if($type == 'book') {
            $sorting = $request->input('sorting', '');

            //Invalid sorting, reset to default
            if(!isset(Constants::BOOK_SORTING[$sorting])) {
                session()->forget('book-sorting');
            } else {
                session()->put('book-sorting', $sorting);
            }

            return redirect()->back();
        } else abort(404);
    }
}
