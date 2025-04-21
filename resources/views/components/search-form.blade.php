@props(['term'])

<form action="#" accept-charset="UTF-8" method="GET" class="mb-4">
    <div class="flex items-center gap-x-2">
        <input type="text" name="q" placeholder="Search for books..." class="border rounded px-4 py-2 flex-1 h-[40px]" value="{{ $term }}">
        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white rounded px-4 py-2 font-bold cursor-pointer">Search</button>
        @if(!empty($term))
            <a href="{{ route('books.index') }}" class="text-black bg-white hover:bg-white/90 rounded px-4 py-2 font-bold cursor-pointer">Clear</a>
        @endif
    </div>
</form>