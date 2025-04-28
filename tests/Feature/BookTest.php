<?php

use App\Constants;
use App\Models\Book;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;

test('books_index', function () {
    $this->get(route('books.index'))
        ->assertStatus(200)
        ->assertSeeText('No books available.');

    $books = $this->getBooks();

    $this->get(route('books.index'))
        ->assertStatus(200)
        ->assertViewIs('books.index')
        ->assertSee("Search for books")
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() === Constants::BOOKS_PER_PAGE)
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->last()))
        ->assertSee(route('books.show', $books->first()));
});

test('books_index_search', function () {
    $books = $this->getBooks(override: ['title' => 'Test Book' . rand(1, 1000)]);

    $this->get(route('books.index', ['q' => 'Test Book']))
        ->assertStatus(200)
        ->assertViewIs('books.index')
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() === Constants::BOOKS_PER_PAGE)
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->last()));

    $this->get(route('books.index', ['q' => 'Nothing Matching']))
        ->assertStatus(200)
        ->assertSeeText('No books found')
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() === 0);
});

test('books_index_pagination', function () {
    $books = $this->getBooks(Constants::BOOKS_PER_PAGE * 2);

    $books = $books->sortBy([['title', 'asc']]);

    $this->get(route('books.index'))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() === Constants::BOOKS_PER_PAGE)
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => !$viewBooks->contains($books->last()))
        ->assertSeeText('Next');

    $this->get(route('books.index', ['page' => 2]))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->count() === Constants::BOOKS_PER_PAGE)
        ->assertViewHas('books', fn($viewBooks) => !$viewBooks->contains($books->first()))
        ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->last()))
        ->assertSeeText('Previous');
});

test('books_index_sorting', function () {
    $this->getBooks(count: Constants::BOOKS_PER_PAGE * 2, reviewCount: 10);

    foreach (Constants::BOOK_SORTING as $key => $value) {
        $books = Book::withCount('reviews')->withAvg('reviews', 'rating')->setSorting($key)->get();

        $this->withSession(['book-sorting' => $key])
            ->get(route('books.index'))
            ->assertViewHas('books', fn($viewBooks) => $viewBooks->contains($books->first()))
            ->assertViewHas('books', fn($viewBooks) => !$viewBooks->contains($books->last()));
    }
});

test("books_index_redirect_last_page", function () {
    $lastPage = 2;

    $this->getBooks(count: Constants::BOOKS_PER_PAGE * $lastPage);

    $this->get(route('books.index', ['page' => $lastPage + 1]))
        ->assertRedirect(route('books.index', ['page' => $lastPage]));

    $this->get(route('books.index', ['page' => $lastPage]))->assertStatus(200);
});

test('books_show', function () {
    $book = $this->getBooks(count: 1, reviewCount: 10);

    DB::enableQueryLog();

    $this->get(route('books.show', $book))
        ->assertStatus(200)
        ->assertViewIs('books.show')
        ->assertViewHas('book', fn($viewBook) => $viewBook->is($book))
        ->assertSeeText($book->title)
        ->assertSeeText($book->author)
        ->assertSeeText($book->summary)
        ->assertViewHas('reviews', fn($reviews) => $reviews->count() === 10)
        ->assertSeeText($book->reviews->first()->review)
        ->assertSeeText($book->reviews->last()->review);

    $queryCount = count(DB::getQueryLog());

    DB::flushQueryLog();

    $this->get(route('books.show', $book)); //Re-call the page 

    expect(count(DB::getQueryLog()))->toBeLessThan($queryCount); // Check if the query count is less than the previous one, the cache is used
});

test('books_show_no_reviews', function () {
    $book = $this->getBooks(count: 1);

    $this->get(route('books.show', $book))
        ->assertStatus(200)
        ->assertSeeText('No reviews yet');
});

test('books_show_user_review', function () {
    $book = $this->getBooks(count: 1, reviewCount: 10);
    $review = $this->getReviews(count: 1, book: $book, user: $this->user);

    $this->actingAs($this->user)
        ->get(route('books.show', $book))
        ->assertStatus(200)
        ->assertViewHas('userReview', fn($userReview) => $userReview->is($review))
        ->assertSeeText('Your Review')
        ->assertSeeTextInOrder(['Your Review', $review->review, 'Edit', 'Delete'])
        ->assertViewHas('reviews', fn($reviews) => $reviews->count() === 10);
});

