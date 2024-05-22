@if($rating)
<div class="inline-flex" title="Rating: {{ number_format($rating, 2) }}">
    @for($i = 1; $i <= 5; ++$i) 
        @if($i <= floor($rating)) <img src="{{ url("/images/star3.png") }}" /> 
        @elseif($i <= floor($rating + 0.5)) <img src="{{ url("/images/star2.png") }}" /> 
        @else <img src="{{ url("/images/star1.png") }}" /> 
        @endif
    @endfor
</div>
@else
    No Rating Yet
@endif

