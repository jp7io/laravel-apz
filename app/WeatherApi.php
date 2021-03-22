<?php

namespace App;

use Cache;
use Carbon\Carbon;

class WeatherApi
{

    public function data()
    {
        return Cache::get('Weather.reads') ?: $this->update();
    }

    public function update($urlApi = null)
    {
        if (is_null($urlApi)) {
            $urlApi = 'https://api.openweathermap.org/data/2.5/weather?q=Sao%20Paulo,br&units=metric&APPID=' . getenv('WEATHER_APPID');
        }

        $json = file_get_contents($urlApi);
        $response = json_decode($json);
        $data = $response->main;
        $data->updated_at = Carbon::now()->toDateTimeString();

        Cache::forever('Weather.reads', $data);

        return $data;
    }
}
