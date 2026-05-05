<?php

declare(strict_types=1);

use Garanaw\LaravelConfigurer\CustomInstallCommands\MigrateCommand;
use Garanaw\LaravelConfigurer\CustomInstallCommands\SeedableMigrationsInstall;
use Garanaw\LaravelConfigurer\CustomInstallCommands\SetEnvVarsCommand;

return [
    'libraries' => [
        // Laravel libraries
        [
            'name' => 'Laravel Sail',
            'command' => 'laravel/sail',
            'installCommands' => ['sail:install'],
            'publishCommands' => [
                'command' => 'sail:publish',
            ],
            'canBeDevOnly' => true,
        ],
        [
            'name' => 'Laravel Horizon',
            'command' => 'laravel/horizon',
            'installCommands' => ['horizon:install'],
            'needsMigrating' => true,
        ],
        [
            'name' => 'Laravel Telescope',
            'command' => 'laravel/telescope',
            'installCommands' => ['telescope:install'],
            'needsMigrating' => true,
            'canBeDevOnly' => true,
        ],
        [
            'name' => 'Laravel Pulse',
            'command' => 'laravel/pulse',
            'installCommands' => ['pulse:install'],
            'needsMigrating' => true,
            'publishCommands' => [
                'provider' => 'Laravel\Pulse\PulseServiceProvider',
                'tags' => [
                    'pulse-config',
                ],
            ],
        ],
        [
            'name' => 'Laravel Reverb',
            'command' => 'laravel/reverb',
            'installCommands' => [
                'install:broadcasting',
                'reverb:install',
            ],
            'envVars' => [
                'REVERB_APP_ID' => 'my-app-id',
                'REVERB_APP_KEY' => 'my-app-key',
                'REVERB_APP_SECRET' => 'my-app-secret',
                'REVERB_SERVER_HOST' => '0.0.0.0',
                'REVERB_SERVER_PORT' => '8080',
                'REVERB_HOST' => 'ws.laravel.com',
                'REVERB_PORT' => '443',
            ],
            'needsMigrating' => true,
        ],
        [
            'name' => 'Laravel Pennant',
            'command' => 'laravel/pennant',
            'publishCommands' => [
                'provider' => 'Laravel\Pennant\PennantServiceProvider',
            ],
            'needsMigrating' => true,
        ],
        [
            'name' => 'Laravel Pint',
            'command' => 'laravel/pint',
            'canBeDevOnly' => true,
        ],
        [
            'name' => 'Laravel AI',
            'command' => 'laravel/ai',
            'needsMigrating' => true,
            'publishCommands' => [
                'provider' => 'Laravel\Pint\PintServiceProvider',
            ],
        ],
        [
            'name' => 'Laravel Prompts',
            'command' => 'laravel/prompt',
        ],
        // Migration libraries
        [
            'name' => 'Seedable Migrations',
            'command' => 'garanaw/seedable-migrations',
            'publishCommands' => [
                'provider' => 'Garanaw\SeedableMigrations\SeedableMigrationsServiceProvider',
            ],
            'installCommands' => [
                SeedableMigrationsInstall::class,
            ],
        ],
        // Spatie libraries
        [
            'name' => 'Laravel Permissions',
            'command' => 'spatie/laravel-permission',
            'needsMigrating' => true,
        ],
        [
            'name' => 'Spatie Media Library',
            'command' => 'spatie/media-library',
            'needsMigrating' => true,
        ],
        [
            'name' => 'Spatie Laravel Tags',
            'command' => 'spatie/laravel-tags',
            'publishCommands' => [
                'provider' => 'Spatie\Tags\TagsServiceProvider',
                'tags' => [
                    'tags-migrations',
                    'tags-config',
                ],
            ],
            'needsMigrating' => true,
        ],
        [
            'name' => 'Spatie Laravel Web Tinker',
            'command' => 'spatie/laravel-web-tinker',
            'canBeDevOnly' => true,
            'publishCommands' => [
                'provider' => 'Spatie\WebTinker\WebTinkerServiceProvider',
                'tags' => ['config'],
            ],
        ],
        // Filament
        [
            'name' => 'Filament',
            'command' => 'filament/filament',
            'installCommands' => ['filament:install --panels'],
            'publishCommands' => [
                'tags' => 'filament-config',
            ],
        ],
        // Relation libraries
        [
            'name' => 'Franzose Closure Table',
            'command' => 'franzose/closure-table',
        ],
        [
            'name' => 'Kalnow Nestedset',
            'command' => 'kalnoy/nestedset',
        ],
        // Connection libraries
        [
            'name' => 'SaloonPHP',
            'command' => 'saloonphp/saloon',
        ],
        [
            'name' => 'Saloon Pagination',
            'command' => 'saloonphp/pagination-plugin',
        ],
        // Security libraries
        [
            'name' => 'Jenseggers Optimus Prime',
            'command' => 'jenssegers/optimus',
        ],
        // Debug libraries
        [
            'name' => 'Laravel Debugbar',
            'command' => 'fruitcake/laravel-debugbar',
            'canBeDevOnly' => true,
        ],
        // Testing libraries
        [
            'name' => 'Pest',
            'command' => 'pest/pest',
            'canBeDevOnly' => true,
            'withAllDependencies' => true,
            'installCommands' => ['./vendor/pest/pest --init'],
        ],
        // Helper libraries
        [
            'name' => 'Laravel Numeral',
            'command' => 'garanaw/laravel-numeral',
        ],
        [
            'name' => 'Units of Measure',
            'command' => 'php-units-of-measure/php-units-of-measure',
        ],
    ],

    // Custom Commands
    'customCommands' => [
        MigrateCommand::class,
        SetEnvVarsCommand::class,
    ],
];
