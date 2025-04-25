@props(['action' => 'Edit', 'book', 'review' => null])

<form action="{{ $action === 'Edit' ? route('reviews.update', [$book->id, $review->id]) : route('reviews.store') }}" method="POST">
    @csrf
    @if ($action === 'Edit')
        @method('PUT')
    @endif

    <x-forms.text name='review' label='Review' required>{{ old('review', $review?->review) }}</x-forms.text>

    <div class="relative my-6">
        <input name="rating" id="rating" type="range" value="{{ old('rating', $review?->rating ?? 2.5) }}" min="0" max="5" step="0.5" class="w-full h-2 bg-white rounded-lg cursor-pointer">
        <div>Rating: <span id=rating_value>{{ old('rating', $review?->rating ?? 2.5) }}</span></div>
        <x-forms.error name="rating" />
    </div>

    <div class="flex gap-x-2 justify-between">
        <x-link-button href="{{ session('url.back', url()->previous()) }}">Cancel</x-link-button>
        <x-forms.button color='blue'>{{ $action }}</x-forms.button>
    </div>
</form>

@push('footer')
    <script>
        const value = document.querySelector("#rating_value");
        const input = document.querySelector("#rating");
        value.textContent = input.value;
        input.addEventListener("input", (event) => {
            value.textContent = event.target.value;
        });
    </script>
@endpush
