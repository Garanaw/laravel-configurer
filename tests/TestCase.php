<?php

namespace Garanaw\LaravelConfigurer\Tests;

use Garanaw\LaravelConfigurer\LaravelConfigurerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaravelConfigurerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set(
            'laravel-configurer.some_option',
            true
        );
    }
}
