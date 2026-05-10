<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms\Publishers;

use Garanaw\LaravelConfigurer\Contracts\PublisherContract;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Contracts\Console\Kernel;

class CommandPublisher implements PublisherContract
{
    public function __construct(
        private readonly Kernel $artisan,
    ) {}

    public function publish(Library $library): void
    {
        $this->artisan->call($library->publishCommands['command']);
    }
}
