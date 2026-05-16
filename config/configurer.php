<?php

declare(strict_types=1);

use Garanaw\LaravelConfigurer\CustomInstallCommands\MigrateCommand;
use Garanaw\LaravelConfigurer\CustomInstallCommands\SeedableMigrationsInstall;
use Garanaw\LaravelConfigurer\CustomInstallCommands\SetEnvVarsCommand;

return [

    /**
     * |------------------------------------------------------------------------
     * | Libraries
     * |------------------------------------------------------------------------
     * | The libraries that will be installed.
     * | You can add any library that is available on Packagist.
     * | You can also add custom libraries by adding a new entry to the array and specifying the necessary information.
     * |--------------------------------------------------------------------------
     */
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
            'command' => 'laravel/prompts',
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
            'command' => 'spatie/laravel-medialibrary',
            'needsMigrating' => true,
            'publishCommands' => [
                'provider' => 'Spatie\MediaLibrary\MediaLibraryServiceProvider',
                'tags' => [
                    'medialibrary-migrations',
                    'medialibrary-config',
                ]
            ]
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
            'command' => 'pestphp/pest',
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

    /**
     * |------------------------------------------------------------------------
     * | Custom Pipes
     * |------------------------------------------------------------------------
     * | - Custom pipes that will be executed during the installation process.
     * | - These pipes will run at the end of the default pipelines, and will receive the same passable as the default pipes.
     * | - You can add any custom pipe that implements the Pipe interface.
     * | - The pipes will be executed in the order they are defined in the array.
     * | - You can use this feature to add any custom logic that you want to run during the installation process,
     * |    such as installing additional libraries, running custom commands, etc.
     * | - Make sure that the return is the same passable as it will be used later
     * | - The pipes will be resolved, so you can use DI. However you must specify here the class names
     * |--------------------------------------------------------------------------
     */
    'customPipes' => [],

    /**
     * |------------------------------------------------------------------------
     * | Custom Commands
     * |------------------------------------------------------------------------
     * | - Custom commands that will be executed after the installation of the libraries.
     * | - These commands will receive the result of the installation process, which is the same passable as the previous pipes.
     * | - You can add any custom command that implements the CustomCommand interface.
     * | - The commands will be executed in the order they are defined in the array.
     * | - You can use this feature to add any custom logic that you want to run after the installation process, such as running migrations, setting environment variables, etc.
     * | - Make sure that the commands are idempotent.
     * | - The commands will be run sequentially, and the next command will only run if the previous command was successful.
     * |------------------------------------------------------------------------
     */
    'customCommands' => [
        MigrateCommand::class,
        SetEnvVarsCommand::class,
    ],
];
