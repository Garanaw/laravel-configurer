<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms\Publishers;

use Garanaw\LaravelConfigurer\Contracts\PublisherContract;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Process;
use function Illuminate\Support\artisan_binary;
use function Illuminate\Support\php_binary;
use function Laravel\Prompts\alert;

class ProviderPublisher implements PublisherContract
{
    public function __construct(
        private readonly Application $app,
        private readonly Kernel $artisan,
        private readonly OutputStyle $output,
    ) {}

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

//        if (! class_exists($provider)) {
//            alert(sprintf('The provider class %s does not exist. Skipping publishing for %s.', $provider, $library->name));
//
//            return;
//        }

//        $this->app->register($provider, force: true);
//
//        $params = ['--provider' => $provider];
//
//        $this->artisan->call(
//            command: 'vendor:publish',
//            parameters: $params,
//            outputBuffer: $this->output,
//        );
    }
}
