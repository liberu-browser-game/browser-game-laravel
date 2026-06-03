<?php

namespace App\Modules;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerModules();
        $this->registerExternalModules();
    }

    public function boot(): void
    {
        $this->bootModules();
    }

    protected function registerModules(): void
    {
        $modulesPath = config('modules.path', app_path('Modules'));
        if (! File::exists($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $modulePath) {
            $this->registerModule(basename($modulePath), $modulePath);
        }
    }

    protected function registerModule(string $moduleName, string $modulePath): void
    {
        $isEnabled = $this->isModuleEnabled($moduleName);

        $providerPath = "{$modulePath}/Providers/{$moduleName}ServiceProvider.php";
        if (File::exists($providerPath)) {
            $providerClass = "App\\Modules\\{$moduleName}\\Providers\\{$moduleName}ServiceProvider";
            if (class_exists($providerClass)) {
                $this->app->register($providerClass);
            }
        }

        // Config is always loaded regardless of enabled state
        $configPath = "{$modulePath}/config";
        if (File::exists($configPath)) {
            foreach (File::files($configPath) as $configFile) {
                $key = Str::snake($moduleName) . '.' . $configFile->getFilenameWithoutExtension();
                $this->mergeConfigFrom($configFile->getPathname(), $key);
            }
        }

        // Migrations are always available so they can be run regardless of state
        $migrationsPath = "{$modulePath}/database/migrations";
        if (File::exists($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }

        // Routes, views, and translations only load when module is enabled
        if ($isEnabled) {
            $this->registerModuleRoutes($moduleName, $modulePath);

            $viewsPath = "{$modulePath}/resources/views";
            if (File::exists($viewsPath)) {
                $this->loadViewsFrom($viewsPath, Str::snake($moduleName));
            }

            $langPath = "{$modulePath}/resources/lang";
            if (File::exists($langPath)) {
                $this->loadTranslationsFrom($langPath, Str::snake($moduleName));
            }
        }
    }

    protected function registerModuleRoutes(string $moduleName, string $modulePath): void
    {
        $routesPath = "{$modulePath}/routes";
        if (! File::exists($routesPath)) {
            return;
        }

        foreach (['web.php', 'api.php', 'admin.php'] as $routeFile) {
            $path = "{$routesPath}/{$routeFile}";
            if (File::exists($path)) {
                $this->loadRoutesFrom($path);
            }
        }
    }

    protected function bootModules(): void
    {
        $modulesPath = config('modules.path', app_path('Modules'));
        if (! File::exists($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $modulePath) {
            $this->bootModule(basename($modulePath), $modulePath);
        }
    }

    protected function bootModule(string $moduleName, string $modulePath): void
    {
        $assetsPath = "{$modulePath}/resources/assets";
        if (File::exists($assetsPath)) {
            $this->publishes(
                [$assetsPath => public_path("modules/{$moduleName}")],
                Str::snake($moduleName) . '-assets'
            );
        }

        $configPath = "{$modulePath}/config";
        if (File::exists($configPath)) {
            foreach (File::files($configPath) as $configFile) {
                $this->publishes(
                    [$configFile->getPathname() => config_path(Str::snake($moduleName) . '.' . $configFile->getFilename())],
                    Str::snake($moduleName) . '-config'
                );
            }
        }

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

        // Filament 5 auto-discovers panels; modules simply need their namespace
        // registered so Filament's autodiscovery picks them up.
        $this->callAfterResolving('filament', static function () {});
    }

    protected function isModuleEnabled(string $moduleName): bool
    {
        try {
            if (class_exists('\\App\\Models\\Module')) {
                $record = \App\Models\Module::where('name', $moduleName)->first();
                if ($record !== null) {
                    return (bool) $record->enabled;
                }
            }
        } catch (\Throwable) {
            // DB not yet available (e.g. during installation) — default to enabled
        }

        return true;
    }

    protected function registerExternalModules(): void
    {
        if (! config('modules.load_composer_modules', false)) {
            return;
        }

        try {
            $externalPath = config('modules.external_path', base_path('app-modules'));
            if (File::exists($externalPath)) {
                foreach (File::directories($externalPath) as $modulePath) {
                    $this->registerModule(basename($modulePath), $modulePath);
                }
            }

            foreach (config('modules.external_paths', []) as $path) {
                if (is_string($path) && $path !== '' && File::exists($path)) {
                    foreach (File::directories($path) as $modulePath) {
                        $this->registerModule(basename($modulePath), $modulePath);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to load external modules: ' . $e->getMessage());
        }
    }
}
