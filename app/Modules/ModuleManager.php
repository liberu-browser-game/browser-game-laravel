<?php

namespace App\Modules;

use Exception;
use App\Modules\Contracts\ModuleInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

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
        $this->flushCache();

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
        $this->flushCache();

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
        $this->flushCache();

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
        $this->flushCache();

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
            'name' => $module->getName(),
            'version' => $module->getVersion(),
            'description' => $module->getDescription(),
            'dependencies' => $module->getDependencies(),
            'enabled' => $module->isEnabled(),
            'config' => $module->getConfig(),
        ];
    }

    public function getAllModulesInfo(): array
    {
        return $this->modules->map(fn ($module) => $this->getModuleInfo($module->getName()))->toArray();
    }

    public function flushCache(): void
    {
        Cache::forget(config('modules.cache_key', 'app.modules'));
    }

    protected function loadModules(): void
    {
        // In development mode skip caching so changes are picked up immediately
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

        if (config('modules.load_composer_modules', false)) {
            $externalPath = config('modules.external_path', base_path('app-modules'));
            if (File::exists($externalPath)) {
                $this->loadFromPath($externalPath, isExternal: true);
            }
        }
    }

    protected function loadFromPath(string $modulesPath, bool $isExternal = false): void
    {
        if (! File::exists($modulesPath)) {
            return;
        }

        foreach (File::directories($modulesPath) as $modulePath) {
            $moduleName = basename($modulePath);
            $this->loadModule($moduleName, $modulePath, $isExternal);
        }
    }

    protected function loadModule(string $moduleName, string $modulePath, bool $isExternal = false): void
    {
        $namespace = $isExternal ? "Modules\\{$moduleName}" : config('modules.namespace', 'App\\Modules');
        $moduleClass = "{$namespace}\\{$moduleName}\\{$moduleName}Module";

        if (class_exists($moduleClass)) {
            $module = new $moduleClass();
            if ($module instanceof ModuleInterface) {
                $this->register($module);
            }
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
