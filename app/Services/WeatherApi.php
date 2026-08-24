<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * The reading is cached as a plain array, never as a Weather.
 *
 * Laravel ships `cache.serializable_classes => false`, so any object put in the cache comes back
 * as __PHP_Incomplete_Class. It is silent on the `array` store the test suite uses and a 500 on
 * every page under the database store the app actually runs on.
 */
class WeatherApi
{
    private const string CACHE_KEY = 'weather.current';

    private const string ENDPOINT = 'https://api.openweathermap.org/data/2.5/weather';

    private const int FAILURE_TTL = 600;

    public function current(): ?Weather
    {
        if (! $this->configured()) {
            return null;
        }

        $cached = Cache::get(self::CACHE_KEY);

        if ($cached !== null) {
            // `false` is a cached failure. Without it an outage at OpenWeather is re-dialled on
            // every single page render, since the navbar asks for the reading on all of them.
            return $cached ? Weather::fromArray($cached) : null;
        }

        $weather = $this->fetch();

        $weather === null
            ? Cache::put(self::CACHE_KEY, false, self::FAILURE_TTL)
            : Cache::forever(self::CACHE_KEY, $weather->toArray());

        return $weather;
    }

    /** Force a fetch, keeping the last known reading if the call fails. Runs hourly. */
    public function refresh(): ?Weather
    {
        if (! $this->configured()) {
            return null;
        }

        $weather = $this->fetch();

        if ($weather !== null) {
            Cache::forever(self::CACHE_KEY, $weather->toArray());
        }

        return $weather;
    }

    private function configured(): bool
    {
        return filled(config('services.openweather.key'));
    }

    private function fetch(): ?Weather
    {
        $city = (string) config('services.openweather.city');

        try {
            $response = Http::timeout(5)->get(self::ENDPOINT, [
                'q' => $city,
                'units' => 'metric',
                'appid' => config('services.openweather.key'),
            ]);
        } catch (ConnectionException) {
            return null;
        }

        if (! $response->successful()) {
            return null;
        }

        return new Weather(
            city: $response->json('name') ?: $city,
            temperature: (float) $response->json('main.temp'),
            updatedAt: CarbonImmutable::now(),
        );
    }
}
