<?php

namespace App\Console\Commands;

use App\Services\WeatherApi;
use Illuminate\Console\Command;

class WeatherUpdate extends Command
{
    protected $signature = 'weather:update';

    protected $description = 'Fetch the current weather reading and cache it';

    public function handle(WeatherApi $api): int
    {
        $weather = $api->refresh();

        if ($weather === null) {
            $this->error('No reading. Check services.openweather.key and the upstream API.');

            return self::FAILURE;
        }

        $this->info("{$weather->city}: {$weather->temperature} Celsius at {$weather->updatedAt}");

        return self::SUCCESS;
    }
}
