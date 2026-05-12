<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Console\Commands;

use Garanaw\LaravelConfigurer\Contracts\InstallCommand;
use Garanaw\LaravelConfigurer\Contracts\InstallerContract;
use Garanaw\LaravelConfigurer\CustomInstallCommands\StringCommand;
use Garanaw\LaravelConfigurer\Dto\Options;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Library;
use Garanaw\LaravelConfigurer\Pipeline\ComposerPipeline;
use Illuminate\Config\Repository;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Composer;
use Illuminate\Support\Enumerable;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;

#[AsCommand('configurer:run')]
#[Description('Pre-configures the application with some useful libraries and settings')]
class Configurer extends Command
{
    protected $signature = 'configurer:run
                            {--all : Installs all libraries without selecting individually}
                            {--auto-confirm : Run all the installations selected without confirmation}
                            {--dev : Installs all the dev libraries under the "dev" section in composer}
                            {--no-publish : Skip the publish commands for the libraries}
                            {--no-install : Skip the install commands for the libraries}
                            {--no-migrate : Skip running any migrations for the libraries}
                            {--no-env : Skip setting up any environment variables for the libraries}
                            {--no-events : Skip dispatching events for the libraries}';

    public function handle(
        Composer $composer,
        Config $config,
        InstallerContract $installer,
        ComposerPipeline $pipeline,
    ): void {
        info('Running the Configurer...');
        $available = $this->collectInstallableLibraries($config, $composer);
        $options = $this->makeOptions();

        $availableSelection = $available->pluck('name')->toArray();

        $selected = $options->all ? $availableSelection : multiselect(
            label: 'Select the libraries to install',
            options: $availableSelection,
            scroll: 10,
            required: true,
        );

        $toInstall = $this->prepareLibraries($available, $selected, $composer);

        $passable = $this->makePassable($toInstall, $options);

        $result = $pipeline->pass($passable);

//        $result = $installer->run($toInstall, $options);

//        info(sprintf('%s libraries have been installed.', $result->count()));

        // Now run the custom commands
        $allCommands = $config->get('configurer.commands', []);

        foreach ($allCommands as $command) {
            $command = $this->mapCommand($command);

            if (! confirm(sprintf('Do you want to run the command %s now?', $command->command()))) {
                continue;
            }

            try {
                $command->install($result);
            } catch (\Throwable $e) {
                info(sprintf('Failed to run command %s: %s', $command->command(), $e->getMessage()));
            }
        }
    }

    protected function collectInstallableLibraries(Repository $config, Composer $composer): Enumerable
    {
        return collect($config->get('configurer.libraries'))
            ->filter(static fn (array $library) => $composer->hasPackage($library['command']) === false);
    }

    protected function makePassable(Enumerable $libraries, Options $options): Passable
    {
        /**
         * @var Enumerable<Library> $dev
         * @var Enumerable<Library> $noDev
         */
        [$dev, $noDev] = $libraries->partition(
            static fn (Library $library) => ($options->devOnly && $library->canBeDevOnly)
                || confirm(sprintf('Do you want to install %s as a dev dependency?', $library->name))
        );

        return new Passable([
            'libraries' => $noDev,
            'devLibraries' => $dev,
            'options' => $options,
        ]);
    }

    protected function makeOptions(): Options
    {
        return new Options([
            'all' => $this->option('all') ?? false,
            'autoConfirm' => $this->option('auto-confirm') ?? false,
            'devOnly' => $this->option('dev') ?? false,
            'noPublish' => $this->option('no-publish') ?? false,
            'noInstall' => $this->option('no-install') ?? false,
            'noMigrate' => $this->option('no-migrate') ?? false,
            'noEvents' => $this->option('no-events') ?? false,
            'noEnv' => $this->option('no-env') ?? false,
        ]);
    }

    protected function prepareLibraries(Enumerable $available, array $selected, Composer $composer): Enumerable
    {
        $toInstall = [];
        $commander = $this->mapCommand(...);

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
                installCommands: collect($library['installCommands'] ?? [])->map($commander),
                publishCommands: $library['publishCommands'] ?? null,
                needsMigrating: $library['needsMigrating'] ?? false,
                canBeDevOnly: $library['canBeDevOnly'] ?? false,
                envVars: $library['envVars'] ?? null,
            );
        }

        return collect($toInstall);
    }

    protected function mapCommand(string|InstallCommand $command): InstallCommand
    {
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
    }
}
