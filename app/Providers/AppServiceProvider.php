<?php

namespace App\Providers;

use App\Http\Controllers\Admin\SettingController;
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') || request()->header('X-Forwarded-Proto') === 'https' || request()->isSecure()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        try {
            SettingController::applyMailSettings();
        } catch (\Throwable) {
            // Silently fall back during migrations / early setup
        }
    }
}
