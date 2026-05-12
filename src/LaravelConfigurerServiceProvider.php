<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer;

use Garanaw\LaravelConfigurer\Console\Commands\Configurer;
use Garanaw\LaravelConfigurer\Contracts\InstallerContract;
use Garanaw\LaravelConfigurer\Mechanisms\RequireMechanism;
use Garanaw\LaravelConfigurer\Pipeline\Pipes\DevRequirerPipe;
use Garanaw\LaravelConfigurer\Pipeline\Pipes\RequirerPipe;
use Illuminate\Console\OutputStyle;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class LaravelConfigurerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/configurer.php', 'configurer');

        $this->app->bind(InstallerContract::class, Installer::class);

        $this->app->when([Configurer::class, 'handle'])
            ->needs(Composer::class)
            ->give(static fn ($app) => new Composer($app['files'], $app->basePath()));

        $this->app->when(Installer::class)
            ->needs(Composer::class)
            ->give(static fn ($app) => new Composer($app['files'], $app->basePath()));

        $this->app->when(RequireMechanism::class)
            ->needs(Composer::class)
            ->give(static fn ($app) => new Composer($app['files'], $app->basePath()));

        $this->app->when([
            RequireMechanism::class,
            RequirerPipe::class,
            DevRequirerPipe::class,
        ])
            ->needs(OutputStyle::class)
            ->give(static fn ($app) => resolve(OutputStyle::class, [
                'input' => new ArgvInput(),
                'output' => new ConsoleOutput(),
            ]));
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->about();

            $this->commands([Configurer::class]);

            $this->publishes([
                __DIR__ . '/../config/configurer.php' => config_path('configurer.php'),
            ], ['configurer', 'configurator', 'laravel-configurer']);
        }
    }

    protected function about(): void
    {
        AboutCommand::add('Configurer', fn () => [
            'Version' => '1.0.0',
        ]);
    }
}
