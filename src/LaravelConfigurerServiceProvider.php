<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer;

use Garanaw\LaravelConfigurer\Console\Commands\Configurer;
use Garanaw\LaravelConfigurer\Mechanisms\RequireMechanism;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;

class LaravelConfigurerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/configurer.php', 'configurer');

        $this->app->bind(InstallerContract::class, Installer::class);

        $this->app->when([Configurer::class, 'handle'])
            ->needs(Composer::class)
            ->give(static fn ($app) => new Composer($app['files'], $app->basePath()));

        $this->app->when(Installer::class
            ->needs(Composer::class)
            ->give(static fn ($app) => new Composer($app['files'], $app->basePath())));

        $this->app->when(RequireMechanism::class)
            ->needs(Composer::class)
            ->give(static fn ($app) => new Composer($app['files'], $app->basePath()));
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