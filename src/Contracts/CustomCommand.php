<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Contracts;

use Illuminate\Support\Enumerable;

interface CustomCommand extends InstallCommand
{
    public function install(Enumerable $libraries): bool;
}