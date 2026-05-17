<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Contracts;

use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Enum\When;

interface InstallCommand
{
    public function id(): string;

    public function setRan(bool $ran = true): static;

    public function didRun(): bool;

    public function when(): When;

    public function command(): string;

    public function dependsOn(): array;

    public function install(Passable $passable): bool;
}
