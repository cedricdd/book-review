@props(["name" => "index"])

@php
    $active = 'bg-white/30 text-white';
    $incative = 'text-gray-300 hover:bg-white/30 hover:text-white';
@endphp

<a {{ $attributes->merge(['class' => "flex-1 rounded-md px-3 py-2 font-bold inline-block min-w-[110px] text-center " . (Route::currentRouteName() == $name ? $active : $incative)]) }} href="{{ route($name) }}">{{ $slot }}</a>
                                