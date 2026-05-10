<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Contracts;

use Garanaw\LaravelConfigurer\Library;

interface LibraryCommand extends InstallCommand
{
    public function install(Library $library): bool;
}
