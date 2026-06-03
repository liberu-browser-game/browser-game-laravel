<?php

namespace App\Modules;

use Exception;
use App\Modules\Contracts\ModuleInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ModuleManager
{
    protected Collection $modules;

    public function __construct()
    {
        $this->modules = collect();
        $this->loadModules();
    }

    public function all(): Collection
    {
        return $this->modules;
    }

    public function enabled(): Collection
    {
        return $this->modules->filter(fn ($module) => $module->isEnabled());
    }

    public function disabled(): Collection
    {
        return $this->modules->filter(fn ($module) => ! $module->isEnabled());
    }

    public function get(string $name): ?ModuleInterface
    {
        return $this->modules->first(fn ($module) => $module->getName() === $name);
    }

    public function find(string $name): ?ModuleInterface
    {
        return $this->get($name);
    }

    public function has(string $name): bool
    {
        return $this->modules->contains(fn ($module) => $module->getName() === $name);
    }

    public function enable(string $name): bool
    {
        $module = $this->get($name);
        if (! $module) {
            return false;
        }

        if (! $this->checkDependencies($module)) {
            throw new Exception("Module {$name} has unmet dependencies.");
        }

        $module->enable();
        $this->persistState($name, true, $module);
        $this->clearCache();

        return true;
    }

    public function disable(string $name): bool
    {
        $module = $this->get($name);
        if (! $module) {
            return false;
        }

        if ($this->hasDependents($name)) {
            throw new Exception("Cannot disable module {$name} as other modules depend on it.");
        }

        $module->disable();
        $this->persistState($name, false);
        $this->clearCache();

        return true;
    }

    public function install(string $name): bool
    {
        $module = $this->get($name);
        if (! $module) {
            return false;
        }

        if (! $this->checkDependencies($module)) {
            throw new Exception("Module {$name} has unmet dependencies.");
        }

        $module->install();
        $this->persistState($name, true, $module);
        $this->clearCache();

        return true;
    }

    public function uninstall(string $name): bool
    {
        $module = $this->get($name);
        if (! $module) {
            return false;
        }

        if ($this->hasDependents($name)) {
            throw new Exception("Cannot uninstall module {$name} as other modules depend on it.");
        }

        $module->uninstall();
        $this->persistState($name, false);
        $this->clearCache();

        return true;
    }

    public function register(ModuleInterface $module): void
    {
        $this->modules->put($module->getName(), $module);
    }

    public function getModuleInfo(string $name): array
    {
        $module = $this->get($name);
        if (! $module) {
            return [];
        }

        return [
            'name'         => $module->getName(),
            'version'      => $module->getVersion(),
            'description'  => $module->getDescription(),
            'dependencies' => $module->getDependencies(),
            'enabled'      => $module->isEnabled(),
            'config'       => $module->getConfig(),
        ];
    }

    public function getAllModulesInfo(): array
    {
        return $this->modules
            ->map(fn ($module) => $this->getModuleInfo($module->getName()))
            ->values()
            ->toArray();
    }

    /** Check health for all enabled modules. */
    public function checkHealth(): array
    {
        $errors   = [];
        $warnings = [];

        foreach ($this->enabled() as $module) {
            foreach ($module->getDependencies() as $dep) {
                $depModule = $this->get($dep);
                if (! $depModule) {
                    $errors[] = "Module '{$module->getName()}' requires '{$dep}' which is not installed.";
                } elseif (! $depModule->isEnabled()) {
                    $errors[] = "Module '{$module->getName()}' requires '{$dep}' which is disabled.";
                }
            }
        }

        return ['errors' => $errors, 'warnings' => $warnings];
    }

    /** Check health for a single named module. */
    public function checkModuleHealth(string $name): array
    {
        $module = $this->get($name);

        if (! $module) {
            return ['healthy' => false, 'errors' => ['Module not found'], 'warnings' => []];
        }

        $errors   = [];
        $warnings = [];

        foreach ($module->getDependencies() as $dep) {
            $depModule = $this->get($dep);
            if (! $depModule) {
                $errors[] = "Dependency '{$dep}' not found.";
            } elseif (! $depModule->isEnabled()) {
                $warnings[] = "Dependency '{$dep}' is disabled.";
            }
        }

        if ($module->isEnabled() && ! $this->checkDependencies($module)) {
            $errors[] = 'Module is enabled but has unmet dependencies.';
        }

        return ['healthy' => empty($errors), 'errors' => $errors, 'warnings' => $warnings];
    }

    public function clearCache(): void
    {
        Cache::forget(config('modules.cache_key', 'app.modules'));
    }

    /** @deprecated Use clearCache() */
    public function flushCache(): void
    {
        $this->clearCache();
    }

    protected function persistState(string $name, bool $enabled, ?ModuleInterface $module = null): void
    {
        try {
            if (class_exists('\\App\\Models\\Module')) {
                $data = ['enabled' => $enabled];
                if ($module !== null) {
                    $data['version']      = $module->getVersion();
                    $data['description']  = $module->getDescription();
                    $data['dependencies'] = $module->getDependencies();
                    $data['config']       = $module->getConfig();
                }
                \App\Models\Module::updateOrCreate(['name' => $name], $data);
            }
        } catch (\Throwable $e) {
            Log::warning("Failed to persist state for module '{$name}': " . $e->getMessage());
        }
    }

    protected function loadModules(): void
    {
        if (config('modules.development', false)) {
            $this->discoverModules();
            return;
        }

        if (config('modules.cache', true)) {
            $cached = Cache::get(config('modules.cache_key', 'app.modules'));
            if ($cached !== null) {
                $this->modules = collect($cached);
                return;
            }
        }

        $this->discoverModules();

        if (config('modules.cache', true)) {
            Cache::put(
                config('modules.cache_key', 'app.modules'),
                $this->modules->toArray(),
                config('modules.cache_ttl', 3600)
            );
        }
    }

    protected function discoverModules(): void
    {
        $this->loadFromPath(config('modules.path', app_path('Modules')));

        // app-modules directory (internachi/modular-style)
        $modularPath = base_path(config('modular.modules_directory', 'app-modules'));
        if (File::exists($modularPath)) {
            $this->loadFromPath($modularPath, isExternal: true, namespace: config('modular.modules_namespace', 'Modules'));
        }

        if (config('modules.load_composer_modules', false)) {
            $externalPath = config('modules.external_path', base_path('app-modules'));
            if (File::exists($externalPath)) {
                $this->loadFromPath($externalPath, isExternal: true);
            }

            foreach (config('modules.external_paths', []) as $path) {
                if (is_string($path) && $path !== '' && File::exists($path)) {
                    $this->loadFromPath($path, isExternal: true);
                }
            }
        }
    }

    protected function loadFromPath(string $modulesPath, bool $isExternal = false, string $namespace = ''): void
    {
        if (! File::exists($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $modulePath) {
            $this->loadModule(basename($modulePath), $modulePath, $isExternal, $namespace);
        }
    }

    protected function loadModule(string $moduleName, string $modulePath, bool $isExternal = false, string $namespace = ''): void
    {
        if ($namespace !== '') {
            $moduleClass = "{$namespace}\\{$moduleName}\\{$moduleName}Module";
        } else {
            $moduleClass = $isExternal
                ? "Modules\\{$moduleName}\\{$moduleName}Module"
                : config('modules.namespace', 'App\\Modules') . "\\{$moduleName}\\{$moduleName}Module";
        }

        if (! class_exists($moduleClass)) {
            $mainFile = "{$modulePath}/{$moduleName}Module.php";
            if (File::exists($mainFile)) {
                try {
                    require_once $mainFile;
                } catch (\Throwable $e) {
                    Log::warning("Failed requiring module file for {$moduleName}: " . $e->getMessage());
                }
            }
        }

        if (! class_exists($moduleClass)) {
            return;
        }

        try {
            $module = new $moduleClass();
        } catch (\Throwable $e) {
            Log::warning("Failed instantiating module {$moduleClass}: " . $e->getMessage());
            return;
        }

        if (! ($module instanceof ModuleInterface)) {
            return;
        }

        $this->register($module);

        try {
            if (class_exists('\\App\\Models\\Module')) {
                \App\Models\Module::updateOrCreate(
                    ['name' => $module->getName()],
                    [
                        'version'      => $module->getVersion(),
                        'description'  => $module->getDescription(),
                        'dependencies' => $module->getDependencies(),
                        'config'       => $module->getConfig(),
                    ]
                );
            }
        } catch (\Throwable) {
            // DB not available during boot — skip metadata persistence
        }
    }

    protected function checkDependencies(ModuleInterface $module): bool
    {
        foreach ($module->getDependencies() as $dependency) {
            $dep = $this->get($dependency);
            if (! $dep || ! $dep->isEnabled()) {
                return false;
            }
        }

        return true;
    }

    protected function hasDependents(string $moduleName): bool
    {
        return $this->enabled()->contains(
            fn ($module) => in_array($moduleName, $module->getDependencies())
        );
    }
}
