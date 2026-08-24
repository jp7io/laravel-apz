@props(['variant' => 'primary'])

@php($classes = 'inline-flex items-center justify-center rounded-lg px-3.5 py-2 text-sm font-medium shadow-sm transition '.[
    'primary' => 'bg-indigo-600 text-white hover:bg-indigo-500',
    'secondary' => 'border border-slate-300 bg-white text-slate-700 hover:bg-slate-50',
    'danger' => 'border border-red-200 bg-red-50 text-red-700 hover:bg-red-100',
][$variant])

@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>{{ $slot }}</button>
@endif
