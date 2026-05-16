<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms\Publishers;

use Garanaw\LaravelConfigurer\Contracts\PublisherContract;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Console\Kernel;

class ProviderPublisher implements PublisherContract
{
    public function __construct(
        private readonly Kernel $artisan,
        private readonly OutputStyle $output,
    ) {}

    public function publish(Library $library): void
    {
        $params = ['--provider' => $library->publishCommands['provider']];

        $this->artisan->call(
            command: 'vendor:publish',
//            parameters: $params,
            outputBuffer: $this->output,
        );
    }
}
