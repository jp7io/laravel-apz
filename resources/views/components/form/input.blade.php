@props(['name', 'value' => null])

<input
    id="{{ $name }}"
    name="{{ $name }}"
    value="{{ old($name, $value) }}"
    {{ $attributes->merge([
        'type' => 'text',
        'class' => 'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none',
    ]) }}
>
