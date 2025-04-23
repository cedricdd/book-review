@props(['term'])

<form action="#" accept-charset="UTF-8" method="GET" class="mb-4">
    <div class="flex items-center gap-x-2">
        <input type="text" name="q" placeholder="Search for books..." class="border rounded px-4 py-2 flex-1 h-[40px]" value="{{ $term }}">
        <x-forms.button color='blue'>Search</x-forms.button>
        @if(!empty($term))
            <x-link-button href="{{ route('books.index') }}">Clear</x-link-button>
        @endif
    </div>
</form>