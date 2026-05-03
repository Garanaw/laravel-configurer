<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer;

use Garanaw\LaravelConfigurer\Console\Commands\Configurer;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class LaravelConfigurerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/configurer.php', 'configurer');

        $this->app->bind(InstallerContract::class, Installer::class);
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