@props(['rating', 'reviewsCount'])

<div>
    <div class="flex gap-1" title="{{ $rating }}/5">
        @for ($i = 0; $i < floor($rating); $i++)
            <img src="{{ Vite::asset('resources/images/full.png') }}" alt="star-full">
        @endfor
        @if($rating != floor($rating))
            @if ($rating - floor($rating) >= 0.5)
                <img src="{{ Vite::asset('resources/images/half.png') }}" alt="star-half">
            @else
                <img src="{{ Vite::asset('resources/images/empty.png') }}" alt="star-empty">
            @endif
        @endif
        @for ($i = 0; $i < 5 - ceil($rating); $i++)
            <img src="{{ Vite::asset('resources/images/empty.png') }}" alt="star-empty">
        @endfor
    </div>
    <p>out of {{ $reviewsCount }} reviews</p>
</div>