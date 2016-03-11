<?php

namespace App;

use Cache;
use Carbon\Carbon;

class WeatherApi
{

    public function data()
    {
        $data = Cache::get('Weather.reads');
        return Cache::get('Weather.reads') ? $data: $this->update();
    }

    public function update($urlApi = null)
    {
        if (is_null($urlApi)) {
            $urlApi = 'http://api.openweathermap.org/data/2.5/weather?q=Sao_Paulo,br&units=metric&APPID=' . getenv('WEATHER_APPID');
        }
        
        $json = file_get_contents($urlApi);
        $response = json_decode($json);
        $data = $response->main;
        $data->updated_at = Carbon::now()->toDateTimeString();

        Cache::forever('Weather.reads', $data);

        return $data;
    }
}
