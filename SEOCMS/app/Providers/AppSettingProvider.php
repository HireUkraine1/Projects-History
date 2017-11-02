<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Config;

class AppSettingProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Config::set('settings.' . 'current_domain', url('/'));
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

