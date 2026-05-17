<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\Contracts\InstallCommand;
use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\CanRun;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Enum\When;
use Illuminate\Contracts\Console\Kernel;

class StringCommand implements InstallCommand
{
    use CanRun;

    public function __construct(
        private readonly Kernel $artisan,
        private readonly string $command,
    ) {}

    public function id(): string
    {
        return sprintf('lib:%s', $this->command);
    }

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

    public function install(Passable $passable): bool
    {
        $this->artisan->call($this->command);

        return true;
    }
}
