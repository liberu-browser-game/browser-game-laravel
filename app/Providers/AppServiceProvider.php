<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use App\Modules\ModuleManager;
use App\Modules\ModuleServiceProvider;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\PermissionRegistrar;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ModuleManager::class, function ($app) {
            return new ModuleManager();
        });

        $this->app->register(ModuleServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use custom Permission and Role models
        $this->app->make(PermissionRegistrar::class)
            ->setPermissionClass(Permission::class)
            ->setRoleClass(Role::class);

    }
}
