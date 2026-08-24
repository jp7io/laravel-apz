<?php

namespace Tests\Unit;

use App\Services\WeatherApi;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.openweather.key' => 'test-key', 'services.openweather.city' => 'Sao Paulo,br']);
    }

    public function test_it_reads_the_temperature_from_the_api(): void
    {
        Http::fake(['api.openweathermap.org/*' => Http::response(['name' => 'Sao Paulo', 'main' => ['temp' => 18]])]);

        $weather = app(WeatherApi::class)->refresh();

        $this->assertNotNull($weather);
        $this->assertSame('Sao Paulo', $weather->city);
        $this->assertSame(18.0, $weather->temperature);
    }

    public function test_it_serves_the_cached_reading_without_calling_the_api_again(): void
    {
        Http::fake(['api.openweathermap.org/*' => Http::response(['name' => 'Sao Paulo', 'main' => ['temp' => 18]])]);

        $api = app(WeatherApi::class);
        $first = $api->current();
        $second = $api->current();

        $this->assertNotNull($first);
        $this->assertNotNull($second);
        $this->assertEquals($first->updatedAt, $second->updatedAt);
        Http::assertSentCount(1);
    }

    public function test_it_returns_nothing_when_no_key_is_configured(): void
    {
        config(['services.openweather.key' => null]);
        Http::fake();

        $this->assertNull(app(WeatherApi::class)->current());
        Http::assertNothingSent();
    }

    public function test_a_refresh_without_a_key_does_not_dial_out_either(): void
    {
        config(['services.openweather.key' => null]);
        Http::fake();

        $this->assertNull(app(WeatherApi::class)->refresh());
        $this->assertSame(Command::FAILURE, Artisan::call('weather:update'));
        Http::assertNothingSent();
    }

    public function test_it_caches_a_failure_so_a_broken_upstream_is_not_re_dialled(): void
    {
        Http::fake(['api.openweathermap.org/*' => Http::response(status: 500)]);

        $api = app(WeatherApi::class);

        $this->assertNull($api->current());
        $this->assertNull($api->current());
        Http::assertSentCount(1);
    }

    public function test_a_failed_refresh_keeps_the_last_known_reading(): void
    {
        Http::fake(['api.openweathermap.org/*' => Http::sequence()
            ->push(['name' => 'Sao Paulo', 'main' => ['temp' => 18]])
            ->pushStatus(500)]);

        $api = app(WeatherApi::class);
        $api->refresh();

        $this->assertNull($api->refresh());
        $this->assertSame(18.0, Cache::get('weather.current')['temperature']);
    }
}
