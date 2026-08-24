@props(['name', 'options', 'selected' => null])

<select
    id="{{ $name }}"
    name="{{ $name }}"
    {{ $attributes->merge([
        'class' => 'block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none',
    ]) }}
>
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected((string) old($name, $selected) === (string) $value)>{{ $label }}</option>
    @endforeach
</select>
