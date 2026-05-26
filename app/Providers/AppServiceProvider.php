<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Force HTTPS when behind Railway's proxy
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::define('viewApiDocs', function ($user = null) {
            return true;
        });
    }
}
