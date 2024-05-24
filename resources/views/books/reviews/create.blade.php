@extends("layouts.main")

@section("content")
    <h1 class="title">{{ $title }}</h1>

    <form action="{{ route('books.reviews.store', $book) }}" method="POST">
        @csrf
        <label for="review" class="font-bold text-xl">Review:</label>
        <textarea name="review" id="review" cols="30" rows="10" placeholder="Enter Your Review" class="input mt-2 mb-4 h-auto" required></textarea>
        @error("review") <p class="text-red-600 font-bold mb-2">{{ $message }}</p> @enderror

        <label for="rating" class="font-bold text-xl">Rating:</label>
        <select name="rating" id="rating" required class="input mt-2">
            @for($i = 1; $i <= 5; ++$i)
            <option value={{ $i }}>{{ $i }}</option>
            @endfor
        </select>
        @error("rating") <p class="text-red-600 font-bold mb-2">{{ $message }}</p> @enderror

        <div class="flex justify-center mt-10">
            <button type="submit" class="btn mr-1 w-32">Add Review</button>
            <a href="{{ route("books.show", $book) }}" class="btn ml-1 w-32">Cancel</a>
        </div>
    </form>
@endsection