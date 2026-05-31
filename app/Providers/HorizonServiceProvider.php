<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class HorizonServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // No bindings required
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Allow Horizon dashboard only for users with the 'super_admin' role.
        // This uses spatie/laravel-permission's hasRole method.
        Horizon::auth(fn ($request) =>
            $request->user() && method_exists($request->user(), 'hasRole') && $request->user()->hasRole('super_admin')
        );
    }
}
