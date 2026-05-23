<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\CanRun;
use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\HasCustomId;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\FilesystemManager;

use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class SeedableMigrationsInstall extends InstallCommand
{
    use CanRun;
    use HasCustomId;

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

    public function command(): string
    {
        return static::class;
    }

    public function install(Passable $passable): bool
    {
        $path = resolve(Application::class)->databasePath('migrations');

        $migrations = glob($path . '/*.php');

        if (! $migrations) {
            info('No migration files found, skipping modification.');
        }

        foreach ($migrations as $migration) {
            $this->modifyMigrationFile($migration);
        }

        return true;
    }

    protected function modifyMigrationFile(string $file): void
    {
        $content = file_get_contents($file);

        if (! $content || ! str_contains($content, self::MIGRATION_SEARCH_PATTERN)) {
            // Not an extendable migration, pass
            warning(sprintf('Migration file %s is not extendable, skipping...', $file));

            return;
        }

        info(sprintf('Modifying migration file %s...', $file));

        $getTableMethod = <<<METHOD
    public function getTable(): string
    {
        return '';
    }
METHOD;

        $content
            |> (static fn ($x) => str_replace(self::MIGRATION_SEARCH_PATTERN, self::MIGRATION_REPLACE_PATTERN, $x))
            |> (static fn ($x) => str_replace(self::BLUEPRINT_SEARCH_PATTERN, self::BLUEPRINT_REPLACE_PATTERN, $x))
            |> (static fn ($x) => str_replace(self::SCHEMA_SEARCH_PATTERN, self::SCHEMA_REPLACE_PATTERN, $x))
            |> (static fn ($x) => str($x)->replaceLast('}', "\n" . $getTableMethod . "\n}")->toString())
            |> (static fn ($x) => file_put_contents($file, $x));
    }
}
