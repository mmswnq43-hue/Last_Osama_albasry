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
        // Force HTTPS — Railway terminates SSL at proxy level
        // trustProxies in bootstrap/app.php handles X-Forwarded-Proto automatically
        if ($this->app->environment('production') || request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
            URL::forceScheme('https');
        }

        Gate::define('viewApiDocs', function ($user = null) {
            return true;
        });

        // Ensure required storage directories exist (Railway filesystem is ephemeral)
        $this->ensureStorageDirectories();
    }

    private function ensureStorageDirectories(): void
    {
        $dirs = [
            storage_path('app/private/livewire-tmp'),
            storage_path('app/public/receipts'),
            storage_path('logs'),
            storage_path('framework/cache/data'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
        ];

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
            }
        }

        // Ensure storage symlink exists
        $link   = public_path('storage');
        $target = storage_path('app/public');
        if (!file_exists($link) && is_dir($target)) {
            symlink($target, $link);
        }
    }
}
