<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Request;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            $remote = Request::ajax() ? true : null;
            $layout = $remote ? 'layouts.ajax' : 'layouts.html';
            $view->with(compact('remote', 'layout'));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
