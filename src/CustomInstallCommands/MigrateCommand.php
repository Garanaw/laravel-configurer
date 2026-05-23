<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\CanRun;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Illuminate\Console\Concerns\InteractsWithIO;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Process;
use function Illuminate\Support\artisan_binary;
use function Illuminate\Support\php_binary;

class MigrateCommand extends InstallCommand
{
    use CanRun;
    use InteractsWithIO;

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

        return Process::run([php_binary(), artisan_binary(), 'migrate', '--step', '--force'])->successful();
    }
}
