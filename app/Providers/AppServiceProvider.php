<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;

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
        // Force HTTPS in production (important for Vercel)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Create symbolic link from storage/app/public to public/storage if it doesn't exist
        $publicStorage = public_path('storage');
        $storagePath = storage_path('app/public');

        if (!file_exists($publicStorage)) {
            symlink($storagePath, $publicStorage);
        }
    }
}
