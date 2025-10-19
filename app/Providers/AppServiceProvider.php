<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Paksa timezone Laravel dan PHP sesuai config/app.php
        date_default_timezone_set(Config::get('app.timezone'));

        // Pastikan Carbon juga sinkron dengan timezone dan locale
        Carbon::setLocale('id');

        // Ini versi kompatibel untuk Carbon semua versi
        Carbon::now()->setTimezone(Config::get('app.timezone'));
    }
}
