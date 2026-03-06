<?php

namespace App\Modules\BlogModule;

use App\Modules\BaseModule;

class BlogModuleModule extends BaseModule
{
    protected string $name = 'BlogModule';
    protected string $version = '1.0.0';
    protected string $description = 'A blog module for the browser game';
    protected array $dependencies = [];
    protected array $config = [];

    protected function loadModuleInfo(): void
    {
        // Override to use class properties instead of module.json
    }
}
