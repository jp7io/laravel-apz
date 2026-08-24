@props(['name', 'value' => null])

<textarea
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $attributes->merge([
        'rows' => 5,
        'class' => 'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none',
    ]) }}
>{{ old($name, $value) }}</textarea>
