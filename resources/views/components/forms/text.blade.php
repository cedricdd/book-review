@props(["label", "name"])

<label for="{{ $name }}" class="block mb-2 text-sm font-bold text-gray-900 text-white">{{ $label }}</label>
<textarea name="{{ $name }}" id="{{ $name }}" rows=6 {{ $attributes->merge(['class' => "bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"]) }}>{{ $slot }}</textarea>
<x-forms.error name="{{ $name }}" />