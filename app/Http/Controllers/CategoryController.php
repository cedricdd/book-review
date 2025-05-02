<?php

namespace App\Http\Controllers;

use App\Constants;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Cache::remember('categories_index', Constants::CACHE_CATEGORIES, function () {
            $results = DB::select("
                SELECT *
                    FROM (
                        SELECT
                            c.id AS category_id,
                            c.name AS category_name,
                            c.slug AS category_slug,
                            b.id AS book_id,
                            b.cover_image AS cover,
                            AVG(r.rating) AS avg_rating,
                            COUNT(r.id) AS review_count,
                            (SELECT COUNT(*) FROM book_category WHERE book_category.category_id = c.id) AS books_count,
                            ROW_NUMBER() OVER (PARTITION BY c.id ORDER BY AVG(r.rating) DESC) as rn
                        FROM
                            categories c
                        JOIN
                            book_category bc ON bc.category_id = c.id
                        JOIN
                            books b ON b.id = bc.book_id
                        JOIN
                            reviews r ON r.book_id = b.id
                        GROUP BY
                            c.id, b.id
                        HAVING
                            COUNT(r.id) >= " . Constants::MIN_REVIEWS_FOR_CATEGORY_COVER . "
                    ) ranked
                    WHERE rn <= 5   
                ");

            $books = [];
            $categories = [];

            foreach ($results as $result) {
                $list[$result->category_name][] = [$result->book_id, $result->cover, $result->category_slug, $result->books_count];
                $books[$result->book_id][$result->category_name] = 1;
            }

            do { // This loop will run until all categories are assigned
                do { // We check all the categories that are still left
                    $added = false;

                    foreach ($list as $category => $options) {
                        // We check if this cover isn't a candidate for another category and if not we assign it
                        foreach ($options as $infos) {
                            if (count($books[$infos[0]]) == 1) {
                                $categories[$category] = $infos;

                                foreach ($options as [$bookID, , ,]) {
                                    unset($books[$bookID][$category]);
                                }

                                $added = true;
                                unset($list[$category]);
                                continue 2;
                            }
                        }
                    }
                } while ($added); //If we have assigned a category, we check again, new categories may now have valid candidates

                // All the categories that are left have candidates that are not unique to a category
                if (count($list)) {
                    $category = array_key_first($list);

                    shuffle($list[$category]); //We randomly use a candidate for this category

                    $categories[$category] = array_pop($list[$category]);

                    foreach ($list[$category] as [$bookID, , ,]) {
                        unset($books[$bookID][$category]);
                    }

                    unset($list[$category]);
                }
            } while ($list);

            ksort($categories);

            return $categories;
        });

        return view('categories.index', compact('categories'));
    }

    public function show(Request $request, Category $category): RedirectResponse|View
    {
        $books = $category->books()
            ->with(['author', 'categories' => fn($query) => $query->orderBy('name')])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->setSorting(session('book-sorting', Constants::BOOK_SORTING_DEFAULT))
            ->paginate(Constants::BOOKS_PER_PAGE);

        if ($request->has('page') && $request->input('page') > $books->lastPage()) {
            return redirect()->route('categories.show', [$category, 'page' => $books->lastPage()]);
        }

        return view('categories.show', compact('category', 'books'));
    }
}
