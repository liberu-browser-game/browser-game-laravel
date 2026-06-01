<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Modules Path
    |--------------------------------------------------------------------------
    |
    | This value determines the path where modules are stored. By default,
    | modules are stored in the app/Modules directory.
    |
    */

    'path' => app_path('Modules'),

    /*
    |--------------------------------------------------------------------------
    | Auto Discovery
    |--------------------------------------------------------------------------
    |
    | When enabled, the module system will automatically discover and register
    | modules found in the modules directory.
    |
    */

    'auto_discovery' => true,

    /*
    |--------------------------------------------------------------------------
    | Cache Modules
    |--------------------------------------------------------------------------
    |
    | When enabled, module information will be cached to improve performance.
    | This is recommended for production environments.
    |
    */

    'cache' => env('MODULES_CACHE', true),

    /*
    |--------------------------------------------------------------------------
    | Cache Key
    |--------------------------------------------------------------------------
    |
    | The cache key used to store module information.
    |
    */

    'cache_key' => 'app.modules',

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    |
    | The time-to-live for cached module information in seconds.
    |
    */

    'cache_ttl' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Module Namespace
    |--------------------------------------------------------------------------
    |
    | The base namespace for modules.
    |
    */

    'namespace' => 'App\\Modules',

    /*
    |--------------------------------------------------------------------------
    | External Modules Path
    |--------------------------------------------------------------------------
    |
    | Path for Composer-based external modules (PSR-4 auto-discovered).
    | These are loaded from the app-modules/ directory at the project root.
    |
    */

    'external_path' => base_path('app-modules'),

    /*
    |--------------------------------------------------------------------------
    | Load Composer Modules
    |--------------------------------------------------------------------------
    |
    | When enabled, the module system will also discover modules installed
    | via Composer in the app-modules/ directory.
    |
    */

    'load_composer_modules' => env('MODULES_LOAD_COMPOSER', false),

    /*
    |--------------------------------------------------------------------------
    | Filament Auto-Discovery
    |--------------------------------------------------------------------------
    |
    | When enabled, Filament resources, pages, and widgets inside modules
    | are auto-discovered and registered with the admin panel.
    |
    */

    'filament_discovery' => env('MODULES_FILAMENT_DISCOVERY', true),

    /*
    |--------------------------------------------------------------------------
    | Enabled Modules
    |--------------------------------------------------------------------------
    |
    | List of modules that should be enabled by default. This is useful
    | for ensuring critical modules are always available.
    |
    */

    'enabled' => [
        // 'ExampleModule',
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Assets
    |--------------------------------------------------------------------------
    |
    | Configuration for module assets publishing.
    |
    */

    'assets' => [
        'path' => public_path('modules'),
        'url' => '/modules',
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Views
    |--------------------------------------------------------------------------
    |
    | Configuration for module views.
    |
    */

    'views' => [
        'namespace_prefix' => 'module',
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Translations
    |--------------------------------------------------------------------------
    |
    | Configuration for module translations.
    |
    */

    'translations' => [
        'namespace_prefix' => 'module',
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, additional debugging information will be available
    | and modules will be reloaded on each request.
    |
    */

    'development' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Theme Configuration
    |--------------------------------------------------------------------------
    |
    | Default theme and persistence settings for per-user theming.
    |
    */

    'theme' => [
        'default' => env('THEME_DEFAULT', 'light'),
        'persist' => env('THEME_PERSIST', 'session'), // 'session' or 'database'
        'options' => ['light', 'dark', 'system'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Requirements
    |--------------------------------------------------------------------------
    |
    | Global requirements that all modules must meet.
    |
    */

    'requirements' => [
        'php' => '8.5',
        'laravel' => '13.0',
    ],

];