<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Pipeline\Pipes;

use Garanaw\LaravelConfigurer\Contracts\InstallCommand;
use Garanaw\LaravelConfigurer\Contracts\Pipe;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Library;
use Garanaw\LaravelConfigurer\Mechanisms\KhanSort;
use Illuminate\Support\Enumerable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

class InstallerPipe implements Pipe
{
    public function __construct(
        private readonly KhanSort $sort,
    ) {
    }

    public function handle(Passable $passable, \Closure $next): Passable
    {
        info('Running installer pipe...');

        try {
            $this->execute($passable);
        } catch (\Throwable $e) {
            error(sprintf('Failed to run install commands: %s', $e->getMessage()));

            return $passable;
        }

        return $next($passable);
    }

    protected function execute(Passable $passable): void
    {
        if ($passable->shouldInstall() === false) {
            warning('Skipping installation.');

            return;
        }

        $libraries = $passable->allLibraries();
        $commands = $this->getCommands($libraries, $passable);

        $this->display($commands);

        foreach ($commands as $command) {
            if (! $passable->options->shouldAutoConfirm()) {
                if (! confirm(sprintf('Do you want to run %s now?', $command->command()))) {
                    continue;
                }
            }

            try {
                $installed = $command->install($passable);
            } catch (\Throwable $e) {
                error(sprintf('Failed to run %s: %s', $command->command(), $e->getMessage()));

                continue;
            }

            $command->setRan($installed);
        }
    }

    /**
     * @param Enumerable<Library> $libraries
     * @return Enumerable<InstallCommand>
     */
    protected function getCommands(Enumerable $libraries, Passable $passable): Enumerable
    {
        info('Building installation commands...');

        $commands = [];

        foreach ($libraries as $library) {
            if (! $library->hasInstallCommands()) {
                continue;
            }

            foreach ($library->installCommands as $command) {
                $commands[] = $command;
            }
        }

        foreach (config('configurer.customCommands', []) as $command) {
            $commands[] = resolve($command);
        }

        $commands = collect($commands);

        info(sprintf('%s commands will be installed', $commands->count()));

        return $this->sort->sort($commands);
    }

    protected function display(Enumerable $commands): void
    {
        info('The following commands will be run:');

        table(
            headers: ['Command', 'Dependencies'],
            rows: $commands->map(static fn (InstallCommand $command) => [
                $command->command(),
                implode(', ', $command->dependsOn() ?? []),
            ])->all(),
        );
    }
}