@props(['color' => 'white', 'size' => 'normal'])

@php
    $classes = 'min-w-[100px] font-bold rounded w-full sm:w-auto px-5 py-1.5 sm:py-2.5 focus:ring-2 focus:outline-none flex gap-x-2 items-center justify-center';

    $classes .= match ($color) {
        'blue' => ' bg-blue-500 hover:bg-blue-600 focus:ring-blue-300',
        'green' => ' bg-green-700 hover:bg-green-600 focus:ring-green-300',
        'red' => ' bg-red-500 hover:bg-red-600 focus:ring-red-300',
        default => ' text-black bg-white/90 border-white hover:bg-white focus:ring-white/50',
    };

    $classes .= match ($size) {
        'big' => ' text-2xl',
        'small' => ' text-xs',
        default => ' text-sm',
    };
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>