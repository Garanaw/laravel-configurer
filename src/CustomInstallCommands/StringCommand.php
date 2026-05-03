<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\CanRun;
use Garanaw\LaravelConfigurer\Enum\When;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Contracts\Console\Kernel;

class StringCommand implements InstallCommand
{
    use CanRun;

    public function __construct(
        private readonly Kernel $artisan,
        private readonly string $command,
    ) {}

    public function when(): When
    {
        return When::END;
    }

    public function command(): string
    {
        return $this->command;
    }

    public function dependsOn(): ?array
    {
        return null;
    }

    public function install(Library $library): bool
    {
        $this->artisan->call($this->command);

        return true;
    }
}
