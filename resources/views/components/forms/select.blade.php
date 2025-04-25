@props(["selectName", "label", "items" => [], "current" => null])

<label for="{{ $selectName }}" class="block mb-2 text-sm font-bold text-white dark:text-white">{{ $label }}</label>
<select id="{{ $selectName }}" name="{{ $selectName }}"
    {{ $attributes->merge(['class' => "bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"]) }}>
    @foreach ($items as $id => $value)
        <option value="{{ $id }}" @if($current == $id) selected @endif>{{ $value }}</option>
    @endforeach
</select>
<x-forms.error name="{{ $selectName }}" />