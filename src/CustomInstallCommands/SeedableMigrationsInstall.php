<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\CanRun;
use Garanaw\LaravelConfigurer\Enum\When;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;

class SeedableMigrationsInstall implements InstallCommand
{
    use CanRun;

    private const string MIGRATION_SEARCH_PATTERN = 'Illuminate\Database\Migrations\Migration';

    private const string MIGRATION_REPLACE_PATTERN = 'Garanaw\SeedableMigrations\Migration';

    private const string BLUEPRINT_SEARCH_PATTERN = 'Illuminate\Database\Migrations\Blueprint';

    private const string BLUEPRINT_REPLACE_PATTERN = 'Garanaw\SeedableMigrations\Blueprint';

    private const string SCHEMA_SEARCH_PATTERN = 'Schema::';

    private const string SCHEMA_REPLACE_PATTERN = '$this->schema->';

    public function __construct(
        private readonly Application $app,
        private readonly FilesystemManager $filesystem,
    ) {}

    public function when(): When
    {
        return When::END_ALL;
    }

    public function command(): string
    {
        return static::class;
    }

    public function dependsOn(): ?array
    {
        return null;
    }

    public function install(Library $library): bool
    {
        $path = resolve(Application::class)->databasePath('migrations');

        $migrations = glob($path . '/*.php');

        foreach ($migrations as $migration) {
            file_get_contents($migration)
                |> (static fn ($x) => str_replace(self::BLUEPRINT_SEARCH_PATTERN, self::BLUEPRINT_REPLACE_PATTERN, $x))
                |> (static fn ($x) => str_replace(self::MIGRATION_SEARCH_PATTERN, self::MIGRATION_REPLACE_PATTERN, $x))
                |> (static fn ($x) => str_replace(self::SCHEMA_SEARCH_PATTERN, self::SCHEMA_REPLACE_PATTERN, $x))
                |> (static fn ($x) => file_put_contents($migration, $x));
        }

        return true;
    }
}
