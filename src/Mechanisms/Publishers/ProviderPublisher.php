<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms\Publishers;

use Garanaw\LaravelConfigurer\Contracts\PublisherContract;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Support\Facades\Process;

use function Illuminate\Support\artisan_binary;
use function Illuminate\Support\php_binary;

class ProviderPublisher implements PublisherContract
{
    public function publish(Library $library): void
    {
        $provider = $library->publishCommands['provider'] ?? null;

        if (! $provider) {
            return;
        }

        Process::run([
            php_binary(),
            artisan_binary(),
            'vendor:publish',
            '--provider',
            $provider,
        ]);
    }
}
