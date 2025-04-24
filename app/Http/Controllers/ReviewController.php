<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ReviewController extends Controller
{
    public function edit($id)
    {
        // Logic to show the edit form for a review
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->back()->with('success', 'Review deleted successfully.');
    }
}
