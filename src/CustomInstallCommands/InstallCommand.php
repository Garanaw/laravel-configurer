<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\Contracts\InstallCommand as InstallCommandContract;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Support\Enumerable;

abstract class InstallCommand implements InstallCommandContract
{
    public function dependsOn(): array
    {
        return [];
    }

    protected function hasMissingDependencies(Enumerable $libraries): bool
    {
        $dependencies = $this->dependsOn();

        if (empty($dependencies)) {
            return false;
        }

        $allCommands = $libraries->flatMap(static fn (Library $library) => $library->installCommands)->all();

        foreach ($dependencies as $dependency) {
            if (array_any($allCommands, static fn ($command) => $command::class === $dependency)) {
                return true;
            }
        }

        return false;
    }
}