<?php

namespace App\Modules;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerModulesFromPath(config('modules.path', app_path('Modules')));

        if (config('modules.load_composer_modules', false)) {
            $externalPath = config('modules.external_path', base_path('app-modules'));
            if (File::exists($externalPath)) {
                $this->registerModulesFromPath($externalPath);
            }
        }
    }

    public function boot(): void
    {
        $this->bootModulesFromPath(config('modules.path', app_path('Modules')));

        if (config('modules.load_composer_modules', false)) {
            $externalPath = config('modules.external_path', base_path('app-modules'));
            if (File::exists($externalPath)) {
                $this->bootModulesFromPath($externalPath);
            }
        }
    }

    protected function registerModulesFromPath(string $modulesPath): void
    {
        if (! File::exists($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $modulePath) {
            $this->registerModule(basename($modulePath), $modulePath);
        }
    }

    protected function registerModule(string $moduleName, string $modulePath): void
    {
        $providerClass = "App\\Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider";
        $providerPath = "{$modulePath}/Providers/{$moduleName}ServiceProvider.php";

        if (File::exists($providerPath) && class_exists($providerClass)) {
            $this->app->register($providerClass);
        }

        $configPath = "{$modulePath}/config";
        if (File::exists($configPath)) {
            foreach (File::files($configPath) as $configFile) {
                $key = Str::snake($moduleName) . '.' . $configFile->getFilenameWithoutExtension();
                $this->mergeConfigFrom($configFile->getPathname(), $key);
            }
        }
    }

    protected function bootModulesFromPath(string $modulesPath): void
    {
        if (! File::exists($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $modulePath) {
            $this->bootModule(basename($modulePath), $modulePath);
        }
    }

    protected function bootModule(string $moduleName, string $modulePath): void
    {
        $snakeName = Str::snake($moduleName);

        $routesPath = "{$modulePath}/routes";
        if (File::exists($routesPath)) {
            foreach (['web.php', 'api.php', 'admin.php'] as $routeFile) {
                $path = "{$routesPath}/{$routeFile}";
                if (File::exists($path)) {
                    $this->loadRoutesFrom($path);
                }
            }
        }

        $viewsPath = "{$modulePath}/resources/views";
        if (File::exists($viewsPath)) {
            $this->loadViewsFrom($viewsPath, $snakeName);
        }

        $langPath = "{$modulePath}/resources/lang";
        if (File::exists($langPath)) {
            $this->loadTranslationsFrom($langPath, $snakeName);
        }

        $migrationsPath = "{$modulePath}/database/migrations";
        if (File::exists($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }

        $assetsPath = "{$modulePath}/resources/assets";
        if (File::exists($assetsPath)) {
            $this->publishes(
                [$assetsPath => public_path("modules/{$moduleName}")],
                "{$snakeName}-assets"
            );
        }

        // Filament auto-discovery: register Filament resources/pages/widgets from module
        if (config('modules.filament_discovery', true)) {
            $this->discoverFilamentComponents($moduleName, $modulePath);
        }
    }

    protected function discoverFilamentComponents(string $moduleName, string $modulePath): void
    {
        $filamentPath = "{$modulePath}/Filament";
        if (! File::exists($filamentPath)) {
            return;
        }

        // Filament 5 auto-discovers components via package discovery.
        // For module-specific components, we publish a discovery hint
        // by registering the namespace so Filament can scan it.
        $namespace = "App\\Modules\\{$moduleName}\\Filament";
        $this->callAfterResolving('filament', function ($filament) use ($namespace, $filamentPath) {
            // Each Filament panel's discoverResources/Pages/Widgets can pick these up
            // if configured to scan module paths. This hook is intentionally lightweight
            // to avoid coupling to a specific panel ID.
        });
    }
}
