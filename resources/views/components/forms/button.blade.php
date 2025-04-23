@props(['color' => 'white'])

@php
    $class = "flex justify-center items-center gap-x-2 min-w-[100px] rounded-lg text-sm w-full sm:w-auto px-5 py-1.5 sm:py-2.5 cursor-pointer font-bold";

    switch ($color) {
        case 'blue':
            $class .= ' bg-blue-500 hover:bg-blue-600 focus:ring-blue-300';
            break;
        case 'green':
            $class .= ' bg-green-700 hover:bg-green-600 focus:ring-green-300';
            break;        
        case 'red':
            $class .= ' bg-red-500 hover:bg-red-600 focus:ring-red-300';
            break;
        default:
            $class .= ' text-black bg-white/90 border-white hover:bg-white focus:ring-white/50';
    }
@endphp

<button type="submit" {{ $attributes->merge(['class' => $class]) }}>{{ $slot }}</button>
