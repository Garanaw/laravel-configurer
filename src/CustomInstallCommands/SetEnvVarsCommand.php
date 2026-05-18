<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands;

use Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns\CanRun;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Library;

class SetEnvVarsCommand extends InstallCommand
{
    use CanRun;

    /**
     * @var int The smaller the number, the sooner it will be executed
     */
    protected int $weight = -100;

    public function id(): string
    {
        return 'internal:set-env-vars';
    }

    public function install(Passable $passable): bool
    {
        $envVars = $passable->allLibraries()
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

    public function command(): string
    {
        return static::class;
    }
}