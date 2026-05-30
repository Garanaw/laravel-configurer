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
            'tags' => ['laravel', 'sail', 'docker'],
            'github' => 'https://github.com/laravel/sail',
        ],
        [
            'name' => 'Laravel Horizon',
            'command' => 'laravel/horizon',
            'installCommands' => ['horizon:install'],
            'needsMigrating' => true,
            'tags' => ['laravel', 'horizon', 'monitoring', 'queue'],
            'github' => 'https://github.com/laravel/horizon',
        ],
        [
            'name' => 'Laravel Telescope',
            'command' => 'laravel/telescope',
            'installCommands' => ['telescope:install'],
            'needsMigrating' => true,
            'canBeDevOnly' => true,
            'tags' => ['laravel', 'telescope', 'monitoring', 'debug'],
            'github' => 'https://github.com/laravel/telescope',
        ],
        [
            'name' => 'Laravel Pulse',
            'command' => 'laravel/pulse',
            'installCommands' => ['pulse:install'],
            'needsMigrating' => true,
            'publishCommands' => [
                'provider' => Laravel\Pulse\PulseServiceProvider::class,
                'tags' => [
                    'pulse-config',
                ],
            ],
            'tags' => ['laravel', 'pulse', 'monitoring', 'debug'],
            'github' => 'https://github.com/laravel/pulse',
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
            'tags' => ['laravel', 'reverb', 'push'],
            'github' => 'https://github.com/laravel/reverb',
        ],
        [
            'name' => 'Laravel Pennant',
            'command' => 'laravel/pennant',
            'publishCommands' => [
                'provider' => Laravel\Pennant\PennantServiceProvider::class,
            ],
            'needsMigrating' => true,
            'tags' => ['laravel', 'pennant', 'feature', 'flags'],
            'github' => 'https://github.com/laravel/pennant',
        ],
        [
            'name' => 'Laravel Pint',
            'command' => 'laravel/pint',
            'canBeDevOnly' => true,
            'tags' => ['laravel', 'pint', 'standards'],
            'github' => 'https://github.com/laravel/pint',
        ],
        [
            'name' => 'Laravel AI',
            'command' => 'laravel/ai',
            'needsMigrating' => true,
            'publishCommands' => [
                'provider' => \Laravel\Ai\AiServiceProvider::class,
            ],
            'tags' => ['laravel', 'ai'],
            'github' => 'https://github.com/laravel/ai',
        ],
        [
            'name' => 'Laravel Prompts',
            'command' => 'laravel/prompts',
            'tags' => ['laravel', 'prompt', 'ui', 'terminal'],
            'github' => 'https://github.com/laravel/prompts',
        ],
        // Migration libraries
        [
            'name' => 'Seedable Migrations',
            'command' => 'garanaw/seedable-migrations',
            'publishCommands' => [
                'provider' => Garanaw\SeedableMigrations\SeedableMigrationsServiceProvider::class,
            ],
            'installCommands' => [
                SeedableMigrationsInstall::class,
            ],
            'tags' => ['garanaw', 'laravel', 'seeding', 'migrations', 'db', 'database'],
            'github' => 'https://github.com/Garanaw/seedable-migrations',
        ],
        // Spatie libraries
        [
            'name' => 'Laravel Permissions',
            'command' => 'spatie/laravel-permission',
            'needsMigrating' => true,
            'tags' => ['spatie', 'permissions', 'security'],
            'github' => 'https://github.com/spatie/laravel-permission',
        ],
        [
            'name' => 'Spatie Media Library',
            'command' => 'spatie/laravel-medialibrary',
            'needsMigrating' => true,
            'publishCommands' => [
                'provider' => Spatie\MediaLibrary\MediaLibraryServiceProvider::class,
                'tags' => [
                    'medialibrary-migrations',
                    'medialibrary-config',
                ]
            ],
            'tags' => ['spatie', 'medialibrary'],
            'github' => 'https://github.com/spatie/laravel-medialibrary',
        ],
        [
            'name' => 'Spatie Ignition',
            'command' => 'spatie/ignition',
            'tags' => ['spatie', 'ignition', 'debug'],
            'github' => 'https://github.com/spatie/ignition',
        ],
        [
            'name' => 'Spatie Error Solutions',
            'command' => 'spatie/error-solutions',
            'tags' => ['spatie', 'error-solutions', 'debug'],
            'github' => 'https://github.com/spatie/error-solutions',
        ],
        [
            'name' => 'Spatie Laravel Tags',
            'command' => 'spatie/laravel-tags',
            'publishCommands' => [
                'provider' => Spatie\Tags\TagsServiceProvider::class,
                'tags' => [
                    'tags-migrations',
                    'tags-config',
                ],
            ],
            'needsMigrating' => true,
            'tags' => ['spatie', 'tags'],
            'github' => 'https://github.com/spatie/laravel-tags',
        ],
        [
            'name' => 'Spatie Laravel Model Flags',
            'command' => 'spatie/laravel-model-flags',
            'publishCommands' => [
                'provider' => Spatie\ModelFlags\ModelFlagsServiceProvider::class,
                'tags' => [
                    'model-flags-migrations',
                    'model-flags-config',
                ],
            ],
            'needsMigrating' => true,
            'tags' => ['spatie', 'model-flags'],
            'github' => 'https://github.com/spatie/laravel-model-flags',
        ],
        [
            'name' => 'Spatie Laravel Web Tinker',
            'command' => 'spatie/laravel-web-tinker',
            'canBeDevOnly' => true,
            'publishCommands' => [
                'provider' => Spatie\WebTinker\WebTinkerServiceProvider::class,
                'tags' => ['config'],
            ],
            'tags' => ['spatie', 'tinker', 'debug'],
            'github' => 'https://github.com/spatie/laravel-web-tinker',
        ],
        [
            'name' => 'Spatie Activity Log',
            'command' => 'spatie/laravel-activitylog',
            'needsMigrating' => true,
            'publishCommands' => [
                'provider' => Spatie\Activitylog\ActivitylogServiceProvider::class,
                'tags' => ['activitylog-migrations', 'activitylog-config'],
            ],
            'tags' => ['spatie', 'activitylog', 'monitoring'],
            'github' => 'https://github.com/spatie/laravel-activitylog',
        ],
        [
            'name' => 'Sun',
            'command' => 'spatie/sun',
            'tags' => ['spatie', 'support', 'sun', 'geo'],
            'github' => 'https://github.com/spatie/sun',
        ],
        [
            'name' => 'Spatie Opening Hours',
            'command' => 'spatie/opening-hours',
            'tags' => ['spatie', 'opening-hours', 'support', 'time'],
            'github' => 'https://github.com/spatie/opening-hours',
        ],
        [
            'name' => 'Spatie Color',
            'command' => 'spatie/color',
            'tags' => ['spatie', 'color', 'support', 'colors', 'ui'],
            'github' => 'https://github.com/spatie/color',
        ],
        // Filament
        [
            'name' => 'Filament',
            'command' => 'filament/filament',
            'installCommands' => ['filament:install --panels'],
            'publishCommands' => [
                'tags' => 'filament-config',
            ],
            'tags' => ['filament', 'panels', 'ui'],
            'github' => 'https://github.com/filamentphp/filament',
        ],
        // Relation libraries
        [
            'name' => 'Franzose Closure Table',
            'command' => 'franzose/closure-table',
            'tags' => ['franzose', 'closure-table', 'db', 'database', 'relations'],
            'github' => 'https://github.com/franzose/ClosureTable',
        ],
        [
            'name' => 'Kalnow Nestedset',
            'command' => 'kalnoy/nestedset',
            'tags' => ['kalnoy', 'nestedset', 'db', 'database', 'relations'],
            'github' => 'https://github.com/lazychaser/laravel-nestedset',
        ],
        // Connection libraries
        [
            'name' => 'SaloonPHP',
            'command' => 'saloonphp/saloon',
            'tags' => ['saloonphp', 'requests', 'api'],
            'github' => 'https://github.com/saloonphp/saloon',
        ],
        [
            'name' => 'Saloon Pagination',
            'command' => 'saloonphp/pagination-plugin',
            'tags' => ['saloonphp', 'pagination', 'plugin'],
            'github' => 'https://github.com/saloonphp/pagination-plugin',
        ],
        // Security libraries
        [
            'name' => 'Jenseggers Optimus Prime',
            'command' => 'jenssegers/optimus',
            'tags' => ['jenssegers', 'optimus', 'id', 'encoding', 'security'],
            'github' => 'https://github.com/jenssegers/optimus',
        ],
        [
            'name' => 'Laravel Impersonate',
            'command' => 'lab404/laravel-impersonate',
            'publishCommands' => [
                'provider' => Lab404\Impersonate\ImpersonateServiceProvider::class,
                'tags' => ['impersonate'],
            ],
            'tags' => ['laravel', 'impersonate'],
            'github' => 'https://github.com/404labfr/laravel-impersonate',
        ],
        // Debug libraries
        [
            'name' => 'Laravel Debugbar',
            'command' => 'fruitcake/laravel-debugbar',
            'canBeDevOnly' => true,
            'tags' => ['laravel', 'debugbar', 'debug', 'development'],
            'github' => 'https://github.com/fruitcake/laravel-debugbar',
        ],
        [
            'name' => 'Laravel Brain',
            'command' => 'laramint/laravel-brain',
            'canBeDevOnly' => true,
            'needsMigrating' => true,
            'publishCommands' => [
                'provider' => LaraMint\LaravelBrain\LaravelBrainServiceProvider::class,
                'tags' => [
                    'laravel-brain-migrations',
                    'laravel-brain-config',
                ],
            ],
            'envVars' => [
                'LARAVEL_BRAIN_AUTO_DISCOVER_ROUTES' => true,
                'LARAVEL_BRAIN_AUTO_DISCOVER_EXCLUDE_VENDOR' => false,
                'LARAVEL_BRAIN_DRIVER' => 'storage', //database
                'LARAVEL_BRAIN_DB_TABLE' => 'laravel_brain_graphs',
                'LARAVEL_BRAIN_DB_CONNECTION' => 'laravel-brain',
                'LARAVEL_BRAIN_DB_DRIVER' => 'mysql',
                'LARAVEL_BRAIN_DB_HOST' => '127.0.0.1',
                'LARAVEL_BRAIN_DB_PORT' => '3306',
                'LARAVEL_BRAIN_DB_DATABASE' => 'laravel_brain',
                'LARAVEL_BRAIN_DB_USERNAME' => 'brain',
                'LARAVEL_BRAIN_DB_PASSWORD' => 'secret',
            ],
            'tags' => ['laravel-brain', 'debug', 'development'],
            'github' => 'https://github.com/laramint/laravel-brain',
        ],
        [
            'name' => 'Laravel DevtoolBox',
            'command' => 'grazulex/laravel-devtoolbox',
            'canBeDevOnly' => true,
            'publishCommands' => [
                'provider' => Grazulex\LaravelDevtoolbox\LaravelDevtoolboxServiceProvider::class,
                'tags' => ['devtoolbox-config', 'devtoolbox-views'],
            ],
            'tags' => ['laravel-devtoolbox', 'debug', 'development'],
            'github' => 'https://github.com/Grazulex/laravel-devtoolbox',
        ],
        [
            'name' => 'Spatie Backtrace',
            'command' => 'spatie/backtrace',
            'tags' => ['spatie', 'backtrace', 'debug', 'development'],
            'github' => 'https://github.com/spatie/backtrace',
        ],
        // Export
        [
            'name' => 'Laravel Excel',
            'command' => 'maatwebsite/excel',
            'publishCommands' => [
                'provider' => Maatwebsite\Excel\ExcelServiceProvider::class,
                'tags' => ['config'],
            ],
            'tags' => ['laravel', 'excel', 'export', 'files'],
            'github' => 'https://github.com/SpartnerNL/Laravel-Excel',
        ],
        // NativePHP
        [
            'name' => 'NativePHP Mobile',
            'command' => 'nativephp/mobile',
            'envVars' => [
                'NATIVEPHP_APP_ID' => 'com.yourcompany.yourapp',
            ],
            'installCommands' => ['native:install'],
            'tags' => ['native', 'mobile'],
            'github' => 'https://nativephp.com/docs/mobile/3/getting-started/installation',
        ],
        [
            'name' => 'NativePHP Desktop',
            'command' => 'nativephp/desktop',
            'installCommands' => ['native:install'],
            'tags' => ['native', 'desktop'],
            'github' => 'https://github.com/NativePHP/desktop',
        ],
        // Testing libraries
        [
            'name' => 'Pest',
            'command' => 'pestphp/pest',
            'canBeDevOnly' => true,
            'withAllDependencies' => true,
            'installCommands' => ['./vendor/pest/pest --init'],
            'tags' => ['pest', 'tests'],
            'github' => 'https://github.com/pestphp/pest',
        ],
        // Helper libraries
        [
            'name' => 'Laravel Numeral',
            'command' => 'garanaw/laravel-numeral',
            'tags' => ['garanaw', 'laravel', 'numeral', 'support', 'math'],
            'github' => 'https://github.com/Garanaw/laravel-numeral',
        ],
        [
            'name' => 'Units of Measure',
            'command' => 'php-units-of-measure/php-units-of-measure',
            'tags' => ['php-units-of-measure', 'units', 'support'],
            'github' => 'https://github.com/PhpUnitsOfMeasure/phpunit-of-measure',
        ],
        [
            'name' => 'StrSim',
            'command' => 'edgaras/strsim',
            'tags' => ['edgaras', 'str-sim', 'string', 'support', 'similarity', 'distance'],
            'github' => 'https://github.com/Edgaras0x4E/StrSim',
        ],
        [
            'name' => 'Laravel Idempotency',
            'command' => 'algoyounes/idempotency',
            'publishCommands' => [
                'provider' => AlgoYounes\Idempotency\Providers\IdempotencyServiceProvider::class,
                'tags' => ['config'],
            ],
            'tags' => ['laravel', 'idempotency', 'middleware', 'resilience', 'duplicate', 'requests', 'support'],
            'github' => 'https://github.com/algoyounes/laravel-idempotency',
        ],
        [
            'name' => 'Idempotency for Laravel',
            'command' => 'infinitypaul/idempotency-laravel',
            'publishCommands' => [
                'provider' => Infinitypaul\Idempotency\IdempotencyServiceProvider::class,
            ],
            'tags' => ['laravel', 'idempotency', 'middleware', 'resilience', 'duplicate', 'requests', 'support'],
            'github' => 'https://github.com/infinitypaul/idempotency-laravel',
        ],
        [
            'name' => 'Laravel Manager',
            'command' => 'graham-campbell/manager',
            'tags' => ['laravel-manager', 'laravel', 'manager', 'support'],
            'github' => 'https://github.com/GrahamCampbell/Laravel-Manager',
        ],
        [
            'name' => 'Laravel Markdown',
            'command' => 'graham-campbell/markdown',
            'publishCommands' => [
                'provider' => GrahamCampbell\Markdown\MarkdownServiceProvider::class,
            ],
            'tags' => ['laravel-markdown', 'laravel', 'markdown', 'support'],
            'github' => 'https://github.com/GrahamCampbell/Laravel-Markdown',
        ],
        // Hardware
        [
            'name' => 'Pinout',
            'command' => 'danjohnson95/pinout',
            'tags' => ['pinout', 'gpio', 'hardware', 'raspberry-pi'],
            'github' => 'https://github.com/danjohnson95/pinout',
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
