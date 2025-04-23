@props(['color' => 'white'])

@php
    switch ($color) {
        case 'blue':
            $colorInfo = ' bg-blue-500 hover:bg-blue-600 focus:ring-blue-300';
            break;
        case 'green':
            $colorInfo = ' bg-green-700 hover:bg-green-600 focus:ring-green-300';
            break;        
        case 'red':
            $colorInfo = ' bg-red-500 hover:bg-red-600 focus:ring-red-300';
            break;
        default:
            $colorInfo = ' text-black bg-white/90 border-white hover:bg-white focus:ring-white/50';
    }
@endphp

<a {{ $attributes->merge(['class' => "min-w-[100px] inline-block text-center font-bold rounded text-sm w-full sm:w-auto px-5 py-1.5 sm:py-2.5 focus:ring-2 focus:outline-none" . $colorInfo]) }}>{{ $slot }}</a>