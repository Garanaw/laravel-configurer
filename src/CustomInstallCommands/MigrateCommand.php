<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\CanRun;
use Garanaw\LaravelConfigurer\Enum\When;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Enumerable;

class MigrateCommand implements CustomCommand
{
    use CanRun;

    public function __construct(private readonly Kernel $kernel) {}

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

    public function install(Enumerable $libraries): bool
    {
        if ($this->didRun()) {
            return true;
        }

        if ($this->dependenciesMissing($libraries)) {
            return false;
        }

        return $this->kernel->call('migrate', ['--force', '--step']) === 0;
    }

    private function dependenciesMissing(Enumerable $libraries): bool
    {
        $dependencies = $this->dependsOn();

        if (empty($dependencies)) {
            return false;
        }

        $allCommands = $libraries->flatMap(static fn (Library $library) => $library->installCommands)->all();

        foreach ($dependencies as $dependency) {
            if (array_any($allCommands, static fn($command) => $command::class === $dependency)) {
                return true;
            }
        }

        return false;
    }
}
