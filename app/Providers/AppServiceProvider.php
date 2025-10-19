<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Paksa timezone Laravel dan PHP sesuai config/app.php
        date_default_timezone_set(Config::get('app.timezone'));

        // Pastikan Carbon juga sinkron dengan timezone dan locale
        Carbon::setLocale('id');
        Carbon::setDefaultTimezone(Config::get('app.timezone'));
    }
}