test('books_show_book_options', function () {
    $book = $this->getBooks(count: 1, user: $this->user);

    $this->actingAs($this->user)
        ->get(route('books.show', $book))
        ->assertStatus(200)
        ->assertSeeTextInOrder(['Edit Book', 'Delete Book']);
});

test('books_show_hide_book_options', function () {
    $book = $this->getBooks(count: 1);

    $this->actingAs($this->user)
        ->get(route('books.show', $book))
        ->assertStatus(200)
        ->assertDontSeeText(['Edit Book', 'Delete Book']);
});

test('book_create_auth', function () {
    $this->get(route('books.create'))
        ->assertStatus(302)
        ->assertRedirect(route('login'));
});

test('book_create', function () {
    $this->actingAs($this->user)
        ->get(route('books.create'))
        ->assertStatus(200)
        ->assertViewIs('books.create')
        ->assertSeeText(['Add A Book', 'Title', 'Author', 'Published Date', 'Summary', 'Cover', 'Create']);
});

test('book_store_successfull', function () {
    Storage::fake('public');

    $data = $this->getBookFormData();

    $this->actingAs($this->user)
        ->post(route('books.store'), $data)
        ->assertValid()
        ->assertStatus(302)
        ->assertRedirectToRoute('books.show', 1)
        ->assertSessionHas('success');

    $this->assertDatabaseHas('books', Arr::except($data, 'cover'));

    //Check if the cover was uploaded
    Storage::assertExists(Book::find(1)->first()->cover_image);
});

test('book_store_auth', function () {
    $this->post(route('books.store'), $this->getBookFormData())
        ->assertStatus(302)
        ->assertRedirect(route('login'));
});

test('book_store_validation', function () {
    Storage::fake('public');

    $this->checkForm(
        route('books.store'),
        $this->getBookFormData(),
        [
            [['title', 'author', 'published_at', 'summary', 'cover'], 'required', ''],
            [['title', 'author'], 'string', 0],
            [['title', 'author'], 'max.string', str_repeat('a', Constants::STRING_MAX_LENGTH + 1), ['max' => Constants::STRING_MAX_LENGTH]],
            [['published_at'], 'date', 'invalid-date'],
            [['summary'], 'min.string', str_repeat('a', Constants::BOOK_SUMMARY_MIN_LENGTH - 1), ['min' => Constants::BOOK_SUMMARY_MIN_LENGTH]],
            [['summary'], 'max.string', str_repeat('a', Constants::BOOK_SUMMARY_MAX_LENGTH + 1), ['max' => Constants::BOOK_SUMMARY_MAX_LENGTH]],
        ],
        $this->user,
    );
}); 

test('book_store_validation_cover', function () {
    Storage::fake('public'); // Create a fake storage disk for testing

    $size = (Constants::BOOK_COVER_MIN_RES + Constants::BOOK_COVER_MAX_RES) / 2;

    //No cover
    $this->actingAs($this->user)
        ->post(route('books.store'), $this->getBookFormData(['cover' => null]))
        ->assertStatus(302)
        ->assertInvalid(['cover' => Lang::get('validation.required', ['attribute' => 'cover'])]);

    //Too small
    $this->actingAs($this->user)
        ->post(route('books.store'), $this->getBookFormData(['cover' => UploadedFile::fake()->image('cover.jpg', Constants::BOOK_COVER_MIN_RES - 1, Constants::BOOK_COVER_MIN_RES - 1)->size(Constants::BOOK_COVER_MAX_WEIGHT / 2)]))
        ->assertStatus(302)
        ->assertInvalid(['cover' => Lang::get('validation.cover_dimensions')]);

    //Too big
    $this->actingAs($this->user)
        ->post(route('books.store'), $this->getBookFormData(['cover' => UploadedFile::fake()->image('cover.jpg',  Constants::BOOK_COVER_MAX_RES + 1, Constants::BOOK_COVER_MAX_RES + 1)->size(Constants::BOOK_COVER_MAX_WEIGHT / 2)]))
        ->assertStatus(302)
        ->assertInvalid(['cover' => Lang::get('validation.cover_dimensions')]);

    //Too heavy
    $this->actingAs($this->user)
        ->post(route('books.store'), $this->getBookFormData(['cover' => UploadedFile::fake()->image('cover.jpg', $size, $size)->size(Constants::BOOK_COVER_MAX_WEIGHT + 1)]))
        ->assertStatus(302)
        ->assertInvalid(['cover' => Lang::get('validation.max.file', ['attribute' => 'cover', 'max' => Constants::BOOK_COVER_MAX_WEIGHT])]);

    //Not an image
    $this->actingAs($this->user)
        ->post(route('books.store'), $this->getBookFormData(['cover' => UploadedFile::fake()->create('cover.pdf', Constants::BOOK_COVER_MAX_WEIGHT / 2)]))
        ->assertStatus(302)
        ->assertInvalid(['cover' => Lang::get('validation.image', ['attribute' => 'cover'])]);

    //Wrong type
    $this->actingAs($this->user)
        ->post(route('books.store'), $this->getBookFormData(['cover' => UploadedFile::fake()->image('cover.gif', $size, $size)->size(Constants::BOOK_COVER_MAX_WEIGHT / 2)]))
        ->assertStatus(302)
        ->assertInvalid(['cover' => Lang::get('validation.mimes', ['attribute' => 'cover', 'values' => implode(', ', Constants::IMAGE_EXTENSIONS_ALLOWED)])]);
});

