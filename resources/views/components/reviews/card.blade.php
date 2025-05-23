<div class="bg-white/10 rounded-lg p-4 mb-4 shadow-md hover:shadow-lg hover:bg-white/15 border-1 border-transparent hover:border-blue-500 transition duration-300 p-4 shadow/20 shadow-white">
    <div class="flex justify-between items-center">
        <x-star-rating :rating="$review->rating" />
        <p class="text-gray-400 mt-1">{{ $review->created_at->diffForHumans() }}</p>
    </div>
    <p class="my-4">{{ $review->review}}</p>
    <div class="flex justify-end">
        @if(Auth::check() && Auth::user()->id === $review->user->id)
        <div class="flex justify-end gap-x-2">
            @can('update', $review)
                <x-link-button color='blue' href="{{ route('reviews.edit', [$review->book->id, $review->id]) }}">Edit</x-link-button>
            @endcan
            @can('destroy', $review)
                <form action="{{ route('reviews.destroy', $review->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <x-forms.button color='red'>Delete</x-forms.button>
                </form>
            @endcan
        </div>
        @else
        <a href="{{ route('users.profile', $review->user->id) }}" class="text-blue-500 hover:underline">{{ $review->user->name }}</a>
        @endif
    </div>
</div>