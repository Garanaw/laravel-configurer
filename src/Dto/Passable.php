<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Dto;

use Garanaw\LaravelConfigurer\Contracts\InstallCommand;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Fluent;

/**
 * @property-read Enumerable<Library> $libraries
 * @property-read Enumerable<Library> $devLibraries
 * @property-read Enumerable<InstallCommand> $commands
 * @property-read Options $options
 */
class Passable extends Fluent
{
    public function hasDevLibraries(): bool
    {
        return $this->devLibraries->isNotEmpty();
    }

    public function allLibraries(): Enumerable
    {
        return $this->libraries->merge($this->devLibraries);
    }

    public function hasCommands(): bool
    {
        return $this->commands->isNotEmpty();
    }

    public function shouldPublish(): bool
    {
        return ! $this->noPublish();
    }

    public function noPublish(): bool
    {
        return $this->options->noPublish;
    }

    public function shouldInstall(): bool
    {
        return ! $this->noInstall();
    }

    public function noInstall(): bool
    {
        return $this->options->noInstall;
    }

    public function shouldMigrate(): bool
    {
        return ! $this->noMigrate();
    }

    public function noMigrate(): bool
    {
        return $this->options->noMigrate;
    }

    public function noEnv(): bool
    {
        return $this->options->noEnv;
    }

    public function isVerbose(): bool
    {
        return $this->options->isVerbose();
    }
}