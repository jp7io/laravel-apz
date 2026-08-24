@php($weather = app(App\Services\WeatherApi::class)->current())

@if ($weather)
    <p {{ $attributes->merge(['class' => 'text-sm text-slate-500']) }}>
        {{ $weather->city }}: {{ $weather->temperature }} Celsius.
        Last update {{ $weather->updatedAt->diffForHumans() }}
    </p>
@endif
