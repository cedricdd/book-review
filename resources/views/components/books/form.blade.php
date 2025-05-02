@props(['action' => 'Edit', 'book' => null])

@php
    $authors = \App\Models\Author::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id');
    $categories = \App\Models\Category::select('id', 'name')->orderBy('name')->get()->pluck('name', 'id');
@endphp

<form action="{{ $action == 'Create' ? route('books.store') : route('books.update', $book) }}" method="POST"
    enctype="multipart/form-data">
    @csrf
    @if ($action == 'Edit')
        @method('PUT')
    @endif

    <div class="mb-4">
        <x-forms.input name='title' label='Title' value="{{ old('title', $book?->title) }}" required />
    </div>
    <div class="mb-4">
        <x-forms.select name='author_id' label='Author' :items='$authors' :current="old('author_id', $book?->author->id)" required />
    </div>
    <div class="mb-4">
        <x-forms.input name="published_at" label="Published Date" type="date"
            value="{{ old('published_at', $book ? \Carbon\Carbon::parse($book->published_at)->format('Y-m-d') : null) }}"
            required />
    </div>
    <div class="mb-4">
        <x-forms.text name='summary' label='Summary' required
            placeholder="Summary needs to be at least {{ Constants::BOOK_SUMMARY_MIN_LENGTH }} characters.">{{ old('summary', $book?->summary) }}</x-forms.text>
    </div>
    <div class="mb-4">
        <x-forms.checkbock name="categories" label="Categories (Select at least {{ Constants::MIN_CATEGORIES_FOR_BOOK }}, at max {{ Constants::MAX_CATEGORIES_FOR_BOOK }})" :current="old('categories', $book?->categories->pluck('id')->toArray())" :items="$categories" />
    </div>
    <div class="mb-4">
        <x-forms.input name="cover" label="Cover (Best 600*800 px)" type="file" />
        @if ($action == 'Edit')
            <span class="italic">* Not including a cover will not remove the existing one!</span>
        @endif
    </div>
    <div class="flex gap-x-2 justify-between">
        <x-link-button href="{{ session('url.back', url()->previous()) }}">Cancel</x-link-button>
        <x-forms.button color='blue'>{{ $action }}</x-forms.button>
    </div>
</form>
