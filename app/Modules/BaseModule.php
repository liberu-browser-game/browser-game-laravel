<?php

namespace App\Modules;

use App\Modules\Contracts\ModuleInterface;
use App\Modules\Events\ModuleDisabled;
use App\Modules\Events\ModuleEnabled;
use App\Modules\Events\ModuleInstalled;
use App\Modules\Events\ModuleUninstalled;
use Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ReflectionClass;

abstract class BaseModule implements ModuleInterface
{
    protected string $name;
    protected string $version;
    protected string $description;
    protected array $dependencies = [];
    protected array $config = [];

    public function __construct()
    {
        $this->loadModuleInfo();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function isEnabled(): bool
    {
        try {
            if (class_exists('\\App\\Models\\Module')) {
                $record = \App\Models\Module::where('name', $this->getName())->first();
                if ($record !== null) {
                    return (bool) $record->enabled;
                }
            }
        } catch (\Throwable $e) {
            Log::debug("Could not read module state from DB for {$this->getName()}: " . $e->getMessage());
        }

        // Fallback: in-process cache when no DB record exists
        return Cache::get("module.{$this->name}.enabled", $this->config['enabled'] ?? false);
    }

    public function enable(): void
    {
        if ($this->isEnabled()) {
            Log::info("Module {$this->getName()} is already enabled.");
            return;
        }

        Log::info("Enabling module: {$this->getName()}");

        try {
            $this->onEnable();
        } catch (\Throwable $e) {
            $message = "Failed to enable module {$this->getName()}: " . $e->getMessage();
            Log::error($message, ['module' => $this->getName()]);
            throw new \RuntimeException($message, 0, $e);
        }

        Cache::put("module.{$this->name}.enabled", true);

        try {
            event(new ModuleEnabled($this->getName(), $this));
        } catch (\Throwable $e) {
            Log::debug("Failed to dispatch ModuleEnabled event for {$this->getName()}: " . $e->getMessage());
        }
    }

    public function disable(): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        try {
            $this->onDisable();
        } catch (\Throwable $e) {
            Log::warning("onDisable failed for {$this->getName()}: " . $e->getMessage());
        }

        Cache::put("module.{$this->name}.enabled", false);

        try {
            event(new ModuleDisabled($this->getName(), $this));
        } catch (\Throwable $e) {
            Log::debug("Failed to dispatch ModuleDisabled event for {$this->getName()}: " . $e->getMessage());
        }
    }

    public function install(): void
    {
        Log::info("Installing module: {$this->getName()}");

        try {
            $this->runMigrations();
            Log::info("Migrations completed for module: {$this->getName()}");
        } catch (\Throwable $e) {
            Log::error("Migration failed for module {$this->getName()}: " . $e->getMessage());
            throw $e;
        }

        try {
            $this->publishAssets();
            Log::info("Assets published for module: {$this->getName()}");
        } catch (\Throwable $e) {
            Log::warning("Asset publishing failed for module {$this->getName()}: " . $e->getMessage());
        }

        $this->onInstall();
        $this->enable();

        try {
            event(new ModuleInstalled($this->getName(), $this));
        } catch (\Throwable $e) {
            Log::debug("Failed to dispatch ModuleInstalled event for {$this->getName()}: " . $e->getMessage());
        }

        Log::info("Module {$this->getName()} installed successfully");
    }

    public function uninstall(): void
    {
        $this->disable();
        $this->rollbackMigrations();
        $this->removeAssets();
        $this->onUninstall();

        try {
            event(new ModuleUninstalled($this->getName(), $this));
        } catch (\Throwable $e) {
            Log::debug("Failed to dispatch ModuleUninstalled event for {$this->getName()}: " . $e->getMessage());
        }
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    protected function loadModuleInfo(): void
    {
        $modulePath = $this->getModulePath();
        $moduleInfoPath = $modulePath . '/module.json';

        if (File::exists($moduleInfoPath)) {
            $moduleInfo = json_decode(File::get($moduleInfoPath), true);

            $this->name        = $moduleInfo['name']         ?? class_basename($this);
            $this->version     = $moduleInfo['version']      ?? '1.0.0';
            $this->description = $moduleInfo['description']  ?? '';
            $this->dependencies = $moduleInfo['dependencies'] ?? [];
            $this->config      = $moduleInfo['config']       ?? [];
        }
    }

    protected function getModulePath(): string
    {
        $reflection = new ReflectionClass($this);
        return dirname($reflection->getFileName());
    }

    protected function runMigrations(): void
    {
        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $this->name)) {
            Log::error("Invalid module name for migrations: {$this->name}");
            throw new \InvalidArgumentException("Invalid module name: {$this->name}");
        }

        $migrationsPath = $this->getModulePath() . '/database/migrations';

        if (! File::exists($migrationsPath)) {
            return;
        }

        $expectedBase = realpath(app_path('Modules/' . $this->name));
        $actualBase   = realpath($this->getModulePath());

        if ($expectedBase === false || $actualBase === false || strpos($actualBase, $expectedBase) !== 0) {
            Log::error("Module path validation failed for: {$this->name}");
            throw new \RuntimeException('Invalid module path');
        }

        Artisan::call('migrate', [
            '--path'  => 'app/Modules/' . $this->name . '/database/migrations',
            '--force' => true,
        ]);
    }

    protected function rollbackMigrations(): void
    {
        if (! preg_match('/^[a-zA-Z0-9_-]+$/', $this->name)) {
            Log::error("Invalid module name for migration rollback: {$this->name}");
            throw new \InvalidArgumentException("Invalid module name: {$this->name}");
        }

        $migrationsPath = $this->getModulePath() . '/database/migrations';

        if (! File::exists($migrationsPath)) {
            return;
        }

        $expectedBase = realpath(app_path('Modules/' . $this->name));
        $actualBase   = realpath($this->getModulePath());

        if ($expectedBase === false || $actualBase === false || strpos($actualBase, $expectedBase) !== 0) {
            Log::error("Module path validation failed for rollback: {$this->name}");
            throw new \RuntimeException('Invalid module path');
        }

        try {
            Artisan::call('migrate:rollback', [
                '--path'  => 'app/Modules/' . $this->name . '/database/migrations',
                '--force' => true,
            ]);
        } catch (\Throwable $e) {
            Log::warning("Failed to rollback migrations for {$this->getName()}: " . $e->getMessage());
        }
    }

    protected function publishAssets(): void
    {
        Artisan::call('vendor:publish', [
            '--tag'   => strtolower($this->name) . '-assets',
            '--force' => true,
        ]);
    }

    protected function removeAssets(): void
    {
        $assetsPath = public_path("modules/{$this->name}");
        if (File::exists($assetsPath)) {
            File::deleteDirectory($assetsPath);
        }
    }

    protected function onEnable(): void {}
    protected function onDisable(): void {}
    protected function onInstall(): void {}
    protected function onUninstall(): void {}
}
