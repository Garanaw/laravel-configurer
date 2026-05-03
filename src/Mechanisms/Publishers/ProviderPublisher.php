<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms\Publishers;

use;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Contracts\Console\Kernel;

class ProviderPublisher implements PublisherContract
{
    public function __construct(
        private readonly Kernel $artisan,
    ) {
    }

    public function publish(Library $library): void
    {
        $command = $this->buildCommand($library);

        $this->artisan->call($command);
    }

    protected function buildCommand(Library $library): string
    {
        /** @var class-string $provider */
        $provider = $library->publishCommands['provider'];

        return "vendor:publish --provider=\"{$provider}\"";
    }
}