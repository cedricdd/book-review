@props(['name'])

@php
    $classes = "flex items-center p-4 m-2 text-sm border rounded-lg bg-red-50";

    $classes .= $name == "failure" ? " text-red-800 border-red-300 bg-red-50" : " text-green-800 border-green-300 bg-green-50";
@endphp

@if (session($name))
    <div {{ $attributes->merge(['class' => $classes]) }} role="alert">
        <span class="sr-only">Info</span>
        <div>
            <span>{{ session($name) }}</span>.
        </div>
    </div>
@endif