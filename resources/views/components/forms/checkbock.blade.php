@props(['name', 'label', 'items' => [], 'minWidth' => '150px', 'current' => []])

<label class="block mb-2 text-sm font-bold text-white dark:text-white">{{ $label }}</label>
<div class="flex flex-wrap gap-2">
    @foreach ($items as $id => $value)
        <div class="min-w-[{{ $minWidth }}]">
            <input id="inline-checkbox-{{ $id }}" type="checkbox" name="{{ $name }}[]" value="{{ $id }}" @if(array_search($id, $current) !== false) checked @endif class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <label for="inline-checkbox-{{ $id }}" class="ms-2 text-sm font-medium">{{ $value }}</label>
        </div>
    @endforeach
</div>
<x-forms.error :name="$name" />