<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        if ($json = env('FIREBASE_CREDENTIALS_JSON')) {
            File::ensureDirectoryExists(storage_path('app/firebase'));

            File::put(
                storage_path('app/firebase/chefvirtuo-service-account.json'),
                $json
            );
        }
    }
}
