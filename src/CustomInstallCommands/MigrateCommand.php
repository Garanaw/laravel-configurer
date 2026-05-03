<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\CanRun;
use Garanaw\LaravelConfigurer\Enum\When;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Contracts\Console\Kernel;

class MigrateCommand implements InstallCommand
{
    use CanRun;

    public function __construct(
        private readonly Kernel $kernel,
    ) {
    }

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
        return [
            SeedableMigrationsInstall::class,
        ];
    }

    public function install(Library $library): bool
    {
        return $this->kernel->call('migrate', ['--force', '--step']) === 0;
    }
}
