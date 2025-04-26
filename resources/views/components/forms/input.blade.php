@props(["label" => null, "name"])

@isset($label)
<label for="{{ $name }}" class="block mb-2 text-sm font-bold text-gray-900 text-white">{{ $label }}</label>
@endisset
<input name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => "bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"]) }} />
<x-forms.error name="{{ $name }}" />