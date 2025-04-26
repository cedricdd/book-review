@props(['action' => 'Edit', 'book' => null])

<form action="{{ $action == 'Create' ? route('books.store') : route('books.edit', $book) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($action == 'Edit')
        @method('PUT')
    @endif

    <div class="mb-4">
        <x-forms.input name='title' label='Title' value="{{ old('title', $book?->title) }}" />
    </div>
    <div class="mb-4">
        <x-forms.input name='author' label='Author' value="{{ old('author', $book?->author) }}" />
    </div>
    <div class="mb-4">
        <x-forms.input name="published_at" label="Published Date" type="date" value="{{ old('published_at', $book?->published_at) }}" />
    </div>
    <div class="mb-4">
        <x-forms.text name='summary' label='Summary' placeholder="Summary needs to be at least {{ Constants::BOOK_SUMMARY_MIN_LENGTH }} characters.">{{ old('summary', $book?->summary) }}</x-forms.text>
    </div>
    <div class="mb-4">
        <x-forms.input name="cover" label="Cover (Best 600*800 px)" type="file" />
    </div>
    <div class="flex gap-x-2 justify-between">
        <x-link-button href="{{ session('url.back', url()->previous()) }}">Cancel</x-link-button>
        <x-forms.button color='blue'>{{ $action }}</x-forms.button>
    </div>
</form>