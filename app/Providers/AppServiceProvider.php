<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceRootUrl(config('app.url'));
        }

        // Set Carbon locale so translatedFormat() returns Spanish dates
        // everywhere: tickets, confirmation, emails, seat map, etc.
        Carbon::setLocale('es');
    }
}
