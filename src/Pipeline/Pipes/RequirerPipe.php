<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Pipeline\Pipes;

use Garanaw\LaravelConfigurer\Contracts\Pipe;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Composer;
use Illuminate\Support\Enumerable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

class RequirerPipe implements Pipe
{
    public function __construct(
        protected readonly Composer $composer,
        protected readonly Dispatcher $events,
        protected readonly OutputStyle $output,
    ) {}

    public function handle(Passable $passable, \Closure $next): Passable
    {
        info('Running requirer pipe...');

        try {
            $installed = $this->execute($passable);

            if (! $installed) {
                return $passable;
            }
        } catch (\Throwable $e) {
            error(sprintf('Failed to require packages: %s', $e->getMessage()));

            return $passable;
        }

        return $next($passable);
    }

    protected function execute(Passable $passable): bool
    {
        $commands = $passable->libraries->map(static fn (Library $library) => $library->command)->all();

        if ($passable->isVerbose()) {
            $this->display($passable->libraries);
        }

        if (! $passable->options->shouldAutoConfirm()) {
            if (! confirm('Do you want to require these packages now?')) {
                return false;
            }
        }

        $this->composer->requirePackages(
            packages: $commands,
            output: $this->output,
        );

        $passable->libraries->each(static fn (Library $library) => $library->required());

        $this->composer->dumpAutoloads();

        return true;
    }

    protected function display(Enumerable $libraries): void
    {
        info('The following libraries will be installed');

        table(
            headers: ['Library', 'Command'],
            rows: $libraries->map(static fn (Library $library) => [
                $library->name,
                $library->command,
            ])->all(),
        );
    }
}