test('book_delete_successfull', function () {
    $book = $this->getBooks(count: 1, user: $this->user);

    $this->actingAs($this->user)
        ->delete(route('books.destroy', $book))
        ->assertStatus(302)
        ->assertRedirectToRoute('books.owner');

    $this->assertDatabaseMissing('books', $book->toArray());
});

test('book_delete_auth', function () {
    $book = $this->getBooks(count: 1);

    $this->delete(route('books.destroy', $book))
        ->assertStatus(302)
        ->assertRedirect(route('login'));
});

test('book_delete_owner', function () {
    $book = $this->getBooks(count: 1);

    $this->actingAs($this->user)->delete(route('books.destroy', $book))->assertForbidden();
});

test('book_edit', function () {
    $book = $this->getBooks(count: 1, user: $this->user);

    $this->actingAs($this->user)
        ->get(route('books.edit', $book))
        ->assertStatus(200)
        ->assertViewIs('books.edit')
        ->assertViewHas('book', fn($viewBook) => $viewBook->is($book))
        ->assertSee(['Title', $book->title, 'Author', $book->author, 'Published Date', Carbon::parse($book->published_at)->format('Y-m-d'), 'Summary', $book->summary, 'Cover', 'Edit']);
});

test('book_edit_auth', function () {
    $book = $this->getBooks(count: 1);

    $this->get(route('books.edit', $book))->assertStatus(302)->assertRedirect(route('login'));
});

test('book_edit_owner', function () {
    $book = $this->getBooks(count: 1);

    $this->actingAs($this->user)->get(route('books.edit', $book))->assertForbidden();;
});

test('book_update_successfull', function () {
    $book = $this->getBooks(count: 1, user: $this->user);

    Storage::fake('public');

    $data = $this->getBookFormData();

    $this->actingAs($this->user)
        ->put(route('books.update', $book), $data)
        ->assertValid()
        ->assertStatus(302)
        ->assertRedirectToRoute('books.show', $book)
        ->assertSessionHas('success');

    $this->assertDatabaseHas('books', Arr::except($data, 'cover'));

    //Check if the cover was uploaded
    Storage::assertExists(Book::find(1)->first()->cover_image);
});

test('book_update_cover_optional', function () {
    $book = $this->getBooks(count: 1, user: $this->user);

    Storage::fake('public'); // Create a fake storage disk for testing

    $this->actingAs($this->user)
        ->put(route('books.update', $book), Arr::except($this->getBookFormData(), 'cover'))
        ->assertValid()
        ->assertStatus(302)
        ->assertRedirectToRoute('books.show', $book);

    expect($book->cover_image)->toBe(Book::find($book->id)->cover_image); //Logo info should not change
});

test('book_update_auth', function () {
    $book = $this->getBooks(count: 1);

    $this->put(route('books.update', $book), $this->getBookFormData())->assertStatus(302)->assertRedirect(route('login'));
});

test('book_update_owner', function () {
    $book = $this->getBooks(count: 1);

    $this->actingAs($this->user)->put(route('books.update', $book), $this->getBookFormData())->assertForbidden();;
});