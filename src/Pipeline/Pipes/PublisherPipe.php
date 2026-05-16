<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Pipeline\Pipes;

use Garanaw\LaravelConfigurer\Contracts\Pipe;
use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Library;
use Garanaw\LaravelConfigurer\Mechanisms\Publishers\PublisherManager;
use Illuminate\Contracts\Console\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Enumerable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;

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
        if ($passable->shouldPublish() === false) {
            return;
        }

        $libraries = $passable->allLibraries()->filter(
            static fn (Library $library) => $library->hasPublishCommands()
        );

        if ($passable->isVerbose()) {
            $this->display($libraries);
        }

        /** @var Library $library */
        foreach ($libraries as $library) {
            if (! $passable->options->shouldAutoConfirm()) {
                if (! confirm(sprintf('Do you want to publish the assets for %s?', $library->name))) {
                    continue;
                }
            }

            $driver = $this->publisher->driver($library);

            $driver->publish($library);

            $library->published();
        }
    }

    protected function display(Enumerable $libraries): void
    {
        info('The following libraries contain asserts to publish:');

        table(
            headers: ['Library', 'Publish Commands'],
            rows: $libraries->map(static fn (Library $library) => [
                $library->name,
                $library->publishCommands()->join(', '),
            ])->all(),
        );
    }
}