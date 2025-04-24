@error($name)
    <p {{ $attributes->merge(['class' => "p-2 text-red-800 rounded-lg"]) }}>{{ $message }}</p>
@enderror