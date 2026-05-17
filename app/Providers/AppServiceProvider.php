<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        \Dedoc\Scramble\Scramble::auth(function ($request) {
            // Allows everyone to access API documentation in production
            return true;
        });
    }
}
