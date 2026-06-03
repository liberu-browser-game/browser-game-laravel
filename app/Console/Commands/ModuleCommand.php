<?php

namespace App\Console\Commands;

use Exception;
use App\Modules\ModuleManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleCommand extends Command
{
    protected $signature = 'module {action} {name?} {--force} {--format=text : Output format (text|json)}';

    protected $description = 'Manage application modules';

    protected const CACHE_CLEAR_MESSAGE = "Run 'php artisan optimize:clear' to apply changes.";

    protected ModuleManager $moduleManager;

    public function __construct(ModuleManager $moduleManager)
    {
        parent::__construct();
        $this->moduleManager = $moduleManager;
    }

    public function handle(): int
    {
        $action = $this->argument('action');
        $name   = $this->argument('name');

        return match ($action) {
            'list'      => $this->listModules(),
            'enable'    => $this->enableModule($name),
            'disable'   => $this->disableModule($name),
            'install'   => $this->installModule($name),
            'uninstall' => $this->uninstallModule($name),
            'create'    => $this->createModule($name),
            'info'      => $this->showModuleInfo($name),
            'health'    => $this->checkHealth($name),
            default     => $this->showHelp(),
        };
    }

    protected function listModules(): int
    {
        $modules = $this->moduleManager->all();

        if ($this->option('format') === 'json') {
            $this->line(json_encode(
                $modules->isEmpty() ? ['modules' => []] : ['modules' => $this->moduleManager->getAllModulesInfo()],
                JSON_PRETTY_PRINT
            ));
            return 0;
        }

        if ($modules->isEmpty()) {
            $this->info('No modules found.');
            return 0;
        }

        $this->table(
            ['Name', 'Version', 'Status', 'Description'],
            $modules->map(fn ($module) => [
                $module->getName(),
                $module->getVersion(),
                $module->isEnabled() ? '<fg=green>Enabled</>' : '<fg=red>Disabled</>',
                $module->getDescription(),
            ])->toArray()
        );

        return 0;
    }

    protected function checkHealth(?string $name = null): int
    {
        if ($name !== null) {
            $result = $this->moduleManager->checkModuleHealth($name);

            if ($this->option('format') === 'json') {
                $this->line(json_encode($result, JSON_PRETTY_PRINT));
                return $result['healthy'] ? 0 : 1;
            }

            if ($result['healthy'] && empty($result['warnings'])) {
                $this->info("Module '{$name}' is healthy.");
                return 0;
            }

            foreach ($result['errors'] as $error) {
                $this->error($error);
            }
            foreach ($result['warnings'] as $warning) {
                $this->warn($warning);
            }

            return $result['healthy'] ? 0 : 1;
        }

        $result = $this->moduleManager->checkHealth();

        if ($this->option('format') === 'json') {
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
            return empty($result['errors']) ? 0 : 1;
        }

        if (empty($result['errors']) && empty($result['warnings'])) {
            $this->info('All modules are healthy.');
            return 0;
        }

        foreach ($result['errors'] as $error) {
            $this->error($error);
        }
        foreach ($result['warnings'] as $warning) {
            $this->warn($warning);
        }

        return empty($result['errors']) ? 0 : 1;
    }

    protected function enableModule(?string $name): int
    {
        if (! $name) {
            $this->error('Module name is required.');
            return 1;
        }

        try {
            $module = $this->moduleManager->find($name);
            if (! $module) {
                $this->error("Module '{$name}' not found.");
                return 1;
            }

            if ($module->isEnabled()) {
                $this->info("Module '{$name}' is already enabled.");
                return 0;
            }

            if ($this->moduleManager->enable($name)) {
                $this->info("Module '{$name}' has been enabled.");
                $this->comment(self::CACHE_CLEAR_MESSAGE);
                return 0;
            }

            $this->error("Failed to enable module '{$name}'.");
            return 1;
        } catch (Exception $e) {
            $this->error("Failed to enable module '{$name}': " . $e->getMessage());
            if ($this->option('verbose')) {
                $this->line($e->getTraceAsString());
            }
            return 1;
        }
    }

    protected function disableModule(?string $name): int
    {
        if (! $name) {
            $this->error('Module name is required.');
            return 1;
        }

        try {
            $module = $this->moduleManager->find($name);
            if (! $module) {
                $this->error("Module '{$name}' not found.");
                return 1;
            }

            if (! $module->isEnabled()) {
                $this->info("Module '{$name}' is already disabled.");
                return 0;
            }

            if ($this->moduleManager->disable($name)) {
                $this->info("Module '{$name}' has been disabled.");
                $this->comment(self::CACHE_CLEAR_MESSAGE);
                return 0;
            }

            $this->error("Failed to disable module '{$name}'.");
            return 1;
        } catch (Exception $e) {
            $this->error("Failed to disable module '{$name}': " . $e->getMessage());
            if ($this->option('verbose')) {
                $this->line($e->getTraceAsString());
            }
            return 1;
        }
    }

    protected function installModule(?string $name): int
    {
        if (! $name) {
            $this->error('Module name is required.');
            return 1;
        }

        try {
            $module = $this->moduleManager->find($name);
            if (! $module) {
                $this->error("Module '{$name}' not found.");
                return 1;
            }

            if ($module->isEnabled()) {
                $this->info("Module '{$name}' is already installed and enabled.");
                return 0;
            }

            $this->info("Installing module '{$name}'...");

            if ($this->moduleManager->install($name)) {
                $this->info("Module '{$name}' has been installed and enabled.");
                $this->comment(self::CACHE_CLEAR_MESSAGE);
                return 0;
            }

            $this->error("Failed to install module '{$name}'.");
            return 1;
        } catch (Exception $e) {
            $this->error("Failed to install module '{$name}': " . $e->getMessage());
            if ($this->option('verbose')) {
                $this->line($e->getTraceAsString());
            }
            return 1;
        }
    }

    protected function uninstallModule(?string $name): int
    {
        if (! $name) {
            $this->error('Module name is required.');
            return 1;
        }

        if (! $this->option('force')) {
            if (! $this->confirm("Are you sure you want to uninstall module '{$name}'? This action cannot be undone.")) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        try {
            if ($this->moduleManager->uninstall($name)) {
                $this->info("Module '{$name}' has been uninstalled.");
                return 0;
            }

            $this->error("Module '{$name}' not found.");
            return 1;
        } catch (Exception $e) {
            $this->error("Failed to uninstall module '{$name}': " . $e->getMessage());
            return 1;
        }
    }

    protected function createModule(?string $name): int
    {
        if (! $name) {
            $this->error('Module name is required.');
            return 1;
        }

        $modulePath = app_path("Modules/{$name}");

        if (File::exists($modulePath) && ! $this->option('force')) {
            $this->error("Module '{$name}' already exists. Use --force to overwrite.");
            return 1;
        }

        $this->createModuleStructure($name, $modulePath);
        $this->info("Module '{$name}' has been created successfully.");
        $this->comment(self::CACHE_CLEAR_MESSAGE);

        return 0;
    }

    protected function showModuleInfo(?string $name): int
    {
        if (! $name) {
            $this->error('Module name is required.');
            return 1;
        }

        $info = $this->moduleManager->getModuleInfo($name);

        if (empty($info)) {
            $this->error("Module '{$name}' not found.");
            return 1;
        }

        if ($this->option('format') === 'json') {
            $this->line(json_encode($info, JSON_PRETTY_PRINT));
            return 0;
        }

        $this->info('Module Information:');
        $this->line("Name: {$info['name']}");
        $this->line("Version: {$info['version']}");
        $this->line("Description: {$info['description']}");
        $this->line('Status: ' . ($info['enabled'] ? 'Enabled' : 'Disabled'));

        if (! empty($info['dependencies'])) {
            $this->line('Dependencies: ' . implode(', ', $info['dependencies']));
        }

        return 0;
    }

    protected function createModuleStructure(string $name, string $modulePath): void
    {
        $directories = [
            'Providers',
            'Http/Controllers',
            'Http/Middleware',
            'Models',
            'Services',
            'resources/views',
            'resources/lang',
            'resources/assets',
            'routes',
            'database/migrations',
            'database/seeders',
            'config',
            'tests',
        ];

        foreach ($directories as $directory) {
            File::makeDirectory("{$modulePath}/{$directory}", 0755, true);
        }

        File::put("{$modulePath}/module.json", json_encode([
            'name'         => $name,
            'version'      => '1.0.0',
            'description'  => "Custom {$name} module",
            'dependencies' => [],
            'config'       => [],
        ], JSON_PRETTY_PRINT));

        File::put("{$modulePath}/{$name}Module.php", $this->getModuleClassStub($name));
        File::put("{$modulePath}/Providers/{$name}ServiceProvider.php", $this->getServiceProviderStub($name));
        File::put("{$modulePath}/routes/web.php", "<?php\n\n// Web routes for {$name} module\n");
        File::put("{$modulePath}/routes/api.php", "<?php\n\n// API routes for {$name} module\n");
    }

    protected function getModuleClassStub(string $name): string
    {
        return "<?php

namespace App\\Modules\\{$name};

use App\\Modules\\BaseModule;

class {$name}Module extends BaseModule
{
    protected function onEnable(): void {}

    protected function onDisable(): void {}

    protected function onInstall(): void {}

    protected function onUninstall(): void {}
}
";
    }

    protected function getServiceProviderStub(string $name): string
    {
        return "<?php

namespace App\\Modules\\{$name}\\Providers;

use Illuminate\\Support\\ServiceProvider;

class {$name}ServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void {}
}
";
    }

    protected function showHelp(): int
    {
        $this->info('Available actions:');
        $this->line('  list [--format=json]     List all modules');
        $this->line('  enable <name>            Enable a module');
        $this->line('  disable <name>           Disable a module');
        $this->line('  install <name>           Install a module');
        $this->line('  uninstall <name>         Uninstall a module (use --force to skip prompt)');
        $this->line('  create <name>            Create a new module scaffold');
        $this->line('  info <name>              Show module information');
        $this->line('  health [name]            Check module dependency health');

        return 0;
    }
}
