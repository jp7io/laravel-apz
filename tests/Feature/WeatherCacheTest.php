<?php

namespace Tests\Feature;

use App\Services\Weather;
use App\Services\WeatherApi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * The cache round-trip on a store that actually serializes.
 *
 * phpunit.xml runs on the `array` store, which holds live references and therefore cannot see a
 * value the framework refuses to unserialize. That gap hid a 500 on every page.
 */
class WeatherCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.default' => 'database',
            'services.openweather.key' => 'test-key',
            'services.openweather.city' => 'Sao Paulo,br',
        ]);

        Http::fake(['api.openweathermap.org/*' => Http::response(['name' => 'Sao Paulo', 'main' => ['temp' => 18]])]);
    }

    public function test_the_reading_survives_a_serializing_store(): void
    {
        $api = app(WeatherApi::class);
        $api->refresh();

        $weather = $api->current();

        $this->assertInstanceOf(Weather::class, $weather);
        $this->assertSame('Sao Paulo', $weather->city);
        $this->assertSame(18.0, $weather->temperature);
    }

    public function test_the_navbar_shows_the_reading(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Sao Paulo: 18 Celsius');
    }

    public function test_the_navbar_stays_quiet_without_a_key(): void
    {
        config(['services.openweather.key' => null]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Celsius');
    }
}
