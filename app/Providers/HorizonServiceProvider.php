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
        // Allow Horizon dashboard in local/testing environments by default.
        // In production, gate access via a `viewHorizon` ability on the User model.
        Horizon::auth(fn ($request) => app()->environment('local') || (
            $request->user() && method_exists($request->user(), 'can') && $request->user()->can('viewHorizon')
        ));
    }
}
