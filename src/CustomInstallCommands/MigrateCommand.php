<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\CanRun;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Illuminate\Contracts\Console\Kernel;

class MigrateCommand extends InstallCommand
{
    use CanRun;

    public function __construct(private readonly Kernel $kernel) {}

    public function id(): string
    {
        return 'internal:migrate';
    }

    public function command(): string
    {
        return static::class;
    }

    public function dependsOn(): array
    {
        return [
            SeedableMigrationsInstall::makeIdForDep(),
        ];
    }

    public function install(Passable $passable): bool
    {
        if ($this->didRun()) {
            return true;
        }

        if ($this->hasMissingDependencies($passable->allLibraries())) {
            return false;
        }

        return $this->kernel->call('migrate', ['--force', '--step']) === 0;
    }
}
