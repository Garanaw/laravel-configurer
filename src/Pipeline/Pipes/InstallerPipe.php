<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Pipeline\Pipes;

use Garanaw\LaravelConfigurer\Contracts\InstallCommand;
use Garanaw\LaravelConfigurer\Contracts\Pipe;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Enum\When;
use Garanaw\LaravelConfigurer\Library;
use function Laravel\Prompts\confirm;

class InstallerPipe implements Pipe
{
    public function __construct(
        private readonly When $when,
    ) {
    }

    public function handle(Passable $passable, \Closure $next): Passable
    {
        try {
            $this->execute($passable);
        } catch (\Throwable $e) {
            return $passable;
        }

        return $next($passable);
    }

    protected function execute(Passable $passable): void
    {
        $libraries = $passable->libraries->merge($passable->devLibraries);

        /** @var Library $library */
        foreach ($libraries as $library) {
            if (! $library->hasInstallCommands() || $library->isInstalled()) {
                continue;
            }

            $commands = $library->installCommands->filter(
                fn (InstallCommand $command) => $command->when()->is($this->when)
            );

            if ($commands->isEmpty()) {
                continue;
            }

            foreach ($commands as $command) {
                if (! $passable->options->autoConfirm || ! confirm(sprintf('Do you want to run %s for %s now?', $command->command(), $library->name))) {
                    continue;
                }

                $command->install($library);
            }

            $library->installed();
        }
    }
}