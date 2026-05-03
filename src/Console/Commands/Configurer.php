<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Console\Commands;

use Garanaw\LaravelConfigurer\CustomInstallCommands\InstallCommand;
use Garanaw\LaravelConfigurer\CustomInstallCommands\StringCommand;
use Garanaw\LaravelConfigurer\InstallerContract;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Config\Repository;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Composer;
use Illuminate\Support\Enumerable;

use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;

#[Signature('configurer:run')]
#[Description('Pre-configures the application with some useful libraries and settings')]
class Configurer extends Command
{
    public function handle(
        Composer $composer,
        Config $config,
        InstallerContract $installer,
    ): void {
        info('Running the Configurer...');
        $available = $this->collectInstallableLibraries($config, $composer);

        $selected = multiselect(
            label: 'Select the libraries to install',
            options: $available->pluck('name')->toArray(),
            scroll: 10,
            required: true,
        );

        $toInstall = $this->prepareLibraries($available, $selected, $composer);

        $result = $installer->run($toInstall);

        info(sprintf('%s libraries have been installed.', $result->count()));
    }

    protected function collectInstallableLibraries(Repository $config, Composer $composer): Enumerable
    {
        return collect($config->get('configurer.libraries'))
            ->filter(static fn (Library $library) => $composer->hasPackage($library->command) === false);
    }

    protected function prepareLibraries(Enumerable $available, array $selected, Composer $composer): Enumerable
    {
        $toInstall = [];

        foreach ($available as $library) {
            if (! in_array($library['name'], $selected, true)) {
                continue;
            }

            if ($composer->hasPackage($library['command'])) {
                info("The package {$library['name']} is already required, skipping...");

                continue;
            }

            $toInstall[] = new Library(
                name: $library['name'],
                command: $library['command'],
                installCommands: collect($library['installCommands'] ?? [])
                    ->map(function (string|InstallCommand $command): InstallCommand {
                        // Already a command, return the instance
                        if ($command instanceof InstallCommand) {
                            return $command;
                        }

                        // If it's a string, check if it's a class that exists and implements InstallCommand, if so resolve it from the container
                        if (class_exists($command) && is_a($command, InstallCommand::class, true)) {
                            return resolve($command);
                        }

                        // If it's a string, and it's a class that exists, but it does not implement the InstallCommand interface,
                        // we can only alert the user
                        if (class_exists($command)) {
                            throw new \RuntimeException("The command {$command} is not a valid install command. It must implement the InstallCommand interface.");
                        }

                        // Lastly, we assume that it is a string command that needs to be run in the artisan console,
                        // so we create a new StringCommand instance for it
                        return new StringCommand(artisan: resolve(Kernel::class), command: $command);
                    }),
                publishCommands: $library['publishCommands'] ?? null,
                needsMigrating: $library['needsMigrating'] ?? false,
                canBeDevOnly: $library['canBeDevOnly'] ?? false,
                envVars: $library['envVars'] ?? null,
            );
        }

        return collect($toInstall);
    }
}
