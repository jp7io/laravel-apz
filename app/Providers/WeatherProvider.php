<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\WeatherApi;

class WeatherProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('shared.nav', function ($view) {
            $data = (new WeatherApi)->data();
            $view->with('weather', $data);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
