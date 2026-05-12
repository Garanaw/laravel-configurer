<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Pipeline\Pipes;

use Garanaw\LaravelConfigurer\Contracts\Pipe;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Composer;

use function Laravel\Prompts\error;

class RequirerPipe implements Pipe
{
    public function __construct(
        protected readonly Composer $composer,
        protected readonly Dispatcher $events,
        protected readonly OutputStyle $output,
    ) {}

    public function handle(Passable $passable, \Closure $next): Passable
    {
        try {
            $this->execute($passable);
        } catch (\Throwable $e) {
            error(sprintf('Failed to require packages: %s', $e->getMessage()));

            return $passable;
        }

        return $next($passable);
    }

    protected function execute(Passable $passable): void
    {
        $commands = $passable->libraries->map(static fn (Library $library) => $library->command)->all();

        $this->composer->requirePackages(
            packages: $commands,
            output: $this->output,
        );

        $passable->libraries->each(static fn (Library $library) => $library->installed());
    }
}