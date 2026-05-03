<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\Enum\When;
use Garanaw\LaravelConfigurer\Library;

interface InstallCommand
{
    public function setRan(bool $ran = true): static;

    public function didRun(): bool;

    public function when(): When;

    public function command(): string;

    public function dependsOn(): ?array;
}
