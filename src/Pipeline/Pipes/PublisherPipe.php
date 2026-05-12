<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Pipeline\Pipes;

use Garanaw\LaravelConfigurer\Contracts\Pipe;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Library;
use Garanaw\LaravelConfigurer\Mechanisms\Publishers\PublisherManager;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;

class PublisherPipe implements Pipe
{
    public function __construct(
        private readonly PublisherManager $publisher,
    ) {
    }

    public function handle(Passable $passable, \Closure $next): Passable
    {
        try {
            $this->execute($passable);
        } catch (\Throwable $e) {
            error(sprintf('Failed to publish assets: %s', $e->getMessage()));

            return $passable;
        }

        return $next($passable);
    }

    public function execute(Passable $passable): void
    {
        $libraries = $passable->libraries
            ->filter(static fn (Library $library) => $library->hasPublishCommands())
            ->merge($passable->devLibraries->filter(static fn (Library $library) => $library->hasPublishCommands()));

        /** @var Library $library */
        foreach ($libraries as $library) {
            if (! $passable->options->autoConfirm && ! confirm(sprintf('Do you want to publish the assets for %s?', $library->name))) {
                continue;
            }

            $driver = $this->publisher->driver($library);

            $driver->publish($library);

            $library->published();
        }
    }
}