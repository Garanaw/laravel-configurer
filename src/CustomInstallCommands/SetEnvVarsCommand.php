<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\Contracts\CustomCommand;
use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\CanRun;
use Garanaw\LaravelConfigurer\Enum\When;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Support\Enumerable;

class SetEnvVarsCommand implements CustomCommand
{
    use CanRun;

    public function install(Enumerable $libraries): bool
    {
        $envVars = $libraries
            ->filter(static fn (Library $library) => $library->hasEnvVars())
            ->map(static fn (Library $library) => $library->envVars);

        $envPath = base_path('.env');
        $envFile = file_get_contents($envPath);

        if (str($envFile)->endsWith("\n") === false) {
            $envFile .= "\n";
        }

        foreach ($envVars as $block) {
            foreach ($block as $key => $value) {
                if (str_contains($envFile, "$key=")) {
                    continue;
                }

                $envFile .= "\n$key=$value";
            }

            $envFile .= "\n";
        }

        file_put_contents($envPath, $envFile);

        return true;
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
        return null;
    }
}